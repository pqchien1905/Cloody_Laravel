@extends('layouts.app')

@section('title', __('common.shared_file'))

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
                        <p><strong>{{ __('common.file_type_label') }}:</strong> {{ strtoupper($share->file->extension) }}</p>
                        <p><strong>{{ __('common.file_size_label') }}:</strong> {{ $share->file->formatted_size }}</p>
                        <p><strong>{{ __('common.shared_by_label') }}:</strong> {{ $share->sharedBy->name ?? __('common.unknown_user') }}</p>
                        <a href="{{ route('cloody.files.download', $share->file->id) }}" class="btn btn-primary">{{ __('common.download') }}</a>
                        <a href="{{ route('cloody.files.view', $share->file->id) }}" class="btn btn-outline-info ml-2">{{ __('common.view') }}</a>
                    </div>
                </div>
            @else
                <div class="alert alert-danger">{{ __('common.file_not_found_or_invalid_link') }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
