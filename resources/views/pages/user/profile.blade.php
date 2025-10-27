@extends('layouts.app')

@section('title', 'User Profile')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body text-center">
                    <img src="{{ Auth::user()->avatar ?? asset('assets/images/user/1.jpg') }}" alt="profile-img" class="avatar-130 img-fluid rounded-circle">
                    <h4 class="mb-2 mt-3">{{ Auth::user()->name }}</h4>
                    <p class="mb-2">{{ Auth::user()->email }}</p>
                    <p class="text-muted">Member since {{ Auth::user()->created_at->format('M Y') }}</p>
                    <div class="mt-3">
                        <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-sm">Edit Profile</a>
                        <a href="{{ route('cloudbox.dashboard') }}" class="btn btn-secondary btn-sm">Dashboard</a>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Storage Info</h4>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <p class="mb-2">Used: <strong>15.5 GB</strong> of 100 GB</p>
                        <div class="progress">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 15.5%" aria-valuenow="15.5" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="ri-folder-line mr-2 text-primary"></i> Documents: 5.2 GB</li>
                        <li class="mb-2"><i class="ri-image-line mr-2 text-success"></i> Photos: 8.3 GB</li>
                        <li class="mb-2"><i class="ri-video-line mr-2 text-danger"></i> Videos: 2 GB</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Profile Information</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <h6 class="mb-0">Full Name</h6>
                        </div>
                        <div class="col-sm-9 text-secondary">
                            {{ Auth::user()->name }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <h6 class="mb-0">Email</h6>
                        </div>
                        <div class="col-sm-9 text-secondary">
                            {{ Auth::user()->email }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3">
                            <h6 class="mb-0">Joined</h6>
                        </div>
                        <div class="col-sm-9 text-secondary">
                            {{ Auth::user()->created_at->format('F d, Y') }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <a href="{{ route('profile.edit') }}" class="btn btn-primary">Edit Profile</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Recent Activity</h4>
                </div>
                <div class="card-body">
                    <div class="iq-timeline">
                        <div class="timeline-item">
                            <div class="timeline-icon"><i class="ri-upload-line text-primary"></i></div>
                            <div class="timeline-content">
                                <h6>Uploaded 3 files</h6>
                                <p class="text-muted mb-0">2 hours ago</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-icon"><i class="ri-folder-add-line text-success"></i></div>
                            <div class="timeline-content">
                                <h6>Created new folder "Projects"</h6>
                                <p class="text-muted mb-0">1 day ago</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-icon"><i class="ri-share-line text-info"></i></div>
                            <div class="timeline-content">
                                <h6>Shared document with team</h6>
                                <p class="text-muted mb-0">3 days ago</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
