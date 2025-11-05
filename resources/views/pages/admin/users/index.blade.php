@extends('layouts.app')

@section('title', 'Manage Users - Admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12 d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Users</h4>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createUserModal"><i class="las la-plus mr-1"></i> New User</button>
        </div>

        <div class="col-12">
            @if(session('status'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('status') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            @endif

            <!-- Search and Filter -->
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.users.index') }}" class="row align-items-end">
                        <div class="col-md-4 mb-2 mb-md-0">
                            <label for="search" class="small text-muted mb-1">Search</label>
                            <input type="text" class="form-control" id="search" name="search" placeholder="Name or email..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3 mb-2 mb-md-0">
                            <label for="role" class="small text-muted mb-1">Role</label>
                            <select class="form-control" id="role" name="role">
                                <option value="">All Users</option>
                                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin Only</option>
                                <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>Regular Users</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2 mb-md-0">
                            <label for="sort" class="small text-muted mb-1">Sort By</label>
                            <select class="form-control" id="sort" name="sort">
                                <option value="created_desc" {{ request('sort') === 'created_desc' ? 'selected' : '' }}>Newest First</option>
                                <option value="created_asc" {{ request('sort') === 'created_asc' ? 'selected' : '' }}>Oldest First</option>
                                <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Name A-Z</option>
                                <option value="name_desc" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>Name Z-A</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-block"><i class="las la-search mr-1"></i> Filter</button>
                        </div>
                    </form>
                    @if(request()->hasAny(['search', 'role', 'sort']))
                        <div class="mt-2">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary"><i class="las la-times mr-1"></i> Clear Filters</a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card card-block card-stretch card-height">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0 table-borderless">
                            <thead>
                                <tr>
                                    <th>Avatar</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Admin</th>
                                    <th>Created</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                <tr>
                                    <td>
                                        @if($user->avatar)
                                            <img src="{{ asset($user->avatar) }}" alt="avatar" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                        @else
                                            <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-size: 16px; font-weight: 600;">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if($user->is_admin)
                                            <span class="badge badge-success">Yes</span>
                                        @else
                                            <span class="badge badge-secondary">No</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->created_at?->format('Y-m-d') }}</td>
                                    <td class="text-right">
                                        <button type="button" class="btn btn-sm btn-outline-secondary" data-toggle="modal" data-target="#editUserModal{{ $user->id }}"><i class="las la-edit"></i></button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-toggle="modal" data-target="#deleteUserModal{{ $user->id }}"><i class="las la-trash"></i></button>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center py-4 text-muted">No users found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if(method_exists($users, 'links'))
                    <div class="card-footer">{{ $users->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Create User Modal -->
<div class="modal fade" id="createUserModal" tabindex="-1" role="dialog" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createUserModalLabel">Create New User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="create_name">Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="create_name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="create_email">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="create_email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="create_password">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="create_password" name="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Minimum 8 characters</small>
                    </div>

                    <div class="form-group">
                        <label for="create_password_confirmation">Confirm Password</label>
                        <input type="password" class="form-control" id="create_password_confirmation" name="password_confirmation" required>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="create_is_admin" name="is_admin" value="1" {{ old('is_admin') ? 'checked' : '' }}>
                            <label class="custom-control-label" for="create_is_admin">Administrator</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modals -->
@foreach($users as $user)
<div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel{{ $user->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel{{ $user->id }}">Edit User: {{ $user->name }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="user_id" value="{{ $user->id }}">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name{{ $user->id }}">Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name{{ $user->id }}" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email{{ $user->id }}">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email{{ $user->id }}" name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password{{ $user->id }}">Password (leave blank to keep current)</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password{{ $user->id }}" name="password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Leave blank if you don't want to change password</small>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="is_admin{{ $user->id }}" name="is_admin" value="1" {{ old('is_admin', $user->is_admin) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_admin{{ $user->id }}">Administrator</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete User Modal -->
<div class="modal fade" id="deleteUserModal{{ $user->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteUserModalLabel{{ $user->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title text-white" id="deleteUserModalLabel{{ $user->id }}">Delete User</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="ri-error-warning-line mr-2"></i>
                        <strong>Warning:</strong> This action cannot be undone!
                    </div>
                    <p>Are you sure you want to delete user <strong>{{ $user->name }}</strong> ({{ $user->email }})?</p>
                    <p class="text-muted">All files, folders, and data associated with this user will be permanently deleted.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete User</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection

@push('scripts')
<script>
    // Auto open create modal if there are validation errors for new user
    @if($errors->any() && !old('_method'))
        $('#createUserModal').modal('show');
    @endif

    // Auto open edit modal if there are validation errors
    @if($errors->any() && old('_method') === 'PUT')
        @foreach($users as $user)
            @if(old('user_id') == $user->id || request()->route('user') == $user->id)
                $('#editUserModal{{ $user->id }}').modal('show');
            @endif
        @endforeach
    @endif
</script>
@endpush
