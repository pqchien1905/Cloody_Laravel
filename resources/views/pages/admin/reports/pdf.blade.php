<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Báo cáo CloudBox</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 10px;
            border-bottom: 3px solid #4472C4;
        }
        .header h1 {
            color: #4472C4;
            font-size: 24px;
            margin-bottom: 10px;
        }
        .info {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f5f5f5;
        }
        .section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .section-title {
            background-color: #4472C4;
            color: white;
            padding: 8px 12px;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table th {
            background-color: #D9E1F2;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #ccc;
        }
        table td {
            padding: 6px 8px;
            border: 1px solid #ddd;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .stats-item {
            display: table-row;
        }
        .stats-label {
            display: table-cell;
            padding: 8px;
            font-weight: bold;
            border-bottom: 1px solid #eee;
            width: 60%;
        }
        .stats-value {
            display: table-cell;
            padding: 8px;
            text-align: right;
            border-bottom: 1px solid #eee;
            color: #4472C4;
            font-weight: bold;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>BÁO CÁO THỐNG KÊ CLOUDBOX</h1>
    </div>

    <!-- Info -->
    <div class="info">
        <strong>Ngày xuất:</strong> {{ $exportDate }}<br>
        <strong>Kỳ báo cáo:</strong> {{ $period === 'day' ? 'Hôm nay' : ($period === 'week' ? 'Tuần này' : ($period === 'month' ? 'Tháng này' : ($period === 'year' ? 'Năm này' : 'Tất cả thời gian'))) }}
    </div>

    <!-- Overview Statistics -->
    <div class="section">
        <div class="section-title">THỐNG KÊ TỔNG QUAN</div>
        <div class="stats-grid">
            <div class="stats-item">
                <div class="stats-label">Tổng số người dùng</div>
                <div class="stats-value">{{ number_format($overview['total_users']) }}</div>
            </div>
            <div class="stats-item">
                <div class="stats-label">Tổng số file</div>
                <div class="stats-value">{{ number_format($overview['total_files']) }}</div>
            </div>
            <div class="stats-item">
                <div class="stats-label">Tổng số folder</div>
                <div class="stats-value">{{ number_format($overview['total_folders']) }}</div>
            </div>
            <div class="stats-item">
                <div class="stats-label">Tổng số nhóm</div>
                <div class="stats-value">{{ number_format($overview['total_groups']) }}</div>
            </div>
            <div class="stats-item">
                <div class="stats-label">Tổng số chia sẻ</div>
                <div class="stats-value">{{ number_format($overview['total_shares']) }}</div>
            </div>
            <div class="stats-item">
                <div class="stats-label">Dung lượng sử dụng</div>
                <div class="stats-value">{{ number_format($overview['total_storage'] / 1024 / 1024 / 1024, 2) }} GB</div>
            </div>
            <div class="stats-item">
                <div class="stats-label">Người dùng hoạt động</div>
                <div class="stats-value">{{ number_format($overview['active_users']) }}</div>
            </div>
        </div>
    </div>

    <!-- File Statistics -->
    <div class="section">
        <div class="section-title">THỐNG KÊ FILE THEO LOẠI</div>
        <table>
            <thead>
                <tr>
                    <th>Loại file</th>
                    <th style="text-align: right;">Số lượng</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Hình ảnh</td>
                    <td style="text-align: right;">{{ number_format($fileStats['by_type']['images']) }}</td>
                </tr>
                <tr>
                    <td>Video</td>
                    <td style="text-align: right;">{{ number_format($fileStats['by_type']['videos']) }}</td>
                </tr>
                <tr>
                    <td>Audio</td>
                    <td style="text-align: right;">{{ number_format($fileStats['by_type']['audio']) }}</td>
                </tr>
                <tr>
                    <td>PDF</td>
                    <td style="text-align: right;">{{ number_format($fileStats['by_type']['pdf']) }}</td>
                </tr>
                <tr>
                    <td>Tài liệu</td>
                    <td style="text-align: right;">{{ number_format($fileStats['by_type']['documents']) }}</td>
                </tr>
                <tr>
                    <td>Bảng tính</td>
                    <td style="text-align: right;">{{ number_format($fileStats['by_type']['spreadsheets']) }}</td>
                </tr>
                <tr>
                    <td>Khác</td>
                    <td style="text-align: right;">{{ number_format($fileStats['by_type']['others']) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- File Size Statistics -->
    <div class="section">
        <div class="section-title">THỐNG KÊ FILE THEO KÍCH THƯỚC</div>
        <table>
            <thead>
                <tr>
                    <th>Kích thước</th>
                    <th style="text-align: right;">Số lượng</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Nhỏ (&lt; 1MB)</td>
                    <td style="text-align: right;">{{ number_format($fileStats['by_size']['small']) }}</td>
                </tr>
                <tr>
                    <td>Trung bình (1MB - 10MB)</td>
                    <td style="text-align: right;">{{ number_format($fileStats['by_size']['medium']) }}</td>
                </tr>
                <tr>
                    <td>Lớn (10MB - 100MB)</td>
                    <td style="text-align: right;">{{ number_format($fileStats['by_size']['large']) }}</td>
                </tr>
                <tr>
                    <td>Rất lớn (&gt; 100MB)</td>
                    <td style="text-align: right;">{{ number_format($fileStats['by_size']['xlarge']) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Activity Statistics -->
    <div class="section">
        <div class="section-title">THỐNG KÊ HOẠT ĐỘNG</div>
        <div class="stats-grid">
            <div class="stats-item">
                <div class="stats-label">File đã tải lên</div>
                <div class="stats-value">{{ number_format($activityStats['files_uploaded']) }}</div>
            </div>
            <div class="stats-item">
                <div class="stats-label">Folder đã tạo</div>
                <div class="stats-value">{{ number_format($activityStats['folders_created']) }}</div>
            </div>
            <div class="stats-item">
                <div class="stats-label">File đã chia sẻ</div>
                <div class="stats-value">{{ number_format($activityStats['files_shared']) }}</div>
            </div>
            <div class="stats-item">
                <div class="stats-label">Folder đã chia sẻ</div>
                <div class="stats-value">{{ number_format($activityStats['folders_shared']) }}</div>
            </div>
            <div class="stats-item">
                <div class="stats-label">Nhóm đã tạo</div>
                <div class="stats-value">{{ number_format($activityStats['groups_created']) }}</div>
            </div>
        </div>
    </div>

    <!-- Top Users -->
    <div class="section">
        <div class="section-title">TOP 10 NGƯỜI DÙNG THEO DUNG LƯỢNG</div>
        <table>
            <thead>
                <tr>
                    <th>Tên người dùng</th>
                    <th style="text-align: right;">Số file</th>
                    <th style="text-align: right;">Dung lượng</th>
                </tr>
            </thead>
            <tbody>
                @forelse($userStats['top_users_by_storage'] as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td style="text-align: right;">{{ number_format($user->files_count) }}</td>
                        <td style="text-align: right;">{{ number_format($user->storage_used / 1024 / 1024, 2) }} MB</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align: center;">Không có dữ liệu</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>CloudBox - Hệ thống quản lý và chia sẻ file | Xuất lúc {{ $exportDate }}</p>
    </div>
</body>
</html>
