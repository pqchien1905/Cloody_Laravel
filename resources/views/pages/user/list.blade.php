@extends('layouts.app')

@section('title', 'User List')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">User List</h4>
                    </div>
                    <div>
                        <a href="{{ route('cloudbox.user.add') }}" class="btn btn-primary">
                            <i class="ri-add-line"></i> Add New User
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Joined</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('assets/images/user/1.jpg') }}" class="avatar-40 rounded-circle mr-3" alt="user">
                                            <div>Admin User</div>
                                        </div>
                                    </td>
                                    <td>admin@cloudbox.com</td>
                                    <td><span class="badge badge-primary">Admin</span></td>
                                    <td>Jan 2024</td>
                                    <td><span class="badge badge-success">Active</span></td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-primary">Edit</a>
                                        <button class="btn btn-sm btn-danger">Delete</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('assets/images/user/2.jpg') }}" class="avatar-40 rounded-circle mr-3" alt="user">
                                            <div>Test User</div>
                                        </div>
                                    </td>
                                    <td>test@cloudbox.com</td>
                                    <td><span class="badge badge-info">User</span></td>
                                    <td>Jan 2024</td>
                                    <td><span class="badge badge-success">Active</span></td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-primary">Edit</a>
                                        <button class="btn btn-sm btn-danger">Delete</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
