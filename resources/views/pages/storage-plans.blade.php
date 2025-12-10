@extends('layouts.app')

@section('title', 'Gói lưu trữ - Cloody')

@push('styles')
<style>
    /* Storage Plans Page Styles */
    .storage-plans-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 10px;
        padding: 1rem 1.25rem;
        color: white;
        margin-bottom: 1rem;
        box-shadow: 0 3px 15px rgba(102, 126, 234, 0.15);
    }
    
    .storage-plans-header h2 {
        color: white;
        font-weight: 700;
        margin-bottom: 0.15rem;
        font-size: 1.5rem;
    }
    
    .storage-plans-header p {
        color: rgba(255, 255, 255, 0.9);
        font-size: 0.875rem;
        margin-bottom: 0;
    }
    
    /* Subscription Expiry Warning */
    .subscription-expiry-warning {
        background: #fff9e6;
        border-left: 4px solid #ff9800;
        border-radius: 8px;
        padding: 12px 16px;
        margin-bottom: 1rem;
        box-shadow: 0 2px 8px rgba(255, 152, 0, 0.1);
    }
    
    .subscription-expiry-warning .warning-icon {
        font-size: 1.5rem;
        color: #ff9800;
        margin-right: 12px;
        flex-shrink: 0;
    }
    
    .subscription-expiry-warning .warning-text {
        color: #4a5568;
        font-size: 0.9rem;
        line-height: 1.5;
        flex: 1;
    }
    
    .current-storage-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.875rem 1rem;
        margin-bottom: 1rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }
    
    .current-storage-card h5 {
        color: #4a5568;
        font-weight: 600;
        font-size: 0.85rem;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
    }
    
    .current-storage-card h5 i {
        color: #667eea;
        font-size: 0.9rem;
        margin-right: 0.5rem;
    }
    
    .storage-stats {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }
    
    .storage-stats > div:first-child {
        flex: 1;
        min-width: 0;
    }
    
    .storage-stats p {
        margin-bottom: 0.5rem;
        font-size: 0.8rem;
        color: #718096;
        line-height: 1.4;
    }
    
    .storage-stats strong {
        color: #2d3748;
        font-weight: 600;
    }
    
    .storage-stats .text-primary {
        color: #667eea !important;
    }
    
    .storage-used-display {
        text-align: center;
        padding: 0.5rem 0.75rem;
        background: #f7fafc;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        min-width: 80px;
    }
    
    .storage-used-display h3 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #667eea;
        margin-bottom: 0.15rem;
        line-height: 1.2;
    }
    
    .storage-used-display p {
        font-size: 0.75rem;
        color: #718096;
        margin-bottom: 0;
        font-weight: 500;
    }
    
    .progress-custom {
        height: 8px;
        border-radius: 4px;
        background: #e2e8f0;
        overflow: hidden;
        margin: 0.5rem 0;
    }
    
    .progress-bar-custom {
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        border-radius: 4px;
        height: 100%;
        transition: width 1s ease;
    }
    
    @media (max-width: 576px) {
        .storage-stats {
            flex-direction: column;
            align-items: stretch;
        }
        
        .storage-used-display {
            width: 100%;
            margin-top: 0.5rem;
        }
    }
    
    .billing-toggle-card {
        background: white;
        border: 2px solid #e9ecef;
        border-radius: 10px;
        padding: 0.75rem;
        margin-bottom: 1rem;
        text-align: center;
    }
    
    .billing-toggle-card h6 {
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
    }
    
    .billing-toggle-wrapper {
        display: inline-flex;
        background: #f8f9fa;
        border-radius: 10px;
        padding: 4px;
        position: relative;
    }
    
    .billing-toggle-wrapper label {
        margin: 0;
        padding: 8px 20px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        z-index: 1;
        font-size: 0.9rem;
    }
    
    .billing-toggle-wrapper input[type="radio"] {
        position: absolute;
        opacity: 0;
    }
    
    .billing-toggle-wrapper input[type="radio"]:checked + label,
    .billing-toggle-wrapper label.active {
        background: #667eea;
        color: white;
        box-shadow: 0 3px 10px rgba(102, 126, 234, 0.3);
    }
    
    .billing-toggle-wrapper label:not(.active) {
        color: #6c757d;
    }
    
    .billing-toggle-wrapper label:hover {
        background: rgba(102, 126, 234, 0.1);
    }
    
    .plan-card {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
        background: white;
        height: 100%;
        display: flex;
        flex-direction: column;
        position: relative;
    }
    
    .plan-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .plan-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.15);
        border-color: #667eea;
    }
    
    .plan-card:hover::before {
        opacity: 1;
    }
    
    .plan-card.current-plan {
        border-color: #667eea;
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.12);
    }
    
    .plan-card.current-plan::before {
        opacity: 1;
    }
    
    .plan-card.recommended {
        border-color: #28a745;
        box-shadow: 0 10px 25px rgba(40, 167, 69, 0.15);
        transform: scale(1.02);
    }
    
    .plan-card.recommended::before {
        background: linear-gradient(90deg, #28a745 0%, #20c997 100%);
        opacity: 1;
        height: 5px;
    }
    
    .plan-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        padding: 4px 12px;
        border-radius: 15px;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        z-index: 10;
    }
    
    .plan-badge.recommended {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        box-shadow: 0 3px 10px rgba(40, 167, 69, 0.3);
    }
    
    .plan-badge.current {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 3px 10px rgba(102, 126, 234, 0.3);
    }
    
    .plan-card-body {
        padding: 1.25rem 1rem;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    
    .plan-name {
        font-size: 1.1rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 0.75rem;
    }
    
    .plan-price {
        margin-bottom: 0.75rem;
    }
    
    .plan-price-amount {
        font-size: 2rem;
        font-weight: 700;
        color: #667eea;
        line-height: 1;
        margin-bottom: 0.15rem;
    }
    
    .plan-price-period {
        color: #6c757d;
        font-size: 0.85rem;
    }
    
    .plan-storage {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        border-radius: 8px;
        padding: 0.75rem;
        margin-bottom: 0.75rem;
    }
    
    .plan-storage-amount {
        font-size: 1.75rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 0.15rem;
    }
    
    .plan-storage-label {
        color: #6c757d;
        font-size: 0.8rem;
    }
    
    .plan-features {
        list-style: none;
        padding: 0;
        margin: 0 0 0.75rem 0;
        flex: 1;
    }
    
    .plan-features li {
        padding: 0.4rem 0;
        display: flex;
        align-items: center;
        color: #4a5568;
        font-size: 0.8rem;
    }
    
    .plan-features li i {
        color: #28a745;
        font-size: 0.9rem;
        margin-right: 0.5rem;
        flex-shrink: 0;
    }
    
    .plan-footer {
        padding: 0.75rem 1rem;
        background: #f8f9fa;
        border-top: 1px solid #e9ecef;
    }
    
    .btn-upgrade {
        width: 100%;
        padding: 10px 20px;
        font-weight: 600;
        border-radius: 8px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        transition: all 0.3s ease;
        box-shadow: 0 3px 12px rgba(102, 126, 234, 0.25);
        font-size: 0.9rem;
    }
    
    .btn-upgrade:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 18px rgba(102, 126, 234, 0.35);
        color: white;
    }
    
    .btn-current {
        width: 100%;
        padding: 10px 20px;
        font-weight: 600;
        border-radius: 8px;
        background: #e9ecef;
        border: none;
        color: #6c757d;
        cursor: not-allowed;
        font-size: 0.9rem;
    }
    
    .info-footer-card {
        background: white;
        border-radius: 10px;
        padding: 1rem 1.25rem;
        margin-top: 1.5rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }
    
    .info-section h6 {
        color: #2d3748;
        font-weight: 700;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        font-size: 0.95rem;
    }
    
    .info-section h6 i {
        margin-right: 0.5rem;
        font-size: 1rem;
    }
    
    .info-section ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .info-section ul li {
        padding: 0.4rem 0;
        color: #4a5568;
        display: flex;
        align-items: center;
        font-size: 0.8rem;
    }
    
    .info-section ul li i {
        margin-right: 0.5rem;
        font-size: 0.9rem;
    }
    
    /* Container Optimization */
    .container-fluid {
        padding-top: 1rem !important;
        padding-bottom: 1rem !important;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .storage-plans-header {
            padding: 0.75rem 1rem;
        }
        
        .storage-stats {
            flex-direction: column;
            gap: 1.5rem;
        }
        
        .plan-card.recommended {
            transform: scale(1);
        }
        
        .plan-price-amount {
            font-size: 2rem;
        }
        
        .plan-storage-amount {
            font-size: 2rem;
        }
    }
    
    /* Animation */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .plan-card {
        animation: fadeInUp 0.6s ease forwards;
    }
    
    .plan-card:nth-child(1) { animation-delay: 0.1s; }
    .plan-card:nth-child(2) { animation-delay: 0.2s; }
    .plan-card:nth-child(3) { animation-delay: 0.3s; }
    .plan-card:nth-child(4) { animation-delay: 0.4s; }
