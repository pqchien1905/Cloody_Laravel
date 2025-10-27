@extends('layouts.app')

@section('title', 'Shared File')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @if(isset($share) && $share->file)
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ $share->file->original_name }}</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Type:</strong> {{ strtoupper($share->file->extension) }}</p>
                        <p><strong>Size:</strong> {{ $share->file->formatted_size }}</p>
                        <p><strong>Shared by:</strong> {{ $share->sharedBy->name ?? 'Unknown' }}</p>
                        <a href="{{ route('cloudbox.files.download', $share->file->id) }}" class="btn btn-primary">Download</a>
                        <a href="{{ route('cloudbox.files.view', $share->file->id) }}" class="btn btn-outline-info ml-2">View</a>
                    </div>
                </div>
            @else
                <div class="alert alert-danger">File not found or share link invalid.</div>
            @endif
        </div>
    </div>
</div>
@endsection
