@extends('layouts.app')

@section('title', __('common.user_profile') . ' - Cloody')

@section('content')
<div class="container-fluid">
    @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            @if(session('status') == 'profile-updated')
                {{ __('common.profile_updated_successfully') }}
            @elseif(session('status') == 'password-updated')
                {{ __('common.password_updated_successfully') }}
            @endif
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    
    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body text-center">
                    <img src="{{ $user->avatar ? $user->avatar_url : asset('assets/images/user/1.jpg') }}" alt="profile-img" class="avatar-130 img-fluid rounded-circle" id="profileAvatar" onerror="this.onerror=null; this.src='{{ asset('assets/images/user/1.jpg') }}';">
                    <h4 class="mb-2 mt-3">{{ $user->name }}</h4>
                    <p class="mb-2">{{ Auth::user()->email }}</p>
                    @if(Auth::user()->phone)
                        <p class="mb-2"><i class="las la-phone mr-1"></i>{{ Auth::user()->phone }}</p>
                    @endif
                    @if(Auth::user()->address)
                        <p class="mb-2 text-muted"><i class="las la-map-marker mr-1"></i>{{ Str::limit(Auth::user()->address, 50) }}</p>
                    @endif
                    <p class="text-muted">{{ __('common.member_since') }} {{ Auth::user()->created_at->timezone('Asia/Ho_Chi_Minh')->format('m/Y') }}</p>
                    <p class="mb-2"><strong>{{ $totalFiles }}</strong> {{ __('common.files') }} • <strong>{{ $totalFolders }}</strong> {{ __('common.folders') }}</p>
                    @if(Auth::user()->bio)
                        <p class="mt-3 text-muted" style="font-size: 14px;">{{ Str::limit(Auth::user()->bio, 100) }}</p>
                    @endif
                    <div class="mt-3">
                        <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-sm">{{ __('common.edit_profile') }}</a>
                        <a href="{{ route('cloody.dashboard') }}" class="btn btn-secondary btn-sm">{{ __('common.dashboard') }}</a>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('common.storage_info') }}</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <p class="mb-2">{{ __('common.used') }}: <strong>{{ number_format($storageUsedGB, 2) }} GB</strong> {{ __('common.of') }} {{ $storageLimit }} GB</p>
                        <div class="progress">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ number_format($storagePercent, 1) }}%" aria-valuenow="{{ $storagePercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="ri-folder-line mr-2 text-primary"></i> {{ __('common.documents') }}: {{ number_format($documentsGB, 2) }} GB</li>
                        <li class="mb-2"><i class="ri-image-line mr-2 text-success"></i> {{ __('common.photos') }}: {{ number_format($imagesGB, 2) }} GB</li>
                        <li class="mb-2"><i class="ri-video-line mr-2 text-danger"></i> {{ __('common.videos') }}: {{ number_format($videosGB, 2) }} GB</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('common.profile_information') }}</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <h6 class="mb-0">{{ __('common.full_name') }}</h6>
                        </div>
                        <div class="col-sm-9 text-secondary">
                            {{ Auth::user()->name }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <h6 class="mb-0">{{ __('common.email') }}</h6>
                        </div>
                        <div class="col-sm-9 text-secondary">
                            {{ Auth::user()->email }}
                        </div>
                    </div>
                    @if(Auth::user()->phone)
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <h6 class="mb-0">{{ __('common.phone') }}</h6>
                        </div>
                        <div class="col-sm-9 text-secondary">
                            {{ Auth::user()->phone }}
                        </div>
                    </div>
                    @endif
                    @if(Auth::user()->address)
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <h6 class="mb-0">{{ __('common.address') }}</h6>
                        </div>
                        <div class="col-sm-9 text-secondary">
                            {{ Auth::user()->address }}
                        </div>
                    </div>
                    @endif
                    @if(Auth::user()->bio)
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <h6 class="mb-0">{{ __('common.bio') }}</h6>
                        </div>
                        <div class="col-sm-9 text-secondary">
                            {{ Auth::user()->bio }}
                        </div>
                    </div>
                    @endif
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <h6 class="mb-0">{{ __('common.joined') }}</h6>
                        </div>
                        <div class="col-sm-9 text-secondary">
                            {{ Auth::user()->created_at->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y') }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <a href="{{ route('profile.edit') }}" class="btn btn-primary">{{ __('common.edit_profile') }}</a>
                            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#updatePasswordModal">{{ __('common.update_password') }}</button>
                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteAccountModal">{{ __('common.delete_account') }}</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('common.recent_activity') }}</h4>
                </div>
                <div class="card-body">
                    @if($recentFiles->count() > 0 || $recentFolders->count() > 0)
                    <div class="iq-timeline">
                        @foreach($recentFiles as $file)
                        <div class="timeline-item">
                            <div class="timeline-icon"><i class="ri-file-line text-primary"></i></div>
                            <div class="timeline-content">
                                <h6>{{ __('common.uploaded') }}: {{ Str::limit($file->original_name, 30) }}</h6>
                                <p class="text-muted mb-0">{{ $file->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        @endforeach
                        @foreach($recentFolders as $folder)
                        <div class="timeline-item">
                            <div class="timeline-icon"><i class="ri-folder-add-line text-success"></i></div>
                            <div class="timeline-content">
                                <h6>{{ __('common.created_folder') }}: {{ $folder->name }}</h6>
                                <p class="text-muted mb-0">{{ $folder->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-center text-muted py-4">{{ __('common.no_recent_activity') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Update Password Modal -->
<div class="modal fade" id="updatePasswordModal" tabindex="-1" role="dialog" aria-labelledby="updatePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updatePasswordModalLabel">{{ __('common.update_password') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="current_password">{{ __('common.current_password') }}</label>
                        <input type="password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" id="current_password" name="current_password" required>
                        @error('current_password', 'updatePassword')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">{{ __('common.new_password') }}</label>
                        <input type="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" id="password" name="password" required>
                        @error('password', 'updatePassword')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">{{ __('common.confirm_password') }}</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('common.update_password') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Account Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" role="dialog" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title text-white" id="deleteAccountModalLabel">{{ __('common.delete_account') }}</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('profile.destroy') }}">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="ri-error-warning-line mr-2"></i>
                        <strong>{{ __('common.warning') }}:</strong> {{ __('common.warning_action_cannot_undo') }}
                    </div>
                    <p>{{ __('common.delete_account_confirm') }}</p>
                    <p>{{ __('common.enter_password_to_confirm_delete') }}</p>
                    
                    <div class="form-group">
                        <label for="password_delete">{{ __('common.password') }}</label>
                        <input type="password" class="form-control @error('password', 'userDeletion') is-invalid @enderror" id="password_delete" name="password" required>
                        @error('password', 'userDeletion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('common.delete_account') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto open password modal if there are password update errors
    @if($errors->updatePassword->any())
        $('#updatePasswordModal').modal('show');
    @endif
    
    // Auto open delete account modal if there are user deletion errors
    @if($errors->userDeletion->any())
        $('#deleteAccountModal').modal('show');
    @endif
</script>
@endpush