</style>
@endpush

@section('content')
<div class="container-fluid" style="padding-top: 1rem; padding-bottom: 1rem;">
    <div class="row">
        <div class="col-lg-12">
            <!-- Header with Gradient -->
            <div class="storage-plans-header">
                <div class="d-flex align-items-center">
                    <i class="las la-cloud mr-2" style="font-size: 1.75rem; opacity: 0.9;"></i>
                    <div>
                        <h2 class="mb-0">Gói lưu trữ Cloody</h2>
                        <p class="mb-0">Nâng cấp để có thêm dung lượng lưu trữ</p>
                    </div>
                </div>
            </div>

            <!-- Subscription Expiry Warning -->
            @if($activeSubscription && $expiresAt && $expiresAt->isFuture())
            @php
                $months = [1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5', 6 => '6', 
                          7 => '7', 8 => '8', 9 => '9', 10 => '10', 11 => '11', 12 => '12'];
                $formattedDate = $expiresAt->format('d') . ' thg ' . $expiresAt->format('n') . ', ' . $expiresAt->format('Y');
            @endphp
            <div class="subscription-expiry-warning">
                <div class="d-flex align-items-center">
                    <i class="las la-exclamation-triangle warning-icon"></i>
                    <div class="warning-text">
                        Gói dịch vụ của bạn sẽ kết thúc vào ngày {{ $formattedDate }}. 
                        Bạn có thể gia hạn gói này bất cứ lúc nào.
                    </div>
                </div>
            </div>
            @endif

            <!-- Current Storage Info -->
            <div class="current-storage-card">
                <h5>
                    <i class="ri-bar-chart-line"></i>Dung lượng hiện tại
                </h5>
                <div class="storage-stats">
                    <div>
                        <p>
                            <strong class="text-primary">{{ number_format($storageUsedGB, 2) }} GB</strong> 
                            / <strong>{{ $currentLimitGB }} GB</strong>
                        </p>
                        <div class="progress-custom">
                            @php($currentPercent = min(($storageUsedGB / $currentLimitGB) * 100, 100))
                            <div class="progress-bar-custom" 
                                 style="width: {{ number_format($currentPercent, 1) }}%"
                                 data-percent="{{ number_format($currentPercent, 1) }}%">
                            </div>
                        </div>
                        <p style="margin-bottom: 0;">
                            <i class="ri-checkbox-circle-line text-success" style="font-size: 0.75rem;"></i>
                            Còn lại: <strong style="color: #28a745;">{{ number_format(max($currentLimitGB - $storageUsedGB, 0), 2) }} GB</strong>
                        </p>
                    </div>
                    <div class="storage-used-display">
                        <h3>{{ number_format($storageUsedGB, 2) }}</h3>
                        <p>GB đã sử dụng</p>
                    </div>
                </div>
            </div>

            <!-- Billing Cycle Toggle -->
            <div class="billing-toggle-card">
                <h6 style="color: #2d3748; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;"></h6>
                <div class="billing-toggle-wrapper" id="billingCycleToggle">
                    <input type="radio" name="billing_cycle" value="monthly" id="billing_monthly" checked>
                    <label for="billing_monthly" class="active">
                        Hằng tháng
                    </label>
                    <input type="radio" name="billing_cycle" value="yearly" id="billing_yearly">
                    <label for="billing_yearly">
                        Hằng năm
                        <small class="d-block mt-1" style="font-size: 0.65rem; opacity: 0.9;">Tiết kiệm 16%</small>
                    </label>
                </div>
            </div>

            <!-- Storage Plans -->
            <div class="row" id="plansContainer">
                @foreach($plans as $index => $plan)
                <div class="col-lg-3 col-md-6 mb-2">
                    <div class="card plan-card {{ ($plan['current_monthly'] || $plan['current_yearly']) ? 'current-plan' : '' }} {{ isset($plan['recommended']) && $plan['recommended'] === true ? 'recommended' : '' }}" 
                         data-plan-id="{{ $plan['id'] }}"
                         data-plan-order="{{ $plan['order'] }}"
                         data-can-purchase="{{ $plan['can_purchase'] ? 'true' : 'false' }}">
                        @if(isset($plan['recommended']) && $plan['recommended'] === true)
                        <span class="plan-badge recommended">Đề xuất</span>
                        @endif
                        @if($plan['current_monthly'])
                        <span class="plan-badge current current-badge-monthly">Gói hiện tại (Tháng)</span>
                        @endif
                        @if($plan['current_yearly'])
                        <span class="plan-badge current current-badge-yearly">Gói hiện tại (Năm)</span>
                        @endif
                        
                        <div class="plan-card-body">
                            <h4 class="plan-name">{{ $plan['name'] }}</h4>
                            
                            @if(isset($plan['price_monthly']) && $plan['price_monthly'] > 0 && isset($plan['discount_percent']))
                            <div class="plan-discount-badge" style="display: none; margin-bottom: 0.5rem;">
                                <span style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">
                                    <i class="ri-percent-line"></i> Tiết kiệm {{ $plan['discount_percent'] }}%
                                </span>
                            </div>
                            @endif
                            
                            <div class="plan-price">
                                <div class="plan-price-amount">
                                    <span class="price-monthly">{{ number_format($plan['price_monthly'], 0, ',', '.') }}</span>
                                    <span class="price-yearly" style="display: none;">
                                        <span class="price-yearly-discounted">{{ number_format($plan['price_yearly'], 0, ',', '.') }}</span>
                                        @if(isset($plan['price_yearly_original']))
                                        <span class="price-yearly-original" style="text-decoration: line-through; color: #999; font-size: 0.9em; margin-left: 0.5rem;">{{ number_format($plan['price_yearly_original'], 0, ',', '.') }}</span>
                                        @endif
                                    </span>
                                    <small style="font-size: 1.5rem;">₫</small>
                                </div>
                                <div class="plan-price-period">
                                    <span class="billing-text-monthly">/ tháng</span>
                                    <span class="billing-text-yearly" style="display: none;">/ năm</span>
                                </div>
                            </div>
                            
                            <div class="plan-storage">
                                @if($plan['storage_gb'] >= 1024)
                                    <div class="plan-storage-amount">{{ number_format($plan['storage_gb'] / 1024, 1) }} TB</div>
                                @else
                                    <div class="plan-storage-amount">{{ $plan['storage_gb'] }} GB</div>
                                @endif
                                <div class="plan-storage-label">Dung lượng lưu trữ</div>
                            </div>
                            
                            <ul class="plan-features">
                                @foreach($plan['features'] as $feature)
                                <li>
                                    <i class="ri-checkbox-circle-fill"></i>
                                    <span>{{ $feature }}</span>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        
                        <div class="plan-footer">
                            @if($plan['current_monthly'] || $plan['current_yearly'])
                            <button class="btn btn-current" disabled>
                                <i class="ri-check-line mr-2"></i>Gói hiện tại
                            </button>
                            @elseif($plan['can_purchase'])
                            <button class="btn btn-upgrade upgrade-btn" 
                                    data-plan-id="{{ $plan['id'] }}"
                                    data-storage-gb="{{ $plan['storage_gb'] }}"
                                    data-price-monthly="{{ $plan['price_monthly'] }}"
                                    data-price-yearly="{{ $plan['price_yearly'] }}"
                                    data-can-purchase="true">
                                <i class="ri-arrow-up-circle-line mr-2"></i>Nâng cấp
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Info Footer -->
            <div class="info-footer-card">
                <div class="row">
                    <div class="col-md-6 mb-2 mb-md-0">
                        <div class="info-section">
                            <h6>
                                <i class="ri-information-line text-primary"></i>
                                Thông tin quan trọng
                            </h6>
                            <ul>
                                <li>
                                    <i class="ri-checkbox-circle-line text-success"></i>
                                    <span>Bạn có thể hủy bất cứ lúc nào</span>
                                </li>
                                <li>
                                    <i class="ri-checkbox-circle-line text-success"></i>
                                    <span>Thanh toán an toàn và bảo mật</span>
                                </li>
                                <li>
                                    <i class="ri-checkbox-circle-line text-success"></i>
                                    <span>Hỗ trợ khách hàng 24/7</span>
                                </li>
                                <li>
                                    <i class="ri-checkbox-circle-line text-success"></i>
                                    <span>Không giới hạn số lượng file</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-section">
                            <h6>
                                <i class="ri-question-line text-primary"></i>
                                Câu hỏi thường gặp
                            </h6>
                            <ul>
                                <li>
                                    <i class="ri-arrow-right-s-line text-primary"></i>
                                    <span>Dung lượng được cộng dồn hay thay thế?</span>
                                </li>
                                <li>
                                    <i class="ri-arrow-right-s-line text-primary"></i>
                                    <span>Có thể nâng cấp/giảm cấp gói không?</span>
                                </li>
                                <li>
                                    <i class="ri-arrow-right-s-line text-primary"></i>
                                    <span>Dữ liệu có bị mất khi đổi gói?</span>
                                </li>
                                <li>
                                    <i class="ri-arrow-right-s-line text-primary"></i>
                                    <span>Có thể hoàn tiền không?</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upgrade Modal -->
