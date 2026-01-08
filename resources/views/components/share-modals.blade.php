<!-- Modal Chia sẻ File/Folder -->
<div class="modal fade" id="shareModal" tabindex="-1" role="dialog" aria-labelledby="shareModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="shareModalLabel">
                    <i class="ri-share-line mr-2"></i><span id="shareModalTitle">{{ __('common.share') }}</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Alert Messages -->
                <div id="shareAlertContainer"></div>

                <!-- Navigation Tabs -->
                <ul class="nav nav-pills mb-3" id="shareTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="user-share-tab" data-toggle="pill" href="#user-share" role="tab">
                            <i class="ri-user-line mr-1"></i> {{ __('common.share_with_user') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="public-link-tab" data-toggle="pill" href="#public-link" role="tab">
                            <i class="ri-link mr-1"></i> {{ __('common.create_public_link') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="current-shares-tab" data-toggle="pill" href="#current-shares" role="tab">
                            <i class="ri-list-check mr-1"></i> {{ __('common.current_shares') }}
                        </a>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="shareTabContent">
                    <!-- Share with User Tab -->
                    <div class="tab-pane fade show active" id="user-share" role="tabpanel">
                        <form id="shareUserForm">
                            <div class="form-group">
                                <label for="shareEmail">{{ __('common.email_address') }} <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="shareEmail" required 
                                       placeholder="{{ __('common.enter_email_to_share') }}">
                                <small class="form-text text-muted">{{ __('common.enter_email_to_share') }}</small>
                            </div>
                            <div class="form-group">
                                <label for="sharePermission">{{ __('common.permission') }}</label>
                                <select class="form-control" id="sharePermission">
                                    <option value="view">{{ __('common.view_only') }}</option>
                                    <option value="download">{{ __('common.view_and_download') }}</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-share-line mr-1"></i> {{ __('common.share') }}
                            </button>
                        </form>
                    </div>

                    <!-- Create Public Link Tab -->
                    <div class="tab-pane fade" id="public-link" role="tabpanel">
                        <div class="alert alert-info">
                            <i class="ri-information-line mr-2"></i>
                            <span id="publicLinkInfo">{{ __('common.public_link_info') }}</span>
                        </div>
                        <form id="publicLinkForm">
                            <div class="form-group">
                                <label for="publicPermission">{{ __('common.permission') }}</label>
                                <select class="form-control" id="publicPermission">
                                    <option value="view">{{ __('common.view_only') }}</option>
                                    <option value="download">{{ __('common.view_and_download') }}</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="publicExpires">{{ __('common.expires_in') }}</label>
                                <select class="form-control" id="publicExpires">
                                    <option value="">{{ __('common.never_expires') }}</option>
                                    <option value="1">1 {{ __('common.day') }}</option>
                                    <option value="7">7 {{ __('common.days') }}</option>
                                    <option value="30">30 {{ __('common.days') }}</option>
                                    <option value="90">90 {{ __('common.days') }}</option>
                                    <option value="365">365 {{ __('common.days') }}</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success">
                                <i class="ri-link mr-1"></i> {{ __('common.create_link') }}
                            </button>
                        </form>
                    </div>

                    <!-- Current Shares Tab -->
                    <div class="tab-pane fade" id="current-shares" role="tabpanel">
                        <div id="sharesListContainer">
                            <div class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">{{ __('common.loading') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('common.close') }}</button>
            </div>
        </div>
    </div>
</div>

<style>
#shareModal .nav-pills .nav-link {
    color: #6c757d;
    border-radius: 0.25rem;
    padding: 0.5rem 1rem;
}
#shareModal .nav-pills .nav-link.active {
    background-color: #007bff;
    color: #fff;
}
#shareModal .nav-pills .nav-link:hover:not(.active) {
    background-color: #e9ecef;
}
#shareModal .share-item {
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
    padding: 1rem;
    margin-bottom: 0.75rem;
    position: relative;
}
#shareModal .share-item:hover {
    background-color: #f8f9fa;
}
#shareModal .share-item.expired {
    background-color: #f8d7da;
    border-color: #f5c6cb;
}
#shareModal .share-url-group {
    display: flex;
    gap: 0.5rem;
}
#shareModal .share-url-input {
    flex: 1;
    font-size: 0.875rem;
}
#shareModal .btn-copy {
    white-space: nowrap;
}
</style>

<script>
// Share Manager Class
class ShareManager {
    constructor() {
        this.currentItemId = null;
        this.currentItemType = 'file';
        this.modal = null;
        this.cachedShares = undefined; // Cache for shares data
    }

