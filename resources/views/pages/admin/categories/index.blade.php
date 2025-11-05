@extends('layouts.app')

@section('title', 'Manage Categories - Admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12 d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">File Categories</h4>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createCategoryModal"><i class="las la-plus mr-1"></i> New Category</button>
        </div>

        <div class="col-12">
            @if(session('status'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('status') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif

            <!-- Search and Filter -->
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.categories.index') }}" class="row align-items-end">
                        <div class="col-md-6 mb-2 mb-md-0">
                            <label for="search" class="small text-muted mb-1">Search</label>
                            <input type="text" class="form-control" id="search" name="search" placeholder="Category name or description..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4 mb-2 mb-md-0">
                            <label for="status" class="small text-muted mb-1">Status</label>
                            <select class="form-control" id="status" name="status">
                                <option value="">All Categories</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Only</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive Only</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-block"><i class="las la-search mr-1"></i> Filter</button>
                        </div>
                    </form>
                    @if(request()->hasAny(['search', 'status']))
                        <div class="mt-2">
                            <a href="{{ route('admin.categories.index') }}" class="btn btn-sm btn-outline-secondary"><i class="las la-times mr-1"></i> Clear Filters</a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0 table-borderless">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">Order</th>
                                    <th>Icon</th>
                                    <th>Name</th>
                                    <th>Extensions</th>
                                    <th>Status</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $category)
                                <tr>
                                    <td><span class="badge badge-light">{{ $category->order }}</span></td>
                                    <td>
                                        @if($category->icon)
                                            <div class="icon-small rounded d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: {{ $category->color }}20;">
                                                <i class="{{ $category->icon }} font-size-20" style="color: {{ $category->color }};"></i>
                                            </div>
                                        @else
                                            <div class="icon-small bg-secondary-light rounded d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="ri-folder-line font-size-20 text-secondary"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $category->name }}</strong>
                                        @if($category->description)
                                            <br><small class="text-muted">{{ Str::limit($category->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($category->extensions)
                                            @foreach(array_slice($category->extensions, 0, 5) as $ext)
                                                <span class="badge badge-info">{{ $ext }}</span>
                                            @endforeach
                                            @if(count($category->extensions) > 5)
                                                <span class="badge badge-secondary">+{{ count($category->extensions) - 5 }}</span>
                                            @endif
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($category->is_active)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <button type="button" class="btn btn-sm btn-outline-secondary" data-toggle="modal" data-target="#editCategoryModal{{ $category->id }}"><i class="las la-edit"></i></button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-toggle="modal" data-target="#deleteCategoryModal{{ $category->id }}"><i class="las la-trash"></i></button>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center py-4 text-muted">No categories found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if(method_exists($categories, 'links'))
                    <div class="card-footer">{{ $categories->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Create Category Modal -->
<div class="modal fade" id="createCategoryModal" tabindex="-1" role="dialog" aria-labelledby="createCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createCategoryModalLabel">Create New Category</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="create_name">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="create_name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="create_slug">Slug</label>
                                <input type="text" class="form-control @error('slug') is-invalid @enderror" id="create_slug" name="slug" value="{{ old('slug') }}" placeholder="Auto-generated if empty">
                                @error('slug')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Leave blank to auto-generate from name</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="create_icon">Icon Class</label>
                                <input type="text" class="form-control" id="create_icon" name="icon" value="{{ old('icon') }}" placeholder="e.g., ri-file-pdf-line">
                                <small class="text-muted">Remixicon class name</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="create_color">Color</label>
                                <input type="color" class="form-control" id="create_color" name="color" value="{{ old('color', '#667eea') }}" style="height: 38px;">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="create_order">Order</label>
                                <input type="number" class="form-control" id="create_order" name="order" value="{{ old('order', 0) }}" min="0">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="create_extensions">File Extensions</label>
                        <input type="text" class="form-control" id="create_extensions" name="extensions" value="{{ old('extensions') }}" placeholder="e.g., pdf, doc, docx">
                        <small class="text-muted">Comma-separated list of extensions</small>
                    </div>

                    <div class="form-group">
                        <label for="create_description">Description</label>
                        <textarea class="form-control" id="create_description" name="description" rows="3">{{ old('description') }}</textarea>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="create_is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="create_is_active">Active</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Category Modals -->
@foreach($categories as $category)
<div class="modal fade" id="editCategoryModal{{ $category->id }}" tabindex="-1" role="dialog" aria-labelledby="editCategoryModalLabel{{ $category->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCategoryModalLabel{{ $category->id }}">Edit Category: {{ $category->name }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.categories.update', $category) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="category_id" value="{{ $category->id }}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name{{ $category->id }}">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name{{ $category->id }}" name="name" value="{{ old('name', $category->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="slug{{ $category->id }}">Slug</label>
                                <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug{{ $category->id }}" name="slug" value="{{ old('slug', $category->slug) }}">
                                @error('slug')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="icon{{ $category->id }}">Icon Class</label>
                                <input type="text" class="form-control" id="icon{{ $category->id }}" name="icon" value="{{ old('icon', $category->icon) }}" placeholder="e.g., ri-file-pdf-line">
                                <small class="text-muted">Remixicon class name</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="color{{ $category->id }}">Color</label>
                                <input type="color" class="form-control" id="color{{ $category->id }}" name="color" value="{{ old('color', $category->color) }}" style="height: 38px;">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="order{{ $category->id }}">Order</label>
                                <input type="number" class="form-control" id="order{{ $category->id }}" name="order" value="{{ old('order', $category->order) }}" min="0">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="extensions{{ $category->id }}">File Extensions</label>
                        <input type="text" class="form-control" id="extensions{{ $category->id }}" name="extensions" value="{{ old('extensions', is_array($category->extensions) ? implode(', ', $category->extensions) : '') }}" placeholder="e.g., pdf, doc, docx">
                        <small class="text-muted">Comma-separated list of extensions</small>
                    </div>

                    <div class="form-group">
                        <label for="description{{ $category->id }}">Description</label>
                        <textarea class="form-control" id="description{{ $category->id }}" name="description" rows="3">{{ old('description', $category->description) }}</textarea>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="is_active{{ $category->id }}" name="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active{{ $category->id }}">Active</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Category Modal -->
<div class="modal fade" id="deleteCategoryModal{{ $category->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteCategoryModalLabel{{ $category->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title text-white" id="deleteCategoryModalLabel{{ $category->id }}">Delete Category</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="ri-error-warning-line mr-2"></i>
                        <strong>Warning:</strong> This action cannot be undone!
                    </div>
                    <p>Are you sure you want to delete category <strong>{{ $category->name }}</strong>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Category</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection

@push('scripts')
<script>
    // Auto open create modal if there are validation errors for new category
    @if($errors->any() && !old('_method'))
        $('#createCategoryModal').modal('show');
    @endif

    // Auto open edit modal if there are validation errors
    @if($errors->any() && old('_method') === 'PUT')
        @foreach($categories as $category)
            @if(old('category_id') == $category->id)
                $('#editCategoryModal{{ $category->id }}').modal('show');
            @endif
        @endforeach
    @endif
</script>
@endpush