<div class="modal fade" id="upgradeModal" tabindex="-1" role="dialog" aria-labelledby="upgradeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 15px 40px rgba(0,0,0,0.2);">
            <div class="modal-header" style="border-bottom: 1px solid #e9ecef; padding: 1.25rem;">
                <h5 class="modal-title" style="font-weight: 700; color: #2d3748; font-size: 1.1rem;">
                    <i class="ri-checkbox-circle-line text-primary mr-2"></i>Xác nhận nâng cấp
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="padding: 0.5rem;">
                    <span aria-hidden="true" style="font-size: 1.5rem; color: #6c757d;">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 1.5rem;">
                <div class="text-center mb-3">
                    <i class="ri-cloud-line text-primary" style="font-size: 3rem; opacity: 0.3;"></i>
                </div>
                <p class="text-center mb-3" style="font-size: 1rem; color: #4a5568;">
                    Bạn có chắc chắn muốn nâng cấp lên gói <strong style="color: #667eea;" id="modalPlanName"></strong>?
                </p>
                <div class="card" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%); border: none; border-radius: 10px; padding: 1.25rem;">
                    <div class="row text-center">
                        <div class="col-6">
                            <div style="color: #6c757d; font-size: 0.85rem; margin-bottom: 0.5rem;">Dung lượng</div>
                            <div style="font-size: 1.25rem; font-weight: 700; color: #2d3748;" id="modalStorage"></div>
                            <div style="color: #6c757d; font-size: 0.8rem;" id="modalStorageUnit"></div>
                        </div>
                        <div class="col-6">
                            <div style="color: #6c757d; font-size: 0.85rem; margin-bottom: 0.5rem;">Giá</div>
                            <div style="font-size: 1.25rem; font-weight: 700; color: #667eea;" id="modalPrice"></div>
                            <div style="color: #6c757d; font-size: 0.8rem;">₫</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #e9ecef; padding: 1.25rem;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 8px; padding: 8px 20px; font-weight: 600; font-size: 0.9rem;">
                    <i class="ri-close-line mr-2"></i>Hủy
                </button>
                <button type="button" class="btn btn-primary" id="confirmUpgrade" style="border-radius: 8px; padding: 8px 20px; font-weight: 600; font-size: 0.9rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; box-shadow: 0 3px 12px rgba(102, 126, 234, 0.25);">
                    <i class="ri-check-line mr-2"></i>Xác nhận
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const billingToggle = document.getElementById('billingCycleToggle');
    const priceMonthly = document.querySelectorAll('.price-monthly');
    const priceYearly = document.querySelectorAll('.price-yearly');
    const billingTextMonthly = document.querySelectorAll('.billing-text-monthly');
    const billingTextYearly = document.querySelectorAll('.billing-text-yearly');
    const discountBadges = document.querySelectorAll('.plan-discount-badge');
    
    // Toggle billing cycle
    billingToggle.addEventListener('change', function(e) {
        const isYearly = e.target.value === 'yearly';
        
        // Update labels
        document.querySelectorAll('#billingCycleToggle label').forEach(label => {
            label.classList.remove('active');
        });
        if (isYearly) {
            document.querySelector('label[for="billing_yearly"]').classList.add('active');
        } else {
            document.querySelector('label[for="billing_monthly"]').classList.add('active');
        }
        
        // Update prices
        priceMonthly.forEach(el => el.style.display = isYearly ? 'none' : 'inline');
        priceYearly.forEach(el => el.style.display = isYearly ? 'inline' : 'none');
        billingTextMonthly.forEach(el => el.style.display = isYearly ? 'none' : 'inline');
        billingTextYearly.forEach(el => el.style.display = isYearly ? 'inline' : 'none');
        
        // Show/hide discount badges
        discountBadges.forEach(badge => {
            badge.style.display = isYearly ? 'block' : 'none';
        });
        
        // Update current plan badges based on billing cycle
        document.querySelectorAll('.current-badge-monthly').forEach(badge => {
            badge.style.display = isYearly ? 'none' : 'block';
        });
        document.querySelectorAll('.current-badge-yearly').forEach(badge => {
            badge.style.display = isYearly ? 'block' : 'none';
        });
        
        // Update button states
        updateButtonStates(isYearly);
    });
    
    // Function to update button states based on billing cycle
    function updateButtonStates(isYearly) {
        const currentPlanId = '{{ $currentPlanId }}';
        const currentBillingCycle = '{{ $currentBillingCycle }}';
        
        document.querySelectorAll('.plan-card').forEach(card => {
            const planId = card.dataset.planId;
            const footer = card.querySelector('.plan-footer');
            const button = footer.querySelector('button');
            
            if (!button) return;
            
            // Check if this is the current plan with current billing cycle
            const isCurrentPlan = (planId === currentPlanId) && 
                                 ((isYearly && currentBillingCycle === 'yearly') || 
                                  (!isYearly && currentBillingCycle === 'monthly'));
            
            if (isCurrentPlan) {
                button.className = 'btn btn-current';
                button.disabled = true;
                button.innerHTML = '<i class="ri-check-line mr-2"></i>Gói hiện tại';
            } else {
                // Chỉ có thể nâng cấp lên gói cao hơn
                button.className = 'btn btn-upgrade upgrade-btn';
                button.disabled = false;
                button.innerHTML = '<i class="ri-arrow-up-circle-line mr-2"></i>Nâng cấp';
            }
        });
    }
    
    // Initialize button states on page load
    const initialBillingCycle = document.querySelector('input[name="billing_cycle"]:checked').value === 'yearly';
    updateButtonStates(initialBillingCycle);
    
    // Upgrade button click
    document.querySelectorAll('.upgrade-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const planId = this.dataset.planId;
            const storageGB = parseFloat(this.dataset.storageGb);
            const priceMonthly = this.dataset.priceMonthly;
            const priceYearly = this.dataset.priceYearly;
            const isYearly = document.querySelector('input[name="billing_cycle"]:checked').value === 'yearly';
            const price = isYearly ? priceYearly : priceMonthly;
            
            // Find plan name
            const planCard = this.closest('.plan-card');
            const planName = planCard.querySelector('.plan-name').textContent;
            
            // Format storage display (GB or TB)
            let storageDisplay = '';
            let storageUnit = '';
            if (storageGB >= 1024) {
                storageDisplay = (storageGB / 1024).toFixed(1);
                storageUnit = 'TB';
            } else {
                storageDisplay = storageGB.toString();
                storageUnit = 'GB';
            }
            
            // Set modal content
            document.getElementById('modalPlanName').textContent = planName;
            document.getElementById('modalStorage').textContent = storageDisplay;
            document.getElementById('modalStorageUnit').textContent = storageUnit;
            document.getElementById('modalPrice').textContent = parseInt(price).toLocaleString('vi-VN');
            
            // Show modal
            $('#upgradeModal').modal('show');
            
            // Store plan data for confirmation
            document.getElementById('confirmUpgrade').dataset.planId = planId;
            document.getElementById('confirmUpgrade').dataset.storageGb = storageGB;
            document.getElementById('confirmUpgrade').dataset.price = price;
            document.getElementById('confirmUpgrade').dataset.billingCycle = isYearly ? 'yearly' : 'monthly';
        });
    });
    
    // Confirm upgrade
    document.getElementById('confirmUpgrade').addEventListener('click', function() {
        const planId = this.dataset.planId;
        const storageGB = parseFloat(this.dataset.storageGb);
        const price = this.dataset.price;
        const billingCycle = this.dataset.billingCycle;
        
        // Disable button to prevent double-click
        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="ri-loader-4-line mr-2"></i>Đang xử lý...';
        
        // Send upgrade request
        fetch('{{ route("cloody.storage.upgrade") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                plan_id: planId,
                billing_cycle: billingCycle
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.redirect && data.payment_url) {
                    // Close upgrade modal
                    $('#upgradeModal').modal('hide');
                    
                    // Show processing modal
                    $('#paymentProcessingModal').modal('show');
                    
                    // Create form and submit to payment (maintains session)
                    // The processing modal will close automatically when redirected to VNPay
                    setTimeout(() => {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = data.payment_url;
                        form.style.display = 'none';
                        
                        const csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = '{{ csrf_token() }}';
                        form.appendChild(csrfToken);
                        
                        const planIdInput = document.createElement('input');
                        planIdInput.type = 'hidden';
                        planIdInput.name = 'plan_id';
                        planIdInput.value = planId;
                        form.appendChild(planIdInput);
                        
                        const billingCycleInput = document.createElement('input');
                        billingCycleInput.type = 'hidden';
                        billingCycleInput.name = 'billing_cycle';
                        billingCycleInput.value = billingCycle;
                        form.appendChild(billingCycleInput);
                        
                        document.body.appendChild(form);
                        form.submit();
                    }, 500); // Small delay to show processing modal
                } else {
                    // Show success message
                    alert(data.message || 'Nâng cấp thành công!');
                    
                    // Close modal
                    $('#upgradeModal').modal('hide');
                    
                    // Reload page to show updated plan
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                }
            } else {
                // Show error message
                alert(data.message || 'Nâng cấp thất bại. Vui lòng thử lại.');
                
                // Re-enable button
                btn.disabled = false;
                btn.innerHTML = '<i class="ri-check-line mr-2"></i>Xác nhận nâng cấp';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Đã xảy ra lỗi. Vui lòng thử lại.');
            
            // Re-enable button
            btn.disabled = false;
            btn.innerHTML = '<i class="ri-check-line mr-2"></i>Xác nhận nâng cấp';
        });
    });
    
    // Animate progress bar on load
    setTimeout(function() {
        const progressBar = document.querySelector('.progress-bar-custom');
        if (progressBar) {
            const width = progressBar.style.width;
            progressBar.style.width = '0%';
            setTimeout(function() {
                progressBar.style.width = width;
            }, 100);
        }
    }, 300);
});
</script>
@endpush

