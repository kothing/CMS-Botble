<script id="_render_custom_field_field_group_template" type="text/x-custom-template">
    <div class="widget meta-boxes">
        <div class="widget-title">
            <h4><span>__title__</span></h4>
        </div>
        <div class="box-body meta-boxes-body widget-body"></div>
    </div>
</script>

<script id="_render_custom_field_global_skeleton_template" type="text/x-custom-template">
    <div class="meta-box field-__type__">
        <div class="title">
            <label class="control-label">__title__</label>
            <span class="help-block">__instructions__</span>
        </div>
        <div class="meta-box-wrap"></div>
    </div>
</script>

<script id="_render_custom_field_text_template" type="text/x-custom-template">
    <input type="text"
           value="__value__"
           placeholder="__placeholderText__"
           class="form-control">
</script>

<script id="_render_custom_field_number_template" type="text/x-custom-template">
    <input type="number"
           value="__value__"
           placeholder="__placeholderText__"
           class="form-control">
</script>

<script id="_render_custom_field_email_template" type="text/x-custom-template">
    <input type="email"
           value="__value__"
           placeholder="__placeholderText__"
           class="form-control">
</script>

<script id="_render_custom_field_password_template" type="text/x-custom-template">
    <input type="password"
           value="__value__"
           placeholder="__placeholderText__"
           class="form-control">
</script>

<script id="_render_custom_field_textarea_template" type="text/x-custom-template">
    <textarea rows="__rows__"
              placeholder="__placeholderText__"
              class="form-control">__value__</textarea>
</script>

<script id="_render_custom_field_checkbox_template" type="text/x-custom-template">
    <div class="clearfix">
        <label class="mt-checkbox mt-checkbox-outline">
            <input type="checkbox"
                   __checked__
                   value="__value__">
            <span></span>
            __title__
        </label>
    </div>
</script>

<script id="_render_custom_field_radio_template" type="text/x-custom-template">
    <div class="clearfix">
        <label class="mt-radio mt-radio-outline">
            <input type="radio"
                   __checked__
                   name="_custom_field_radio_box__id__"
                   value="__value__">
            <span></span>
            __title__
        </label>
    </div>
</script>

<script id="_render_custom_field_select_template" type="text/x-custom-template">
    <select class="form-control">
        <option value=""></option>
    </select>
</script>

<script id="_render_custom_field_image_template" type="text/x-custom-template">
    <div class="select-media-box">
        @include('core/base::forms.partials.image', [
            'name' => null,
            'value' => '__value__',
            'image' => '__image__',
            'attributes' => [],
        ])
    </div>
</script>

<script id="_render_custom_field_file_template" type="text/x-custom-template">
    <div class="select-media-box">
        @include('core/base::forms.partials.file', [
            'name' => null,
            'value' => '__value__',
            'url' => '__url__',
            'attributes' => [],
        ])
    </div>
</script>

<script id="_render_custom_field_wysiswg_template" type="text/x-custom-template">
    <textarea class="form-control wysiwyg-editor" rows="3">__value__</textarea>
</script>

<script id="_render_custom_field_repeater_template" type="text/x-custom-template">
    <div class="lcf-repeater">
        <ul class="sortable-wrapper field-group-items"></ul>
        <a href="#" class="repeater-add-new-field mt10 btn btn-success"></a>
    </div>
</script>

<script id="_render_custom_field_repeater_item_template" type="text/x-custom-template">
    <li class="ui-sortable-handle" data-position="__position__">
        <a href="#" class="remove-field-line" title="{{ trans('plugins/custom-field::base.remove_this_line') }}"><span>&nbsp;</span></a>
        <a href="#" class="collapse-field-line" title="{{ trans('plugins/custom-field::base.collapse_this_line') }}"><i class="fa fa-bars"></i></a>
        <div class="col-12 field-line-wrapper clearfix">
            <ul class="field-group"></ul>
        </div>
        <div class="clearfix"></div>
    </li>
</script>

<script id="_render_custom_field_repeater_line_template" type="text/x-custom-template">
    <li>
        <div class="col-3 repeater-item-helper">
            <div class="field-label">__title__</div>
            <div class="field-instructions help-block">__instructions__</div>
        </div>
        <div class="col-9 repeater-item-input"></div>
        <div class="clearfix"></div>
    </li>
</script>
