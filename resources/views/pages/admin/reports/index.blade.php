@extends('layouts.app')

@section('title', __('common.manage_reports') . ' - Admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12 d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">{{ __('common.manage_reports') }}</h4>
            <div class="d-flex align-items-center">
                <!-- Export Buttons -->
                <div class="btn-group mr-3">
                    <a href="{{ route('admin.reports.export', ['format' => 'excel', 'period' => $period]) }}" 
                       class="btn btn-success btn-sm">
                        <i class="las la-file-excel"></i> {{ __('common.export_excel') }}
                    </a>
                    <a href="{{ route('admin.reports.export', ['format' => 'pdf', 'period' => $period]) }}" 
                       class="btn btn-danger btn-sm">
                        <i class="las la-file-pdf"></i> {{ __('common.export_pdf') }}
                    </a>
                </div>
                
                <!-- Period Filter -->
                <form method="GET" action="{{ route('admin.reports.index') }}" class="d-inline">
                    <select name="period" class="form-control form-control-sm d-inline-block" style="width: auto;" onchange="this.form.submit()">
                        <option value="day" {{ $period === 'day' ? 'selected' : '' }}>{{ __('common.period_day') }}</option>
                        <option value="week" {{ $period === 'week' ? 'selected' : '' }}>{{ __('common.period_week') }}</option>
                        <option value="month" {{ $period === 'month' ? 'selected' : '' }}>{{ __('common.period_month') }}</option>
                        <option value="year" {{ $period === 'year' ? 'selected' : '' }}>{{ __('common.period_year') }}</option>
                        <option value="all" {{ $period === 'all' ? 'selected' : '' }}>{{ __('common.period_all') }}</option>
                    </select>
                </form>
            </div>
        </div>

        <!-- Overview Statistics -->
        <div class="col-lg-12 mb-3">
            <div class="card card-block card-stretch card-height">
                <div class="card-header">
                    <h4 class="card-title">{{ __('common.overview_statistics') }}</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="text-center p-3 border rounded">
                                <h6 class="text-muted mb-2">{{ __('common.total_users') }}</h6>
                                <h3 class="mb-0 text-primary">{{ number_format($overview['total_users']) }}</h3>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="text-center p-3 border rounded">
                                <h6 class="text-muted mb-2">{{ __('common.total_files') }}</h6>
                                <h3 class="mb-0 text-info">{{ number_format($overview['total_files']) }}</h3>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="text-center p-3 border rounded">
                                <h6 class="text-muted mb-2">{{ __('common.total_folders') }}</h6>
                                <h3 class="mb-0 text-success">{{ number_format($overview['total_folders']) }}</h3>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="text-center p-3 border rounded">
                                <h6 class="text-muted mb-2">{{ __('common.total_storage_used') }}</h6>
                                <h3 class="mb-0 text-warning">{{ number_format($overview['total_storage'] / 1024 / 1024 / 1024, 2) }} GB</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Statistics -->
        <div class="col-lg-6 mb-3">
            <div class="card card-block card-stretch card-height">
                <div class="card-header">
                    <h4 class="card-title">{{ __('common.user_statistics') }}</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('common.active_users_count') }}</span>
                            <strong>{{ number_format($overview['active_users']) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('common.new_users_count') }}</span>
                            <strong>{{ number_format($userStats['new_users']) }}</strong>
                        </div>
                    </div>
                    <h6 class="mb-3">{{ __('common.top_users_by_storage') }}</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>{{ __('common.name') }}</th>
                                    <th>{{ __('common.total_files') }}</th>
                                    <th class="text-right">{{ __('common.storage_used') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($userStats['top_users_by_storage'] as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ number_format($user->files_count) }}</td>
                                    <td class="text-right">{{ number_format($user->storage_used / 1024 / 1024, 2) }} MB</td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center text-muted">{{ __('common.no_users_found') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- File Statistics -->
        <div class="col-lg-6 mb-3">
            <div class="card card-block card-stretch card-height">
                <div class="card-header">
                    <h4 class="card-title">{{ __('common.file_statistics') }}</h4>
                </div>
                <div class="card-body">
                    <h6 class="mb-3">{{ __('common.files_by_type') }}</h6>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('common.images') }}</span>
                            <strong>{{ number_format($fileStats['by_type']['images']) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('common.videos') }}</span>
                            <strong>{{ number_format($fileStats['by_type']['videos']) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('common.audio') }}</span>
                            <strong>{{ number_format($fileStats['by_type']['audio']) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>PDF</span>
                            <strong>{{ number_format($fileStats['by_type']['pdf']) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('common.documents') }}</span>
                            <strong>{{ number_format($fileStats['by_type']['documents']) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('common.sheets') }}</span>
                            <strong>{{ number_format($fileStats['by_type']['spreadsheets']) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>{{ __('common.others') }}</span>
                            <strong>{{ number_format($fileStats['by_type']['others']) }}</strong>
                        </div>
                    </div>
                    <h6 class="mb-3">{{ __('common.files_by_size') }}</h6>
                    <div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('common.size_small') }}</span>
                            <strong>{{ number_format($fileStats['by_size']['small']) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('common.size_medium') }}</span>
                            <strong>{{ number_format($fileStats['by_size']['medium']) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('common.size_large') }}</span>
                            <strong>{{ number_format($fileStats['by_size']['large']) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>{{ __('common.size_xlarge') }}</span>
                            <strong>{{ number_format($fileStats['by_size']['xlarge']) }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Storage Statistics -->
        <div class="col-lg-6 mb-3">
            <div class="card card-block card-stretch card-height">
                <div class="card-header">
                    <h4 class="card-title">{{ __('common.storage_statistics') }}</h4>
                </div>
                <div class="card-body">
                    <h6 class="mb-3">{{ __('common.storage_by_type') }}</h6>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('common.images') }}</span>
                            <strong>{{ number_format($storageStats['by_type']['images'] / 1024 / 1024, 2) }} MB</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('common.videos') }}</span>
                            <strong>{{ number_format($storageStats['by_type']['videos'] / 1024 / 1024, 2) }} MB</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('common.audio') }}</span>
                            <strong>{{ number_format($storageStats['by_type']['audio'] / 1024 / 1024, 2) }} MB</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>PDF</span>
                            <strong>{{ number_format($storageStats['by_type']['pdf'] / 1024 / 1024, 2) }} MB</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('common.documents') }}</span>
                            <strong>{{ number_format($storageStats['by_type']['documents'] / 1024 / 1024, 2) }} MB</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('common.sheets') }}</span>
                            <strong>{{ number_format($storageStats['by_type']['spreadsheets'] / 1024 / 1024, 2) }} MB</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>{{ __('common.others') }}</span>
                            <strong>{{ number_format($storageStats['by_type']['others'] / 1024 / 1024, 2) }} MB</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Statistics -->
        <div class="col-lg-6 mb-3">
            <div class="card card-block card-stretch card-height">
                <div class="card-header">
                    <h4 class="card-title">{{ __('common.activity_statistics') }}</h4>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <span>{{ __('common.files_uploaded') }}</span>
                        <strong class="text-primary">{{ number_format($activityStats['files_uploaded']) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>{{ __('common.folders_created') }}</span>
                        <strong class="text-success">{{ number_format($activityStats['folders_created']) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>{{ __('common.files_shared') }}</span>
                        <strong class="text-info">{{ number_format($activityStats['files_shared']) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>{{ __('common.folders_shared') }}</span>
                        <strong class="text-info">{{ number_format($activityStats['folders_shared']) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>{{ __('common.groups_created') }}</span>
                        <strong class="text-warning">{{ number_format($activityStats['groups_created']) }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Time Series Chart -->
        <div class="col-lg-12 mb-3">
            <div class="card card-block card-stretch card-height">
                <div class="card-header">
                    <h4 class="card-title">{{ __('common.time_series_chart') }}</h4>
                </div>
                <div class="card-body">
                    <canvas id="timeSeriesChart" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('timeSeriesChart');
    if (ctx) {
        const timeSeriesData = @json($timeSeries);
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: timeSeriesData.map(item => item.label),
                datasets: [
                    {
                        label: '{{ __('common.files') }}',
                        data: timeSeriesData.map(item => item.files),
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.1
                    },
                    {
                        label: '{{ __('common.folders') }}',
                        data: timeSeriesData.map(item => item.folders),
                        borderColor: 'rgb(255, 99, 132)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        tension: 0.1
                    },
                    {
                        label: '{{ __('common.shares') }}',
                        data: timeSeriesData.map(item => item.shares),
                        borderColor: 'rgb(255, 206, 86)',
                        backgroundColor: 'rgba(255, 206, 86, 0.2)',
                        tension: 0.1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
});
</script>
@endpush

