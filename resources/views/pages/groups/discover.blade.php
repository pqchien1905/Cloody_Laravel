@extends('layouts.app')

@section('title', 'Khám phá nhóm - Cloody')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0"><i class="ri-compass-3-line mr-2"></i>Khám phá nhóm công khai</h4>
                    <p class="text-muted mb-0">Tìm và tham gia các nhóm công khai</p>
                </div>
                <a href="{{ route('groups.index') }}" class="btn btn-outline-secondary">
                    <i class="ri-arrow-left-line mr-1"></i> Nhóm của tôi
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    <div class="row">
        @forelse($publicGroups as $group)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        @if($group->avatar)
                            <img src="{{ $group->avatar ? $group->avatar_url : asset('assets/images/user/1.jpg') }}" alt="{{ $group->name }}" 
                                 class="avatar-60 rounded-circle mr-3">
                        @else
                            <div class="avatar-60 rounded-circle bg-success-light text-success 
                                        d-flex align-items-center justify-content-center mr-3">
                                <i class="ri-group-line font-size-24"></i>
                            </div>
                        @endif
                        <div class="flex-grow-1">
                            <h6 class="mb-0">{{ $group->name }}</h6>
                            <small class="text-muted">
                                <i class="ri-earth-line mr-1"></i>Công khai
                            </small>
                        </div>
                    </div>
                    
                    <p class="text-muted mb-3">{{ Str::limit($group->description, 100) }}</p>
                    
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="text-muted">
                            <i class="ri-user-line mr-1"></i>{{ $group->members->count() }} thành viên
                        </div>
                        <div class="text-muted">
                            <small>Chủ nhóm: {{ $group->owner->name }}</small>
                        </div>
                    </div>

                    <div class="d-flex">
                        <a href="{{ route('groups.show', $group) }}" class="btn btn-outline-primary btn-sm mr-2">
                            <i class="ri-eye-line mr-1"></i>Xem
                        </a>
                        <form action="{{ route('groups.request-join', $group) }}" method="POST" class="flex-grow-1">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-sm btn-block">
                                <i class="ri-user-add-line mr-1"></i>Tham gia
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="ri-group-line font-size-48 text-muted mb-3"></i>
                    <h5 class="text-muted">Không tìm thấy nhóm công khai nào</h5>
                    <p class="text-muted mb-4">Hãy tạo nhóm mới hoặc quay lại sau</p>
                    <a href="{{ route('groups.create') }}" class="btn btn-primary">
                        <i class="ri-add-line mr-1"></i> Tạo nhóm mới
                    </a>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($publicGroups->hasPages())
    <div class="row mt-4">
        <div class="col-12">
            <div class="d-flex justify-content-center">
                {{ $publicGroups->links() }}
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
