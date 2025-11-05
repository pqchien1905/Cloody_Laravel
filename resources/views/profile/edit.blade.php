@extends('layouts.app')

@section('title', 'Edit Profile')

@push('styles')
<style>
    .card {
        margin-bottom: 1.5rem !important;
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        height: auto !important;
    }
    .card-block {
        display: block !important;
    }
    .card-stretch {
        display: block !important;
    }
    .card-height {
        height: auto !important;
        min-height: auto !important;
    }
    .card-body {
        padding: 1.5rem !important;
        display: block !important;
    }
    .form-group {
        margin-bottom: 1rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            @if(session('status'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Profile updated successfully!
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <!-- Update Profile Information -->
            <div class="card mb-3">
                <div class="card-header">
                    <h4 class="card-title">Profile Information</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')
                        
                        <!-- Avatar Upload -->
                        <div class="form-group text-center">
                            <div class="mb-3">
                                <img src="{{ $user->avatar ? asset($user->avatar) : asset('assets/images/user/1.jpg') }}" 
                                     alt="avatar" 
                                     class="avatar-130 img-fluid rounded-circle" 
                                     id="avatarPreview">
                            </div>
                            <div class="custom-file" style="max-width: 400px; margin: 0 auto;">
                                <input type="file" class="custom-file-input @error('avatar') is-invalid @enderror" id="avatar" name="avatar" accept="image/*" onchange="previewAvatar(event)">
                                <label class="custom-file-label" for="avatar">Choose avatar</label>
                                @error('avatar')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted d-block mt-2">Max 2MB (JPG, PNG, GIF)</small>
                        </div>

                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if ($user->email_verified_at === null)
                                <small class="text-warning">Your email address is unverified.</small>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+1 234 567 890">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="2" placeholder="Your address">{{ old('address', $user->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="bio">Bio</label>
                            <textarea class="form-control @error('bio') is-invalid @enderror" id="bio" name="bio" rows="4" placeholder="Tell us about yourself...">{{ old('bio', $user->bio) }}</textarea>
                            @error('bio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Max 1000 characters</small>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('cloudbox.user.profile') }}" class="btn btn-secondary mr-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewAvatar(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatarPreview').src = e.target.result;
        }
        reader.readAsDataURL(file);
        
        // Update label
        const fileName = file.name;
        event.target.nextElementSibling.textContent = fileName;
    }
}

// Bootstrap custom file input label update
$('.custom-file-input').on('change', function() {
    let fileName = $(this).val().split('\\').pop();
    $(this).next('.custom-file-label').html(fileName);
});
</script>
@endpush