<!-- Payment Success Modal -->
<div class="modal fade" id="paymentSuccessModal" tabindex="-1" role="dialog" aria-labelledby="paymentSuccessModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content payment-modal-content">
            <div class="modal-body text-center payment-modal-body">
                <!-- Success Icon -->
                <div class="payment-icon-wrapper payment-icon-success">
                    <i class="las la-check"></i>
                </div>
                
                <!-- Title -->
                <h4 class="payment-modal-title">Thanh toán thành công!</h4>
                
                <!-- Message -->
                <p class="payment-modal-message" id="paymentSuccessMessage">
                    Gói của bạn đã được nâng cấp thành công.
                </p>
                
                <!-- Payment Details -->
                <div class="payment-details payment-details-success">
                    <div class="payment-detail-item" id="paymentSuccessPlanName" style="display: none;">
                        <span class="payment-detail-label">Gói:</span>
                        <span class="payment-detail-value" id="paymentSuccessPlanNameValue"></span>
                    </div>
                    <div class="payment-detail-item" id="paymentSuccessAmount" style="display: none;">
                        <span class="payment-detail-label">Số tiền:</span>
                        <span class="payment-detail-value" id="paymentSuccessAmountValue"></span>
                    </div>
                    <div class="payment-detail-item" id="paymentSuccessBillingCycle" style="display: none;">
                        <span class="payment-detail-label">Chu kỳ:</span>
                        <span class="payment-detail-value" id="paymentSuccessBillingCycleValue"></span>
                    </div>
                    <div class="payment-detail-item" id="paymentSuccessTransactionId" style="display: none;">
                        <span class="payment-detail-label">Mã giao dịch:</span>
                        <span class="payment-detail-value payment-detail-code" id="paymentSuccessTransactionIdValue"></span>
                    </div>
                </div>
                
                <!-- Close Button -->
                <button type="button" class="btn payment-btn-primary" data-dismiss="modal">
                    <i class="las la-check mr-1"></i>Đóng
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Payment Failed Modal -->
<div class="modal fade" id="paymentFailedModal" tabindex="-1" role="dialog" aria-labelledby="paymentFailedModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content payment-modal-content">
            <div class="modal-body text-center payment-modal-body">
                <!-- Failed Icon -->
                <div class="payment-icon-wrapper payment-icon-failed">
                    <i class="las la-times"></i>
                </div>
                
                <!-- Title -->
                <h4 class="payment-modal-title">Thanh toán thất bại</h4>
                
                <!-- Message -->
                <p class="payment-modal-message" id="paymentFailedMessage">
                    Đã xảy ra lỗi trong quá trình thanh toán.
                </p>
                
                <!-- Error Details -->
                <div class="payment-details payment-details-failed">
                    <div class="payment-detail-item" id="paymentFailedPlanName" style="display: none;">
                        <span class="payment-detail-label">Gói:</span>
                        <span class="payment-detail-value" id="paymentFailedPlanNameValue"></span>
                    </div>
                    <div class="payment-detail-item" id="paymentFailedAmount" style="display: none;">
                        <span class="payment-detail-label">Số tiền:</span>
                        <span class="payment-detail-value" id="paymentFailedAmountValue"></span>
                    </div>
                    <div class="payment-detail-item" id="paymentFailedErrorMessage" style="display: none;">
                        <span class="payment-detail-label payment-detail-error">Lỗi:</span>
                        <span class="payment-detail-value payment-detail-error-text" id="paymentFailedErrorMessageValue"></span>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="payment-modal-actions">
                    <button type="button" class="btn payment-btn-secondary" data-dismiss="modal">
                        <i class="las la-times mr-1"></i>Đóng
                    </button>
                    <a href="{{ route('cloody.storage.plans') }}" class="btn payment-btn-primary">
                        <i class="las la-redo mr-1"></i>Thử lại
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Processing Modal -->
<div class="modal fade" id="paymentProcessingModal" tabindex="-1" role="dialog" aria-labelledby="paymentProcessingModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content payment-modal-content">
            <div class="modal-body text-center payment-modal-body">
                <!-- Processing Icon with Animation -->
                <div class="payment-icon-wrapper payment-icon-processing">
                    <i class="las la-spinner la-spin"></i>
                </div>
                
                <!-- Title -->
                <h4 class="payment-modal-title">Đang xử lý thanh toán</h4>
                
                <!-- Message -->
                <p class="payment-modal-message">
                    Vui lòng đợi trong khi chúng tôi xử lý giao dịch của bạn...
                </p>
                
                <!-- Loading Animation -->
                <div class="payment-loading-dots">
                    <div class="payment-loading-dot" style="animation-delay: -0.32s;"></div>
                    <div class="payment-loading-dot" style="animation-delay: -0.16s;"></div>
                    <div class="payment-loading-dot"></div>
                </div>
                
                <!-- Note -->
                <p class="payment-modal-note">
                    <i class="las la-info-circle mr-1"></i>Không đóng cửa sổ này cho đến khi thanh toán hoàn tất
                </p>
            </div>
        </div>
    </div>
