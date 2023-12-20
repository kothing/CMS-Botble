<div class="modal fade" id="avatar-modal" tabindex="-1" role="dialog" aria-labelledby="avatar-modal-label"
     aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form class="avatar-form" method="post" action="{{ route('public.member.avatar') }}" enctype="multipart/form-data">
                <div class="modal-header">
                    <h4 class="modal-title" id="avatar-modal-label"><i class="til_img"></i><strong>{{ trans('plugins/member::dashboard.change_profile_image') }}</strong></h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    <div class="avatar-body">

                        <!-- Upload image and data -->
                        <div class="avatar-upload">
                            <input class="avatar-src" name="avatar_src" type="hidden">
                            <input class="avatar-data" name="avatar_data" type="hidden">
                            @csrf
                            <label for="avatarInput">{{ trans('plugins/member::dashboard.new_image') }}</label>
                            <input class="avatar-input" id="avatarInput" name="avatar_file" type="file">
                        </div>

                        <div class="loading" tabindex="-1" role="img" aria-label="{{ trans('plugins/member::dashboard.loading') }}"></div>

                        <!-- Crop and preview -->
                        <div class="row">
                            <div class="col-md-9">
                                <div class="avatar-wrapper"></div>
                            </div>
                            <div class="col-md-3 avatar-preview-wrapper">
                                <div class="avatar-preview preview-lg"></div>
                                <div class="avatar-preview preview-md"></div>
                                <div class="avatar-preview preview-sm"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">{{ trans('plugins/member::dashboard.close') }}</button>
                    <button class="btn btn-primary avatar-save" type="submit">{{ trans('plugins/member::dashboard.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div><!-- /.modal -->
