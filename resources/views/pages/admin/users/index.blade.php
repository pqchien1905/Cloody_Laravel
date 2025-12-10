@extends('layouts.app')

@section('title', __('common.manage_users') . ' - Admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12 d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">{{ __('common.users') }}</h4>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createUserModal"><i class="las la-plus mr-1"></i> {{ __('common.new_user') }}</button>
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
                            <label for="search" class="small text-muted mb-1">{{ __('common.search') }}</label>
                            <input type="text" class="form-control" id="search" name="search" placeholder="{{ __('common.name_or_email') }}" value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3 mb-2 mb-md-0">
                            <label for="role" class="small text-muted mb-1">{{ __('common.role') }}</label>
                            <select class="form-control" id="role" name="role">
                                <option value="">{{ __('common.all_users') }}</option>
                                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>{{ __('common.admin_only') }}</option>
                                <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>{{ __('common.regular_users') }}</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-2 mb-md-0">
                            <label for="sort" class="small text-muted mb-1">{{ __('common.sort_by') }}</label>
                            <select class="form-control" id="sort" name="sort">
                                <option value="created_desc" {{ request('sort') === 'created_desc' ? 'selected' : '' }}>{{ __('common.newest_first') }}</option>
                                <option value="created_asc" {{ request('sort') === 'created_asc' ? 'selected' : '' }}>{{ __('common.oldest_first') }}</option>
                                <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>{{ __('common.name_a_z') }}</option>
                                <option value="name_desc" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>{{ __('common.name_z_a') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-block"><i class="las la-search mr-1"></i> {{ __('common.filter') }}</button>
                        </div>
                    </form>
                    @if(request()->hasAny(['search', 'role', 'sort']))
                        <div class="mt-2">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary"><i class="las la-times mr-1"></i> {{ __('common.clear_filters') }}</a>
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
                                    <th>{{ __('common.avatar') }}</th>
                                    <th>{{ __('common.name') }}</th>
                                    <th>{{ __('common.email') }}</th>
                                    <th>{{ __('common.administrator') }}</th>
                                    <th>{{ __('common.created') }}</th>
                                    <th class="text-right">{{ __('common.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                <tr>
                                    <td>
                                        @if($user->avatar)
                                            <img src="{{ $user->avatar ? $user->avatar_url : asset('assets/images/user/1.jpg') }}" alt="avatar" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;" onerror="this.src='{{ asset('assets/images/user/1.jpg') }}'">
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
                                            <span class="badge badge-success">{{ __('common.yes') }}</span>
                                        @else
                                            <span class="badge badge-secondary">{{ __('common.no') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->created_at?->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i') }}</td>
                                    <td class="text-right">
                                        <button type="button" class="btn btn-sm btn-outline-secondary" data-toggle="modal" data-target="#editUserModal{{ $user->id }}"><i class="las la-edit"></i></button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-toggle="modal" data-target="#deleteUserModal{{ $user->id }}"><i class="las la-trash"></i></button>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center py-4 text-muted">{{ __('common.no_users_found') }}</td></tr>
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
                <h5 class="modal-title" id="createUserModalLabel">{{ __('common.create_new_user') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="create_name">{{ __('common.name') }}</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="create_name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="create_email">{{ __('common.email') }}</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="create_email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="create_password">{{ __('common.password') }}</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="create_password" name="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">{{ __('common.minimum_characters') }}</small>
                    </div>

                    <div class="form-group">
                        <label for="create_password_confirmation">{{ __('common.confirm_password') }}</label>
                        <input type="password" class="form-control" id="create_password_confirmation" name="password_confirmation" required>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="create_is_admin" name="is_admin" value="1" {{ old('is_admin') ? 'checked' : '' }}>
                            <label class="custom-control-label" for="create_is_admin">{{ __('common.administrator') }}</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('common.create_user') }}</button>
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
                <h5 class="modal-title" id="editUserModalLabel{{ $user->id }}">{{ __('common.edit_user') }}: {{ $user->name }}</h5>
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
                        <label for="name{{ $user->id }}">{{ __('common.name') }}</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name{{ $user->id }}" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="email{{ $user->id }}">{{ __('common.email') }}</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email{{ $user->id }}" name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password{{ $user->id }}">{{ __('common.password_leave_blank') }}</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password{{ $user->id }}" name="password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">{{ __('common.leave_blank_no_change') }}</small>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="is_admin{{ $user->id }}" name="is_admin" value="1" {{ old('is_admin', $user->is_admin) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_admin{{ $user->id }}">{{ __('common.administrator') }}</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('common.update_user') }}</button>
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
                <h5 class="modal-title text-white" id="deleteUserModalLabel{{ $user->id }}">{{ __('common.delete_user') }}</h5>
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
                        <strong>{{ __('common.warning') }}:</strong> {{ __('common.warning_action_cannot_undo') }}
                    </div>
                    <p>{{ __('common.delete_user_confirmation') }} <strong>{{ $user->name }}</strong> ({{ $user->email }})?</p>
                    <p class="text-muted">{{ __('common.all_data_will_be_deleted') }}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('common.delete_user') }}</button>
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