</div>

<style>
/* Payment Modal Styles - Compact & Optimized */
.payment-modal-content {
    border-radius: 12px;
    border: none;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
    overflow: hidden;
}

.payment-modal-body {
    padding: 1.5rem 1.25rem;
}

/* Icon Wrapper */
.payment-icon-wrapper {
    width: 70px;
    height: 70px;
    margin: 0 auto 1rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
}

.payment-icon-wrapper i {
    font-size: 2.25rem;
    color: white;
}

.payment-icon-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}

.payment-icon-failed {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
}

.payment-icon-processing {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

/* Title */
.payment-modal-title {
    color: #2d3748;
    font-weight: 700;
    font-size: 1.25rem;
    margin-bottom: 0.5rem;
    line-height: 1.3;
}

/* Message */
.payment-modal-message {
    color: #718096;
    font-size: 0.9rem;
    line-height: 1.5;
    margin-bottom: 1rem;
}

/* Payment Details */
.payment-details {
    border-radius: 8px;
    padding: 0.875rem;
    margin-bottom: 1.25rem;
    text-align: left;
    font-size: 0.875rem;
}

.payment-details-success {
    background: #f7fafc;
    border: 1px solid #e2e8f0;
}

.payment-details-failed {
    background: #fff5f5;
    border-left: 3px solid #dc3545;
}

.payment-detail-item {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.payment-detail-item:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
}

.payment-detail-label {
    color: #4a5568;
    font-weight: 600;
    margin-right: 0.5rem;
    flex-shrink: 0;
    min-width: 80px;
}

.payment-detail-value {
    color: #2d3748;
    text-align: right;
    flex: 1;
    word-break: break-word;
}

.payment-detail-code {
    font-family: 'Courier New', monospace;
    font-size: 0.8rem;
    color: #667eea;
}

.payment-detail-error {
    color: #dc3545;
}

.payment-detail-error-text {
    color: #c82333;
}

/* Buttons */
.payment-btn-primary,
.payment-btn-secondary {
    border-radius: 6px;
    padding: 0.5rem 1.25rem;
    font-weight: 600;
    font-size: 0.875rem;
    border: none;
    transition: all 0.2s ease;
}

.payment-btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 3px 12px rgba(102, 126, 234, 0.25);
}

