@extends('layouts.app')

@section('title', 'Tạo nhóm mới - Cloody')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <!-- Page Header -->
            <div class="mb-4">
                <a href="{{ route('groups.index') }}" class="btn btn-outline-secondary btn-sm mb-3">
                    <i class="ri-arrow-left-line mr-1"></i> Quay lại
                </a>
                <h4 class="mb-0">Tạo nhóm mới</h4>
                <p class="text-muted">Tạo nhóm để cộng tác và chia sẻ tệp với mọi người</p>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('groups.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Group Name -->
                        <div class="form-group">
                            <label for="name">Tên nhóm <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" 
                                   placeholder="Nhập tên nhóm" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="form-group">
                            <label for="description">Mô tả nhóm</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4" 
                                      placeholder="Mô tả về mục đích và hoạt động của nhóm">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Tối đa 1000 ký tự</small>
                        </div>

                        <!-- Privacy Setting -->
                        <div class="form-group">
                            <label>Quyền riêng tư <span class="text-danger">*</span></label>
                            <div class="custom-control custom-radio mb-2">
                                <input type="radio" id="privacy_private" name="privacy" 
                                       class="custom-control-input" value="private" 
                                       {{ old('privacy', 'private') === 'private' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="privacy_private">
                                    <i class="ri-lock-line mr-1"></i> <strong>Riêng tư</strong>
                                    <br>
                                    <small class="text-muted">Chỉ thành viên mới có thể xem và truy cập nhóm</small>
                                </label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="privacy_public" name="privacy" 
                                       class="custom-control-input" value="public"
                                       {{ old('privacy') === 'public' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="privacy_public">
                                    <i class="ri-earth-line mr-1"></i> <strong>Công khai</strong>
                                    <br>
                                    <small class="text-muted">Mọi người có thể xem nhóm (cần phê duyệt để tham gia)</small>
                                </label>
                            </div>
                            @error('privacy')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Group Avatar -->
                        <div class="form-group">
                            <label for="avatar">Ảnh đại diện nhóm</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input @error('avatar') is-invalid @enderror" 
                                       id="avatar" name="avatar" accept="image/*">
                                <label class="custom-file-label" for="avatar">Chọn ảnh...</label>
                            </div>
                            @error('avatar')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Định dạng: JPG, PNG, GIF. Kích thước tối đa: 2MB</small>
                            
                            <!-- Preview -->
                            <div id="avatar-preview" class="mt-3" style="display: none;">
                                <img id="preview-image" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px;">
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="form-group mb-0 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-save-line mr-1"></i> Tạo nhóm
                            </button>
                            <a href="{{ route('groups.index') }}" class="btn btn-light ml-2">
                                Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // File input label update
    const customFileInput = document.querySelector('.custom-file-input');
    if (customFileInput) {
        customFileInput.addEventListener('change', function(e) {
            var fileName = e.target.files[0].name;
            var label = e.target.nextElementSibling;
            if (label) label.innerText = fileName;
            
            // Show preview
            if (e.target.files && e.target.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('avatar-preview');
                    const previewImage = document.getElementById('preview-image');
                    if (preview) preview.style.display = 'block';
                    if (previewImage) previewImage.src = e.target.result;
                }
                reader.readAsDataURL(e.target.files[0]);
            }
        });
    }
</script>
@endpush
@endsection
