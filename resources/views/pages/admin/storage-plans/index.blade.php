@extends('layouts.app')

@section('title', 'Quản lý gói lưu trữ - Admin')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="mb-0"><i class="las la-box mr-2"></i>Quản lý gói lưu trữ</h3>
                <div>
                    @if($hasStoragePlansTable)
                    <button type="button" class="btn btn-primary mr-2" data-toggle="modal" data-target="#addPlanModal">
                        <i class="las la-plus mr-1"></i>Thêm gói mới
                    </button>
                    @endif
                    <a href="{{ route('cloody.storage.plans') }}" class="btn btn-outline-primary" target="_blank">
                        <i class="las la-eye mr-1"></i>Xem trang người dùng
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="las la-check-circle mr-2"></i>{{ session('success') }}
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="las la-exclamation-circle mr-2"></i>{{ session('error') }}
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    </div>
    @endif

    @if(!$hasStoragePlansTable)
    <div class="alert alert-warning" role="alert">
        <i class="las la-exclamation-triangle mr-2"></i>
        <strong>Chú ý:</strong> Bạn cần chạy migration để tạo bảng <code>storage_plans</code> trước khi có thể thêm/sửa gói.
        <br>
        <code>php artisan migrate</code>
    </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2">Tổng gói đang hoạt động</h6>
                            <h3 class="mb-0">{{ $totalActiveSubscriptions }}</h3>
                        </div>
                        <div class="icon-small bg-primary-light rounded p-3">
                            <i class="las la-users text-primary" style="font-size: 28px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2">Doanh thu tổng</h6>
                            <h3 class="mb-0">{{ number_format($totalRevenue) }}đ</h3>
                        </div>
                        <div class="icon-small bg-success-light rounded p-3">
                            <i class="las la-dollar-sign text-success" style="font-size: 28px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2">Doanh thu tháng này</h6>
                            <h3 class="mb-0">{{ number_format($monthlyRevenue) }}đ</h3>
                        </div>
                        <div class="icon-small bg-info-light rounded p-3">
                            <i class="las la-calendar text-info" style="font-size: 28px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Plan Statistics -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Thống kê theo gói</h5>
                    @if($hasStoragePlansTable)
                    <span class="badge badge-info">Có thể chỉnh sửa</span>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Tên gói</th>
                                    <th>Dung lượng</th>
                                    <th>Giá tháng</th>
                                    <th>Giá năm</th>
                                    <th>Người dùng</th>
                                    <th>Doanh thu</th>
                                    <th>Trạng thái</th>
                                    @if($hasStoragePlansTable)
                                    <th>Hành động</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($planStats as $stat)
                                <tr>
                                    <td>
                                        <strong>{{ $stat['name'] }}</strong>
                                        @if($stat['plan_id'] === 'basic')
                                        <span class="badge badge-secondary ml-2">Miễn phí</span>
                                        @endif
                                        @if($stat['is_popular'] ?? false)
                                        <span class="badge badge-warning ml-1">Phổ biến</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($stat['storage_gb'] >= 1024)
                                            {{ round($stat['storage_gb'] / 1024, 1) }} TB
                                        @else
                                            {{ $stat['storage_gb'] }} GB
                                        @endif
                                    </td>
                                    <td>{{ number_format($stat['price_monthly']) }}đ</td>
                                    <td>{{ number_format($stat['price_yearly']) }}đ</td>
                                    <td>
                                        <span class="badge badge-primary badge-pill">
                                            {{ $stat['active_users'] }} người
                                        </span>
                                    </td>
                                    <td><strong>{{ number_format($stat['total_revenue']) }}đ</strong></td>
                                    <td>
                                        @if($stat['is_active'] ?? true)
                                        <span class="badge badge-success">Hoạt động</span>
                                        @else
                                        <span class="badge badge-secondary">Vô hiệu</span>
                                        @endif
                                    </td>
                                    @if($hasStoragePlansTable)
                                    <td>
                                        @if($stat['id'])
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-info edit-plan-btn" 
                                                    data-toggle="modal" 
                                                    data-target="#editPlanModal"
                                                    data-id="{{ $stat['id'] }}"
                                                    data-plan-id="{{ $stat['plan_id'] }}"
                                                    data-name="{{ $stat['name'] }}"
                                                    data-storage="{{ $stat['storage_gb'] }}"
                                                    data-price-monthly="{{ $stat['price_monthly'] }}"
                                                    data-price-yearly="{{ $stat['price_yearly'] }}"
                                                    data-features="{{ implode("\n", $stat['features'] ?? []) }}"
                                                    data-is-active="{{ $stat['is_active'] ? '1' : '0' }}"
                                                    data-is-popular="{{ $stat['is_popular'] ? '1' : '0' }}"
                                                    data-sort-order="{{ $stat['sort_order'] ?? 0 }}"
                                                    title="Sửa">
                                                <i class="las la-edit"></i>
                                            </button>
                                            <form action="{{ route('admin.storage-plans.toggle-active', $stat['id']) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-{{ $stat['is_active'] ? 'warning' : 'success' }}" 
                                                        title="{{ $stat['is_active'] ? 'Vô hiệu hóa' : 'Kích hoạt' }}">
                                                    <i class="las la-{{ $stat['is_active'] ? 'pause' : 'play' }}"></i>
                                                </button>
                                            </form>
                                            @if($stat['active_users'] == 0 && $stat['plan_id'] !== 'basic')
                                            <form action="{{ route('admin.storage-plans.destroy', $stat['id']) }}" 
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Bạn có chắc muốn xóa gói này?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" title="Xóa">
                                                    <i class="las la-trash"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Subscriptions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Gói đăng ký gần đây</h5>
                    <span class="badge badge-primary badge-pill">{{ count($recentSubscriptions) }} gói</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Người dùng</th>
                                    <th>Gói</th>
                                    <th>Chu kỳ</th>
                                    <th>Dung lượng</th>
                                    <th>Giá</th>
                                    <th>Trạng thái</th>
                                    <th>Thanh toán</th>
                                    <th>Ngày tạo</th>
                                    <th>Hết hạn</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentSubscriptions as $sub)
                                <tr>
                                    <td>#{{ $sub['id'] }}</td>
                                    <td>
                                        <div>
                                            <strong>{{ $sub['user_name'] }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $sub['user_email'] }}</small>
                                        </div>
                                    </td>
                                    <td><strong>{{ $sub['plan_name'] }}</strong></td>
                                    <td>
                                        <span class="badge badge-{{ $sub['billing_cycle'] === 'yearly' ? 'success' : 'info' }}">
                                            {{ $sub['billing_cycle'] === 'yearly' ? 'Năm' : 'Tháng' }}
                                        </span>
                                    </td>
                                    <td>{{ $sub['storage_gb'] }} GB</td>
                                    <td><strong>{{ number_format($sub['price']) }}đ</strong></td>
                                    <td>
                                        @if($sub['is_active'])
                                        <span class="badge badge-success">
                                            <i class="las la-check-circle"></i> Hoạt động
                                        </span>
                                        @else
                                        <span class="badge badge-secondary">
                                            <i class="las la-times-circle"></i> Đã hủy
                                        </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($sub['payment_status'] === 'paid')
                                        <span class="badge badge-success">Đã thanh toán</span>
                                        @elseif($sub['payment_status'] === 'pending')
                                        <span class="badge badge-warning">Chờ xử lý</span>
                                        @else
                                        <span class="badge badge-danger">Thất bại</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ \Carbon\Carbon::parse($sub['created_at'])->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i') }}</small>
                                    </td>
                                    <td>
                                        @if($sub['expires_at'])
                                        <small>{{ \Carbon\Carbon::parse($sub['expires_at'])->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y') }}</small>
                                        @else
                                        <small class="text-muted">Vô thời hạn</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            @if($sub['is_active'])
                                            <form action="{{ route('admin.storage-plans.deactivate', $sub['id']) }}" 
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Bạn có chắc muốn hủy kích hoạt gói này?')">
                                                @csrf
                                                @method('POST')
                                                <button type="submit" class="btn btn-warning" title="Hủy kích hoạt">
                                                    <i class="las la-pause"></i>
                                                </button>
                                            </form>
                                            @else
                                            <form action="{{ route('admin.storage-plans.activate', $sub['id']) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('POST')
                                                <button type="submit" class="btn btn-success" title="Kích hoạt">
                                                    <i class="las la-play"></i>
                                                </button>
                                            </form>
                                            @endif
                                            <form action="{{ route('admin.storage-plans.delete', $sub['id']) }}" 
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Bạn có chắc muốn xóa gói này?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" title="Xóa">
                                                    <i class="las la-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="11" class="text-center py-5">
                                        <i class="las la-inbox text-muted" style="font-size: 48px;"></i>
                                        <p class="text-muted mb-0">Chưa có gói đăng ký nào</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Thêm gói mới -->
<div class="modal fade" id="addPlanModal" tabindex="-1" role="dialog" aria-labelledby="addPlanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.storage-plans.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addPlanModalLabel">
                        <i class="las la-plus-circle mr-2"></i>Thêm gói lưu trữ mới
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="add_plan_id">ID gói <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="add_plan_id" name="plan_id" 
                                       placeholder="vd: 500gb, pro, enterprise" required
                                       pattern="[a-z0-9_-]+" title="Chỉ chữ thường, số, gạch ngang và gạch dưới">
                                <small class="form-text text-muted">ID duy nhất, chỉ chữ thường và số</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="add_name">Tên hiển thị <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="add_name" name="name" 
                                       placeholder="vd: 500 GB, Pro, Enterprise" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="add_storage_gb">Dung lượng (GB) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="add_storage_gb" name="storage_gb" 
                                       min="1" placeholder="vd: 500" required>
                                <small class="form-text text-muted">1024 GB = 1 TB</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="add_price_monthly">Giá tháng (VNĐ) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="add_price_monthly" name="price_monthly" 
                                       min="0" step="1000" placeholder="vd: 99000" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="add_price_yearly">Giá năm (VNĐ) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="add_price_yearly" name="price_yearly" 
                                       min="0" step="1000" placeholder="vd: 990000" required>
                                <small class="form-text text-muted">
                                    <a href="#" id="calcYearlyPrice">Tính giảm 16%</a>
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="add_features">Tính năng (mỗi dòng 1 tính năng)</label>
                        <textarea class="form-control" id="add_features" name="features" rows="4" 
                                  placeholder="Nhập mỗi tính năng trên một dòng&#10;vd:&#10;500 GB dung lượng lưu trữ&#10;Chia sẻ file không giới hạn&#10;Hỗ trợ ưu tiên"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="add_is_active" name="is_active" value="1" checked>
                                    <label class="custom-control-label" for="add_is_active">Kích hoạt gói</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="add_is_popular" name="is_popular" value="1">
                                    <label class="custom-control-label" for="add_is_popular">Đánh dấu là gói phổ biến</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="las la-save mr-1"></i>Thêm gói
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Sửa gói -->
<div class="modal fade" id="editPlanModal" tabindex="-1" role="dialog" aria-labelledby="editPlanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="editPlanForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editPlanModalLabel">
                        <i class="las la-edit mr-2"></i>Sửa gói lưu trữ
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>ID gói</label>
                                <input type="text" class="form-control" id="edit_plan_id_display" readonly disabled>
                                <small class="form-text text-muted">ID gói không thể thay đổi</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_name">Tên hiển thị <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_name" name="name" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_storage_gb">Dung lượng (GB) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="edit_storage_gb" name="storage_gb" min="1" required>
                                <small class="form-text text-muted">1024 GB = 1 TB</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_price_monthly">Giá tháng (VNĐ) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="edit_price_monthly" name="price_monthly" min="0" step="1000" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_price_yearly">Giá năm (VNĐ) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="edit_price_yearly" name="price_yearly" min="0" step="1000" required>
                                <small class="form-text text-muted">
                                    <a href="#" id="editCalcYearlyPrice">Tính giảm 16%</a>
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_sort_order">Thứ tự hiển thị</label>
                                <input type="number" class="form-control" id="edit_sort_order" name="sort_order" min="0">
                                <small class="form-text text-muted">Số nhỏ hơn sẽ hiển thị trước</small>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="edit_features">Tính năng (mỗi dòng 1 tính năng)</label>
                        <textarea class="form-control" id="edit_features" name="features" rows="4"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="edit_is_active" name="is_active" value="1">
                                    <label class="custom-control-label" for="edit_is_active">Kích hoạt gói</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="edit_is_popular" name="is_popular" value="1">
                                    <label class="custom-control-label" for="edit_is_popular">Đánh dấu là gói phổ biến</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="las la-save mr-1"></i>Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .icon-small {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .feature-preview {
        background: #f8f9fa;
        border-radius: 4px;
        padding: 8px 12px;
        margin-top: 8px;
        font-size: 0.875rem;
    }
    .feature-preview ul {
        margin: 0;
        padding-left: 20px;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Tính giá năm giảm 16% khi thêm gói
    $('#calcYearlyPrice').click(function(e) {
        e.preventDefault();
        var monthlyPrice = parseFloat($('#add_price_monthly').val()) || 0;
        var yearlyPrice = Math.round(monthlyPrice * 12 * 0.84 / 1000) * 1000;
        $('#add_price_yearly').val(yearlyPrice);
    });

    // Tính giá năm giảm 16% khi sửa gói
    $('#editCalcYearlyPrice').click(function(e) {
        e.preventDefault();
        var monthlyPrice = parseFloat($('#edit_price_monthly').val()) || 0;
        var yearlyPrice = Math.round(monthlyPrice * 12 * 0.84 / 1000) * 1000;
        $('#edit_price_yearly').val(yearlyPrice);
    });

    // Xử lý khi click nút sửa gói
    $('.edit-plan-btn').click(function() {
        var id = $(this).data('id');
        var planId = $(this).data('plan-id');
        var name = $(this).data('name');
        var storage = $(this).data('storage');
        var priceMonthly = $(this).data('price-monthly');
        var priceYearly = $(this).data('price-yearly');
        var features = $(this).data('features');
        var isActive = $(this).data('is-active');
        var isPopular = $(this).data('is-popular');
        var sortOrder = $(this).data('sort-order');

        // Cập nhật form action
        $('#editPlanForm').attr('action', '{{ url("admin/storage-plans") }}/' + id);

        // Điền dữ liệu vào form
        $('#edit_plan_id_display').val(planId);
        $('#edit_name').val(name);
        $('#edit_storage_gb').val(storage);
        $('#edit_price_monthly').val(priceMonthly);
        $('#edit_price_yearly').val(priceYearly);
        $('#edit_features').val(features);
        $('#edit_sort_order').val(sortOrder);
        $('#edit_is_active').prop('checked', isActive == '1');
        $('#edit_is_popular').prop('checked', isPopular == '1');
    });
});
</script>
@endpush