    openShareModal(itemId, itemType) {
        this.currentItemId = itemId;
        this.currentItemType = itemType;
        this.cachedShares = undefined; // Clear cache for new item
        this.currentItemType = itemType;
        
        // Update modal title
        const titleElement = document.getElementById('shareModalTitle');
        const infoElement = document.getElementById('publicLinkInfo');
        if (itemType === 'folder') {
            titleElement.textContent = '{{ __("common.share_folder") }}';
            infoElement.textContent = '{{ __("common.public_link_folder_info") }}';
        } else {
            titleElement.textContent = '{{ __("common.share_file") }}';
            infoElement.textContent = '{{ __("common.public_link_info") }}';
        }
        
        // Clear forms
        document.getElementById('shareUserForm').reset();
        document.getElementById('publicLinkForm').reset();
        document.getElementById('shareAlertContainer').innerHTML = '';
        
        // Reset to first tab
        $('#shareTab a:first').tab('show');
        
        // Remove old event listener to prevent duplicates
        $('#current-shares-tab').off('shown.bs.tab');
        
        // Load shares when current shares tab is clicked
        $('#current-shares-tab').on('shown.bs.tab', () => {
            this.loadShares();
        });
        
        // Show modal and wait for it to be fully shown
        $('#shareModal').off('shown.bs.modal').on('shown.bs.modal', () => {
            // Load shares immediately when modal is shown
            this.loadSharesInBackground();
        });
        
        this.modal = $('#shareModal').modal('show');
    }
    
    async loadSharesInBackground() {
        // Load ALL shares data (not just current file/folder)
        try {
            const endpoint = '/cloody/shares/all';
            
            const response = await fetch(endpoint);
            const data = await response.json();
            
            if (data.success) {
                // Cache ALL shares data
                this.cachedShares = data.shares;
                console.log('Pre-loaded ALL shares:', data.shares.length);
            } else {
                // Even if no shares, still cache empty array
                this.cachedShares = [];
                console.log('No shares found, cached empty array');
            }
        } catch (error) {
            console.error('Error pre-loading shares:', error);
            // Cache empty array on error so tab can still be viewed
            this.cachedShares = [];
        }
    }

    async loadShares() {
        const container = document.getElementById('sharesListContainer');
        
        // Check if we have cached data (including empty array)
        if (this.cachedShares !== undefined && this.cachedShares !== null) {
            console.log('Using cached shares:', this.cachedShares.length);
            this.displayShares(this.cachedShares);
            return;
        }
        
        // Show loading spinner
        container.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="sr-only">Đang tải...</span></div></div>';
        
        try {
            // Load ALL shares (not just current file)
            const endpoint = '/cloody/shares/all';
            
            console.log('Fetching ALL shares from:', endpoint);
            const response = await fetch(endpoint);
            const data = await response.json();
            
            console.log('All shares response:', data);
            
            if (data.success) {
                this.cachedShares = data.shares || [];
                this.displayShares(this.cachedShares);
            } else {
                // Still show empty state, not error
                this.cachedShares = [];
                this.displayShares([]);
            }
        } catch (error) {
            console.error('Error loading shares:', error);
            // Show empty state instead of error
            this.cachedShares = [];
            this.displayShares([]);
        }
    }

    displayShares(shares) {
        const container = document.getElementById('sharesListContainer');
        
        if (shares.length === 0) {
            container.innerHTML = '<div class="text-center py-4 text-muted"><i class="ri-share-line" style="font-size: 48px;"></i><p class="mt-2">Chưa có chia sẻ nào</p></div>';
            return;
        }

        let html = '';
        shares.forEach(share => {
            const isExpired = share.is_expired;
            const expiryText = share.expires_at 
                ? `Hết hạn: ${new Date(share.expires_at).toLocaleString('vi-VN')}`
                : 'Không giới hạn';
            
            const itemIcon = share.type === 'folder' ? 'ri-folder-line' : 'ri-file-line';
            const itemType = share.type === 'folder' ? 'Thư mục' : 'Tệp';
            
            html += `<div class="share-item ${isExpired ? 'expired' : ''}">`;
            
            if (share.is_public) {
                html += `
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="mb-2">
                                <i class="${itemIcon} text-muted mr-1"></i>
                                <strong>${share.item_name}</strong>
                                <span class="badge badge-secondary badge-sm ml-1">${itemType}</span>
                            </div>
                            <h6 class="mb-2"><i class="ri-link text-primary"></i> Link công khai</h6>
                            <div class="share-url-group mb-2">
                                <input type="text" class="form-control form-control-sm share-url-input" value="${share.share_url}" readonly id="share-url-${share.id}">
                                <button class="btn btn-sm btn-outline-secondary btn-copy" onclick="shareManager.copyToClipboard('share-url-${share.id}')">
                                    <i class="ri-file-copy-line"></i> Copy
                                </button>
                            </div>
                            <small class="text-muted">
                                <span class="badge badge-${share.permission === 'download' ? 'success' : 'info'}">${share.permission}</span>
                                ${isExpired ? '<span class="badge badge-danger ml-1">Đã hết hạn</span>' : ''}
                                <span class="ml-2">${expiryText}</span>
                            </small>
                        </div>
                        <button class="btn btn-sm btn-danger ml-2" onclick="shareManager.revokeShare(${share.id}, '${share.type}')" title="Thu hồi">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>
                `;
            } else {
                html += `
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="mb-2">
                                <i class="${itemIcon} text-muted mr-1"></i>
                                <strong>${share.item_name}</strong>
                                <span class="badge badge-secondary badge-sm ml-1">${itemType}</span>
                            </div>
                            <h6 class="mb-1"><i class="ri-user-line text-info"></i> ${share.shared_with.name}</h6>
                            <small class="text-muted d-block">${share.shared_with.email}</small>
                            <small class="text-muted mt-1 d-block">
                                <span class="badge badge-${share.permission === 'download' ? 'success' : 'info'}">${share.permission}</span>
                                ${isExpired ? '<span class="badge badge-danger ml-1">Đã hết hạn</span>' : ''}
                                <span class="ml-2">${expiryText}</span>
                            </small>
                        </div>
                        <button class="btn btn-sm btn-danger ml-2" onclick="shareManager.revokeShare(${share.id}, '${share.type}')" title="Thu hồi">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>
                `;
            }
            
            html += '</div>';
        });
        
        container.innerHTML = html;
    }