.payment-btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 16px rgba(102, 126, 234, 0.35);
    color: white;
}

.payment-btn-secondary {
    background: #e2e8f0;
    color: #4a5568;
}

.payment-btn-secondary:hover {
    background: #cbd5e0;
    color: #2d3748;
}

.payment-modal-actions {
    display: flex;
    gap: 0.75rem;
    justify-content: center;
    margin-top: 1rem;
}

/* Loading Dots */
.payment-loading-dots {
    display: flex;
    justify-content: center;
    gap: 0.4rem;
    margin: 1rem 0;
}

.payment-loading-dot {
    width: 10px;
    height: 10px;
    background: #667eea;
    border-radius: 50%;
    animation: paymentBounce 1.4s infinite ease-in-out both;
}

.payment-modal-note {
    font-size: 0.8rem;
    color: #a0aec0;
    margin-bottom: 0;
    margin-top: 0.75rem;
}

/* Animations */
@keyframes paymentBounce {
    0%, 80%, 100% {
        transform: scale(0);
        opacity: 0.5;
    }
    40% {
        transform: scale(1);
        opacity: 1;
    }
}

/* Responsive */
@media (max-width: 576px) {
    .payment-modal-body {
        padding: 1.25rem 1rem;
    }
    
    .payment-icon-wrapper {
        width: 60px;
        height: 60px;
    }
    
    .payment-icon-wrapper i {
        font-size: 1.875rem;
    }
    
    .payment-modal-title {
        font-size: 1.125rem;
    }
    
    .payment-modal-message {
        font-size: 0.85rem;
    }
    
    .payment-modal-actions {
        flex-direction: column;
    }
    
    .payment-btn-primary,
    .payment-btn-secondary {
        width: 100%;
    }
}
</style>

