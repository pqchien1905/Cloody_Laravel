<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\File;
use App\Models\Folder;
use App\Models\User;
use App\Models\Group;
use App\Models\FileShare;
use App\Models\FolderShare;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class AdminReportsController extends Controller
{
    /**
     * Display reports and statistics dashboard.
     */
    public function index(Request $request)
    {
        $period = $request->get('period', 'month'); // day, week, month, year, all

        // Tính toán khoảng thời gian
        $dateRange = $this->getDateRange($period);

        // Thống kê tổng quan
        $overview = [
            'total_users' => User::count(),
            'total_files' => File::count(),
            'total_folders' => Folder::count(),
            'total_groups' => Group::count(),
            'total_shares' => FileShare::count() + FolderShare::count(),
            'total_storage' => File::sum('size'),
            'active_users' => User::whereHas('files')->orWhereHas('folders')->count(),
        ];

        // Thống kê người dùng
        $userStats = $this->getUserStatistics($dateRange);

        // Thống kê file
        $fileStats = $this->getFileStatistics($dateRange);

        // Thống kê lưu trữ
        $storageStats = $this->getStorageStatistics();

        // Thống kê hoạt động
        $activityStats = $this->getActivityStatistics($dateRange);

        // Thống kê theo thời gian (cho biểu đồ)
        $timeSeries = $this->getTimeSeriesStatistics($dateRange);

        return view('pages.admin.reports.index', compact(
            'overview',
            'userStats',
            'fileStats',
            'storageStats',
            'activityStats',
            'timeSeries',
            'period',
            'dateRange'
        ));
    }

    /**
     * Get date range based on period.
     */
    private function getDateRange($period)
    {
        switch ($period) {
            case 'day':
                return [
                    'start' => now()->startOfDay(),
                    'end' => now()->endOfDay(),
                ];
            case 'week':
                return [
                    'start' => now()->startOfWeek(),
                    'end' => now()->endOfWeek(),
                ];
            case 'month':
                return [
                    'start' => now()->startOfMonth(),
                    'end' => now()->endOfMonth(),
                ];
            case 'year':
                return [
                    'start' => now()->startOfYear(),
                    'end' => now()->endOfYear(),
                ];
            default: // all
                return [
                    'start' => null,
                    'end' => null,
                ];
        }
    }

    /**
     * Get user statistics.
     */
    private function getUserStatistics($dateRange)
    {
        $query = User::query();
        
        if ($dateRange['start']) {
            $query->where('created_at', '>=', $dateRange['start'])
                  ->where('created_at', '<=', $dateRange['end']);
        }

        $users = User::withCount(['files' => function($q) {
                $q->where('is_trash', false);
            }])
            ->withCount(['folders' => function($q) {
                $q->where('is_trash', false);
            }])
            ->get()
            ->map(function($user) {
                $user->storage_used = File::where('user_id', $user->id)
                    ->where('is_trash', false)
                    ->sum('size');
                return $user;
            })
            ->sortByDesc('storage_used')
            ->take(10);

        return [
            'top_users_by_storage' => $users,
            'new_users' => $query->count(),
            'total_users' => User::count(),
        ];
    }

    /**
     * Get file statistics.
     */
    private function getFileStatistics($dateRange)
    {
        $query = File::query();
        
        if ($dateRange['start']) {
            $query->where('created_at', '>=', $dateRange['start'])
                  ->where('created_at', '<=', $dateRange['end']);
        }

        return [
            'total' => $query->where('is_trash', false)->count(),
            'by_type' => [
                'images' => File::where('mime_type', 'like', 'image%')->where('is_trash', false)->count(),
                'videos' => File::where('mime_type', 'like', 'video%')->where('is_trash', false)->count(),
                'audio' => File::where('mime_type', 'like', 'audio%')->where('is_trash', false)->count(),
                'pdf' => File::where('mime_type', 'like', '%pdf%')->where('is_trash', false)->count(),
                'documents' => File::where(function($q) {
                    $q->where('mime_type', 'like', '%word%')
                      ->orWhere('mime_type', 'like', '%officedocument%');
                })->where('is_trash', false)->count(),
                'spreadsheets' => File::where(function($q) {
                    $q->where('mime_type', 'like', '%excel%')
                      ->orWhere('mime_type', 'like', '%spreadsheet%');
                })->where('is_trash', false)->count(),
                'others' => File::where('is_trash', false)
                    ->where('mime_type', 'not like', 'image%')
                    ->where('mime_type', 'not like', 'video%')
                    ->where('mime_type', 'not like', 'audio%')
                    ->where('mime_type', 'not like', '%pdf%')
                    ->where(function($q) {
                        $q->where('mime_type', 'not like', '%word%')
                          ->where('mime_type', 'not like', '%officedocument%');
                    })
                    ->where(function($q) {
                        $q->where('mime_type', 'not like', '%excel%')
                          ->where('mime_type', 'not like', '%spreadsheet%');
                    })
                    ->count(),
            ],
            'by_size' => [
                'small' => File::where('size', '<', 1024 * 1024)->where('is_trash', false)->count(), // < 1MB
                'medium' => File::where('size', '>=', 1024 * 1024)
                    ->where('size', '<', 10 * 1024 * 1024)
                    ->where('is_trash', false)->count(), // 1MB - 10MB
                'large' => File::where('size', '>=', 10 * 1024 * 1024)
                    ->where('size', '<', 100 * 1024 * 1024)
                    ->where('is_trash', false)->count(), // 10MB - 100MB
                'xlarge' => File::where('size', '>=', 100 * 1024 * 1024)->where('is_trash', false)->count(), // > 100MB
            ],
        ];
    }

    /**
     * Get storage statistics.
     */
    private function getStorageStatistics()
    {
        $totalStorage = File::where('is_trash', false)->sum('size');
        
        $storageByUser = User::with(['files' => function($q) {
                $q->where('is_trash', false);
            }])
            ->get()
            ->map(function($user) {
                $user->storage_used = $user->files->sum('size');
                return $user;
            })
            ->sortByDesc('storage_used')
            ->take(10);

        $storageByType = [
            'images' => File::where('mime_type', 'like', 'image%')->where('is_trash', false)->sum('size'),
            'videos' => File::where('mime_type', 'like', 'video%')->where('is_trash', false)->sum('size'),
            'audio' => File::where('mime_type', 'like', 'audio%')->where('is_trash', false)->sum('size'),
            'pdf' => File::where('mime_type', 'like', '%pdf%')->where('is_trash', false)->sum('size'),
            'documents' => File::where(function($q) {
                $q->where('mime_type', 'like', '%word%')
                  ->orWhere('mime_type', 'like', '%officedocument%');
            })->where('is_trash', false)->sum('size'),
            'spreadsheets' => File::where(function($q) {
                $q->where('mime_type', 'like', '%excel%')
                  ->orWhere('mime_type', 'like', '%spreadsheet%');
            })->where('is_trash', false)->sum('size'),
            'others' => $totalStorage - 
                File::where('mime_type', 'like', 'image%')->where('is_trash', false)->sum('size') -
                File::where('mime_type', 'like', 'video%')->where('is_trash', false)->sum('size') -
                File::where('mime_type', 'like', 'audio%')->where('is_trash', false)->sum('size') -
                File::where('mime_type', 'like', '%pdf%')->where('is_trash', false)->sum('size') -
                File::where(function($q) {
                    $q->where('mime_type', 'like', '%word%')
                      ->orWhere('mime_type', 'like', '%officedocument%');
                })->where('is_trash', false)->sum('size') -
                File::where(function($q) {
                    $q->where('mime_type', 'like', '%excel%')
                      ->orWhere('mime_type', 'like', '%spreadsheet%');
                })->where('is_trash', false)->sum('size'),
        ];

        return [
            'total' => $totalStorage,
            'by_user' => $storageByUser,
            'by_type' => $storageByType,
        ];
    }

    /**
     * Get activity statistics.
     */
    private function getActivityStatistics($dateRange)
    {
        $fileQuery = File::query();
        $folderQuery = Folder::query();
        $fileShareQuery = FileShare::query();
        $folderShareQuery = FolderShare::query();
        $groupQuery = Group::query();

        if ($dateRange['start']) {
            $fileQuery->where('created_at', '>=', $dateRange['start'])
                      ->where('created_at', '<=', $dateRange['end']);
            $folderQuery->where('created_at', '>=', $dateRange['start'])
                        ->where('created_at', '<=', $dateRange['end']);
            $fileShareQuery->where('created_at', '>=', $dateRange['start'])
                           ->where('created_at', '<=', $dateRange['end']);
            $folderShareQuery->where('created_at', '>=', $dateRange['start'])
                             ->where('created_at', '<=', $dateRange['end']);
            $groupQuery->where('created_at', '>=', $dateRange['start'])
                       ->where('created_at', '<=', $dateRange['end']);
        }

        return [
            'files_uploaded' => $fileQuery->where('is_trash', false)->count(),
            'folders_created' => $folderQuery->where('is_trash', false)->count(),
            'files_shared' => $fileShareQuery->count(),
            'folders_shared' => $folderShareQuery->count(),
            'groups_created' => $groupQuery->count(),
        ];
    }

    /**
     * Get time series statistics for charts.
     */
    private function getTimeSeriesStatistics($dateRange)
    {
        $days = 30; // Last 30 days
        $data = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->startOfDay();
            $data[] = [
                'date' => $date->format('Y-m-d'),
                'label' => $date->format('d/m'),
                'files' => File::whereDate('created_at', $date)->where('is_trash', false)->count(),
                'folders' => Folder::whereDate('created_at', $date)->where('is_trash', false)->count(),
                'shares' => FileShare::whereDate('created_at', $date)->count() + 
                           FolderShare::whereDate('created_at', $date)->count(),
            ];
        }

        return $data;
    }

    /**
     * Export reports to Excel or PDF.
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'excel'); // excel or pdf
        $period = $request->get('period', 'month');

        // Lấy dữ liệu báo cáo
        $dateRange = $this->getDateRange($period);
        
        $data = [
            'overview' => [
                'total_users' => User::count(),
                'total_files' => File::count(),
                'total_folders' => Folder::count(),
                'total_groups' => Group::count(),
                'total_shares' => FileShare::count() + FolderShare::count(),
                'total_storage' => File::sum('size'),
                'active_users' => User::whereHas('files')->orWhereHas('folders')->count(),
            ],
            'userStats' => $this->getUserStatistics($dateRange),
            'fileStats' => $this->getFileStatistics($dateRange),
            'storageStats' => $this->getStorageStatistics(),
            'activityStats' => $this->getActivityStatistics($dateRange),
            'period' => $period,
            'dateRange' => $dateRange,
            'exportDate' => now()->format('d/m/Y H:i'),
        ];

        if ($format === 'pdf') {
            return $this->exportToPDF($data);
        } else {
            return $this->exportToExcel($data);
        }
    }

    /**
     * Export report to Excel.
     */
    private function exportToExcel($data)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Báo cáo CloudBox');

        // Header styling
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ];

        $subHeaderStyle = [
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D9E1F2']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ];

        // Title
        $sheet->setCellValue('A1', 'BÁO CÁO THỐNG KÊ CLOUDBOX');
        $sheet->mergeCells('A1:D1');
        $sheet->getStyle('A1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Export info
        $row = 2;
        $sheet->setCellValue("A{$row}", 'Ngày xuất: ' . $data['exportDate']);
        $sheet->setCellValue("A" . ($row + 1), 'Kỳ báo cáo: ' . $this->getPeriodLabel($data['period']));
        $row += 3;

        // Overview Statistics
        $sheet->setCellValue("A{$row}", 'THỐNG KÊ TỔNG QUAN');
        $sheet->mergeCells("A{$row}:D{$row}");
        $sheet->getStyle("A{$row}")->applyFromArray($subHeaderStyle);
        $row++;

        $overviewData = [
            ['Tổng số người dùng', number_format($data['overview']['total_users'])],
            ['Tổng số file', number_format($data['overview']['total_files'])],
            ['Tổng số folder', number_format($data['overview']['total_folders'])],
            ['Tổng số nhóm', number_format($data['overview']['total_groups'])],
            ['Tổng số chia sẻ', number_format($data['overview']['total_shares'])],
            ['Dung lượng sử dụng', number_format($data['overview']['total_storage'] / 1024 / 1024 / 1024, 2) . ' GB'],
            ['Người dùng hoạt động', number_format($data['overview']['active_users'])],
        ];

        foreach ($overviewData as $item) {
            $sheet->setCellValue("A{$row}", $item[0]);
            $sheet->setCellValue("B{$row}", $item[1]);
            $row++;
        }
        $row++;

        // File Statistics
        $sheet->setCellValue("A{$row}", 'THỐNG KÊ FILE');
        $sheet->mergeCells("A{$row}:D{$row}");
        $sheet->getStyle("A{$row}")->applyFromArray($subHeaderStyle);
        $row++;

        $sheet->setCellValue("A{$row}", 'Loại file');
        $sheet->setCellValue("B{$row}", 'Số lượng');
        $sheet->getStyle("A{$row}:B{$row}")->applyFromArray($headerStyle);
        $row++;

        $fileTypes = [
            ['Hình ảnh', $data['fileStats']['by_type']['images']],
            ['Video', $data['fileStats']['by_type']['videos']],
            ['Audio', $data['fileStats']['by_type']['audio']],
            ['PDF', $data['fileStats']['by_type']['pdf']],
            ['Tài liệu', $data['fileStats']['by_type']['documents']],
            ['Bảng tính', $data['fileStats']['by_type']['spreadsheets']],
            ['Khác', $data['fileStats']['by_type']['others']],
        ];

        foreach ($fileTypes as $type) {
            $sheet->setCellValue("A{$row}", $type[0]);
            $sheet->setCellValue("B{$row}", number_format($type[1]));
            $row++;
        }
        $row++;

        // Activity Statistics
        $sheet->setCellValue("A{$row}", 'THỐNG KÊ HOẠT ĐỘNG');
        $sheet->mergeCells("A{$row}:D{$row}");
        $sheet->getStyle("A{$row}")->applyFromArray($subHeaderStyle);
        $row++;

        $activityData = [
            ['File đã tải lên', number_format($data['activityStats']['files_uploaded'])],
            ['Folder đã tạo', number_format($data['activityStats']['folders_created'])],
            ['File đã chia sẻ', number_format($data['activityStats']['files_shared'])],
            ['Folder đã chia sẻ', number_format($data['activityStats']['folders_shared'])],
            ['Nhóm đã tạo', number_format($data['activityStats']['groups_created'])],
        ];

        foreach ($activityData as $item) {
            $sheet->setCellValue("A{$row}", $item[0]);
            $sheet->setCellValue("B{$row}", $item[1]);
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Generate file
        $filename = 'CloudBox_Report_' . date('Y-m-d_His') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    /**
     * Export report to PDF.
     */
    private function exportToPDF($data)
    {
        $pdf = PDF::loadView('pages.admin.reports.pdf', $data);
        $filename = 'CloudBox_Report_' . date('Y-m-d_His') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Get period label.
     */
    private function getPeriodLabel($period)
    {
        $labels = [
            'day' => 'Hôm nay',
            'week' => 'Tuần này',
            'month' => 'Tháng này',
            'year' => 'Năm này',
            'all' => 'Tất cả thời gian',
        ];

        return $labels[$period] ?? 'Không xác định';
    }
}

