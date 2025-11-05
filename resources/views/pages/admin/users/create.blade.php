@extends('layouts.app')

@section('title', 'Create User - Admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <div class="card card-block card-stretch card-height">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Create User</h4>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('admin.users.store') }}">
                        @csrf
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input id="password" type="password" class="form-control" name="password" required>
                        </div>
                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="is_admin" name="is_admin" value="1" {{ old('is_admin') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_admin">Is Admin</label>
                        </div>
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary mr-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