<script>
// Auto-show payment modals based on session
document.addEventListener('DOMContentLoaded', function() {
    @if(session('payment_success'))
        const paymentData = @json(session('payment_data', []));
        const paymentMessage = @json(session('payment_message', 'Thanh toán thành công!'));
        
        // Set message
        document.getElementById('paymentSuccessMessage').textContent = paymentMessage;
        
        // Set payment details if available
        if (paymentData.plan_name) {
            document.getElementById('paymentSuccessPlanName').style.display = 'block';
            document.getElementById('paymentSuccessPlanNameValue').textContent = paymentData.plan_name;
        }
        if (paymentData.amount) {
            document.getElementById('paymentSuccessAmount').style.display = 'block';
            document.getElementById('paymentSuccessAmountValue').textContent = paymentData.amount;
        }
        if (paymentData.billing_cycle) {
            document.getElementById('paymentSuccessBillingCycle').style.display = 'block';
            document.getElementById('paymentSuccessBillingCycleValue').textContent = paymentData.billing_cycle;
        }
        if (paymentData.transaction_id) {
            document.getElementById('paymentSuccessTransactionId').style.display = 'block';
            document.getElementById('paymentSuccessTransactionIdValue').textContent = paymentData.transaction_id;
        }
        
        // Show modal
        $('#paymentSuccessModal').modal('show');
    @endif
    
    @if(session('payment_failed'))
        const paymentData = @json(session('payment_data', []));
        const paymentMessage = @json(session('payment_message', 'Thanh toán thất bại.'));
        
        // Set message
        document.getElementById('paymentFailedMessage').textContent = paymentMessage;
        
        // Set error details if available
        if (paymentData.plan_name) {
            document.getElementById('paymentFailedPlanName').style.display = 'block';
            document.getElementById('paymentFailedPlanNameValue').textContent = paymentData.plan_name;
        }
        if (paymentData.amount) {
            document.getElementById('paymentFailedAmount').style.display = 'block';
            document.getElementById('paymentFailedAmountValue').textContent = paymentData.amount;
        }
        if (paymentData.error_message) {
            document.getElementById('paymentFailedErrorMessage').style.display = 'block';
            document.getElementById('paymentFailedErrorMessageValue').textContent = paymentData.error_message;
        }
        
        // Show modal
        $('#paymentFailedModal').modal('show');
    @endif
    
    // Show processing modal when redirecting to payment
    // This will be triggered when user clicks upgrade button
    // The modal will be closed automatically when redirected to VNPay
});
</script>
@endsection
