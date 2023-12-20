<div class="modal fade {{ $name }}" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-{{ $type }}">
                <h4 class="modal-title"><i class="til_img"></i><strong>{{ $title }}</strong></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>

            <div class="modal-body with-padding" data-select2-dropdown-parent>
                <div>{!! $content !!}</div>
            </div>

            <div class="modal-footer">
                <button type="button" class="float-start btn btn-warning" data-bs-dismiss="modal">{{ trans('core/table::table.cancel') }}</button>
                <button class="float-end btn btn-{{ $type }} {{ Arr::get($action_button_attributes, 'class') }}" {!! Html::attributes(Arr::except($action_button_attributes, 'class')) !!}>{{ $action_name }}</button>
            </div>
        </div>
    </div>
</div>
<!-- end Modal -->
