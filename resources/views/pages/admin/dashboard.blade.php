@extends('layouts.app')

@section('title', __('common.admin_dashboard_title') . ' - Cloody')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12 mb-3">
            <div class="card-transparent card-block card-stretch card-height">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ __('common.admin_dashboard_title') }}</h4>
                    <div class="dashboard1-dropdown d-flex align-items-center">
                        <div class="dashboard1-info">
                            <a href="#calander" class="collapsed" data-toggle="collapse" aria-expanded="false">
                                <i class="ri-arrow-down-s-line"></i>
                            </a>
                            <ul id="calander" class="iq-dropdown collapse list-inline m-0 p-0 mt-2">
                                <li class="mb-2">
                                    <a href="#" data-toggle="tooltip" data-placement="right" title="Calendar"><i class="las la-calendar iq-arrow-left"></i></a>
                                </li>
                                <li class="mb-2">
                                    <a href="#" data-toggle="tooltip" data-placement="right" title="Keep"><i class="las la-lightbulb iq-arrow-left"></i></a>
                                </li>
                                <li>
                                    <a href="#" data-toggle="tooltip" data-placement="right" title="Tasks"><i class="las la-tasks iq-arrow-left"></i></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Stats -->
        <div class="col-md-6 col-xl-3">
            <div class="card card-block card-stretch card-height">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="mb-2 text-muted">{{ __('common.total_users') }}</h6>
                            <h3 class="mb-0">{{ $totalUsers }}</h3>
                        </div>
                        <div class="icon-small bg-primary-light rounded p-2">
                            <i class="las la-users text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card card-block card-stretch card-height">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="mb-2 text-muted">{{ __('common.total_files') }}</h6>
                            <h3 class="mb-0">{{ $totalFiles }}</h3>
                        </div>
                        <div class="icon-small bg-info-light rounded p-2">
                            <i class="las la-file text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card card-block card-stretch card-height">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="mb-2 text-muted">{{ __('common.total_folders') }}</h6>
                            <h3 class="mb-0">{{ $totalFolders }}</h3>
                        </div>
                        <div class="icon-small bg-warning-light rounded p-2">
                            <i class="las la-folder text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card card-block card-stretch card-height">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="mb-2 text-muted">{{ __('common.total_shares') }}</h6>
                            <h3 class="mb-0">{{ $totalFileShares + $totalFolderShares }}</h3>
                        </div>
                        <div class="icon-small bg-success-light rounded p-2">
                            <i class="las la-share-alt text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Storage Overview -->
        <div class="col-lg-6">
            <div class="card card-block card-stretch card-height">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">{{ __('common.storage_usage') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <h2 class="mb-3">{{ number_format($storageUsed / 1024 / 1024 / 1024, 2) }} GB</h2>
                        <p class="mb-4 text-muted">{{ __('common.of_used') }} {{ $storageLimitGB ?? ($totalUsers * 1) }} GB</p>
                        <div class="iq-progress-bar mb-3">
                            @php($storageLimit = $storageLimitGB ?? ($totalUsers * 1))
                            @php($percent = $storageLimit > 0 ? min(($storageUsed / 1024 / 1024 / 1024) / $storageLimit * 100, 100) : 0)
                            <span class="bg-primary iq-progress progress-1 admin-storage-bar" data-percent="{{ number_format($percent, 2) }}" style="width: 0%; transition: width 1s ease;"></span>
                        </div>
                        <p class="mb-0">~ {{ number_format(max($storageLimit - ($storageUsed / 1024 / 1024 / 1024), 0), 2) }} GB {{ __('common.free') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- File Type Breakdown Chart -->
        <div class="col-lg-6">
            <div class="card card-block card-stretch card-height">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">{{ __('common.files_by_type') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div id="admin-file-type-chart" style="min-height: 260px;"></div>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="col-lg-6">
            <div class="card card-block card-stretch card-height">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">{{ __('common.quick_links') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <a href="{{ route('cloody.user.list') }}" class="btn btn-outline-primary btn-block"><i class="las la-th-list mr-2"></i>{{ __('common.manage_users') }}</a>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <a href="{{ route('cloody.files') }}" class="btn btn-outline-secondary btn-block"><i class="las la-file mr-2"></i>{{ __('common.all_files') }}</a>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <a href="{{ route('cloody.folders.index') }}" class="btn btn-outline-info btn-block"><i class="las la-folder mr-2"></i>{{ __('common.all_folders') }}</a>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <a href="{{ route('cloody.shared') }}" class="btn btn-outline-success btn-block"><i class="las la-share-alt mr-2"></i>{{ __('common.shared') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        const bar = document.querySelector('.admin-storage-bar');
        if (bar) {
            const percent = parseFloat(bar.getAttribute('data-percent')) || 0;
            bar.style.width = percent + '%';
        }
    }, 100);

    // Render file type chart (requires ApexCharts via template bundle)
    try {
        if (window.ApexCharts) {
            const options = {
                chart: { type: 'donut', height: 260 },
                labels: ['{{ __('common.images') }}', '{{ __('common.videos') }}', '{{ __('common.audio') }}', 'PDF', 'Docs', '{{ __('common.sheets') }}', '{{ __('common.others') }}'],
                series: [
                    {{ $byType['images'] ?? 0 }},
                    {{ $byType['videos'] ?? 0 }},
                    {{ $byType['audio'] ?? 0 }},
                    {{ $byType['pdf'] ?? 0 }},
                    {{ $byType['docs'] ?? 0 }},
                    {{ $byType['sheets'] ?? 0 }},
                    {{ $byType['others'] ?? 0 }}
                ],
                dataLabels: { enabled: false },
                legend: { position: 'bottom' },
            };
            const el = document.querySelector('#admin-file-type-chart');
            if (el) {
                const chart = new window.ApexCharts(el, options);
                chart.render();
            }
        }
    } catch (e) { console.warn('Chart init failed', e); }
});
</script>
@endpush