    async shareWithUser(email, permission) {
        try {
            const endpoint = this.currentItemType === 'file'
                ? `/cloody/files/${this.currentItemId}/share`
                : `/cloody/folders/${this.currentItemId}/share`;

            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    share_type: 'user',
                    email: email,
                    permission: permission
                })
            });

            const data = await response.json();
            
            if (data.success) {
                this.showAlert('success', data.message);
                document.getElementById('shareUserForm').reset();
                // Clear cache to force reload
                this.cachedShares = undefined;
                // Switch to current shares tab
                $('#current-shares-tab').tab('show');
            } else {
                this.showAlert('danger', data.message);
            }
        } catch (error) {
            console.error('Error sharing:', error);
            this.showAlert('danger', 'Có lỗi xảy ra khi chia sẻ');
        }
    }

    async createPublicLink(permission, expiresInDays) {
        try {
            const endpoint = this.currentItemType === 'file'
                ? `/cloody/files/${this.currentItemId}/share`
                : `/cloody/folders/${this.currentItemId}/share`;

            const body = {
                share_type: 'public',
                permission: permission
            };

            if (expiresInDays) {
                body.expires_in_days = parseInt(expiresInDays);
            }

            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(body)
            });

            const data = await response.json();
            
            if (data.success) {
                this.showAlert('success', data.message);
                document.getElementById('publicLinkForm').reset();
                // Clear cache to force reload
                this.cachedShares = undefined;
                // Switch to current shares tab
                $('#current-shares-tab').tab('show');
            } else {
                this.showAlert('danger', data.message);
            }
        } catch (error) {
            console.error('Error creating public link:', error);
            this.showAlert('danger', 'Có lỗi xảy ra khi tạo link công khai');
        }
    }

    async revokeShare(shareId, shareType) {
        if (!confirm('Bạn có chắc muốn thu hồi quyền chia sẻ này?')) {
            return;
        }

        try {
            const response = await fetch(`/cloody/shares/${shareId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    type: shareType || 'file'
                })
            });

            const data = await response.json();

            if (response.ok && data.success) {
                this.showAlert('success', data.message || 'Đã thu hồi quyền chia sẻ');
                // Clear cache to force reload
                this.cachedShares = undefined;
                this.loadShares();
            } else {
                this.showAlert('danger', data.message || 'Không thể thu hồi quyền chia sẻ');
            }
        } catch (error) {
            console.error('Error revoking share:', error);
            this.showAlert('danger', 'Có lỗi xảy ra khi thu hồi quyền chia sẻ');
        }
    }

    copyToClipboard(elementId) {
        const input = document.getElementById(elementId);
        if (!input) return;

        input.select();
        input.setSelectionRange(0, 99999);
        document.execCommand('copy');
        
        this.showAlert('success', 'Đã copy link vào clipboard!', 3000);
    }

    showAlert(type, message, timeout = 5000) {
        const container = document.getElementById('shareAlertContainer');
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;
        
        container.innerHTML = alertHtml;
        
        if (timeout > 0) {
            setTimeout(() => {
                container.innerHTML = '';
            }, timeout);
        }
    }
}

// Initialize ShareManager
const shareManager = new ShareManager();

// Form handlers
$(document).ready(function() {
    $('#shareUserForm').on('submit', function(e) {
        e.preventDefault();
        const email = $('#shareEmail').val();
        const permission = $('#sharePermission').val();
        shareManager.shareWithUser(email, permission);
    });

    $('#publicLinkForm').on('submit', function(e) {
        e.preventDefault();
        const permission = $('#publicPermission').val();
        const expiresInDays = $('#publicExpires').val();
        shareManager.createPublicLink(permission, expiresInDays);
    });
});
</script>

