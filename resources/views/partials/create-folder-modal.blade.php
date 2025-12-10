<!-- Create Folder Modal -->
<div class="modal fade" id="createFolderModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ri-folder-add-line"></i> {{ __('common.create_new_folder') }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('cloody.folders.store') }}" method="POST">
                @csrf
                <input type="hidden" name="parent_id" value="{{ $currentFolderId ?? null }}">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="folder_name">{{ __('common.folder_name') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="folder_name" name="name" placeholder="{{ __('common.enter_folder_name') }}" required autofocus>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="folder_color">{{ __('common.folder_color') }}</label>
                        <input type="color" class="form-control" id="folder_color" name="color" value="#3498db" style="height: 45px;">
                        <small class="form-text text-muted">{{ __('common.choose_color_for_folder_icon') }}</small>
                    </div>
                    <div class="form-group">
                        <label for="folder_description">{{ __('common.description_optional') }}</label>
                        <textarea class="form-control" id="folder_description" name="description" 
                                  rows="3" placeholder="{{ __('common.add_description_for_folder') }}"></textarea>
                    </div>
                    <div class="form-group">
                        <label>{{ __('common.privacy_settings') }} <span class="text-danger">*</span></label>
                        <div class="custom-control custom-radio mb-2">
                            <input type="radio" id="create_folder_privacy_private" name="is_public" value="0" 
                                   class="custom-control-input" checked>
                            <label class="custom-control-label" for="create_folder_privacy_private">
                                <i class="ri-lock-line"></i> {{ __('common.private') }}
                                <small class="d-block text-muted">{{ __('common.only_you_can_access_this_folder') }}</small>
                            </label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" id="create_folder_privacy_public" name="is_public" value="1" 
                                   class="custom-control-input">
                            <label class="custom-control-label" for="create_folder_privacy_public">
                                <i class="ri-global-line"></i> {{ __('common.public') }}
                                <small class="d-block text-muted">{{ __('common.anyone_with_link_can_view') }}</small>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('common.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-folder-add-line"></i> {{ __('common.create_folder') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
