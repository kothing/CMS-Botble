<script id="_options-repeater_template" type="text/x-custom-template">
    <div class="line" data-option="repeater">
        <div class="col-3">
            <h5>{!! BaseHelper::clean(trans('plugins/custom-field::base.form.repeater_fields')) !!}</h5>
        </div>
        <div class="col-9">
            <h5>{!! BaseHelper::clean(trans('plugins/custom-field::base.form.repeater_fields')) !!}</h5>
            <div class="add-new-field">
                <ul class="row list-group field-table-header clearfix">
                    <li class="col-4 list-group-item w-bold">{{ trans('plugins/custom-field::base.form.field_label') }}</li>
                    <li class="col-4 list-group-item w-bold">{{ trans('plugins/custom-field::base.form.field_name') }}</li>
                    <li class="col-4 list-group-item w-bold">{{ trans('plugins/custom-field::base.form.field_type') }}</li>
                </ul>
                <div class="clearfix"></div>
                <ul class="sortable-wrapper edit-field-group-items field-group-items">

                </ul>
                <div class="text-end pt10">
                    <a class="btn btn-info btn-add-field" href="#">{{ trans('plugins/custom-field::base.form.add_field') }}</a>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
</script>

<script id="_options-defaultvalue_template" type="text/x-custom-template">
    <div class="line" data-option="defaultvalue">
        <div class="col-3">
            <h5>{!! BaseHelper::clean(trans('plugins/custom-field::base.form.default_value')) !!}</h5>
            <p>{!! BaseHelper::clean(trans('plugins/custom-field::base.form.default_value_helper')) !!}</p>
        </div>
        <div class="col-9">
            <h5>{!! BaseHelper::clean(trans('plugins/custom-field::base.form.default_value')) !!}</h5>
            <input type="text" class="form-control" placeholder="" value="">
        </div>
        <div class="clearfix"></div>
    </div>
</script>

<script id="_options-placeholdertext_template" type="text/x-custom-template">
    <div class="line" data-option="placeholdertext">
        <div class="col-3">
            <h5>{!! BaseHelper::clean(trans('plugins/custom-field::base.form.placeholder')) !!}</h5>
            <p>{!! BaseHelper::clean(trans('plugins/custom-field::base.form.placeholder_helper')) !!}</p>
        </div>
        <div class="col-9">
            <h5>{!! BaseHelper::clean(trans('plugins/custom-field::base.form.placeholder')) !!}</h5>
            <input type="text" class="form-control" placeholder="" value="">
        </div>
        <div class="clearfix"></div>
    </div>
</script>

<script id="_options-defaultvaluetextarea_template" type="text/x-custom-template">
    <div class="line" data-option="defaultvaluetextarea">
        <div class="col-3">
            <h5>{!! BaseHelper::clean(trans('plugins/custom-field::base.form.default_value')) !!}</h5>
            <p>{!! BaseHelper::clean(trans('plugins/custom-field::base.form.default_value_helper')) !!}</p>
        </div>
        <div class="col-9">
            <h5>{!! BaseHelper::clean(trans('plugins/custom-field::base.form.default_value')) !!}</h5>
            <textarea class="form-control" rows="5"></textarea>
        </div>
        <div class="clearfix"></div>
    </div>
</script>

<script id="_options-rows_template" type="text/x-custom-template">
    <div class="line" data-option="rows">
        <div class="col-3">
            <h5>{!! BaseHelper::clean(trans('plugins/custom-field::base.form.rows')) !!}</h5>
            <p>{!! BaseHelper::clean(trans('plugins/custom-field::base.form.rows_helper')) !!}</p>
        </div>
        <div class="col-9">
            <h5>{!! BaseHelper::clean(trans('plugins/custom-field::base.form.rows')) !!}</h5>
            <input type="number" class="form-control" placeholder="Number" value="" min="1" max="10">
        </div>
        <div class="clearfix"></div>
    </div>
</script>

<script id="_options-selectchoices_template" type="text/x-custom-template">
    <div class="line" data-option="selectchoices">
        <div class="col-3">
            <h5>{!! BaseHelper::clean(trans('plugins/custom-field::base.form.choices')) !!}</h5>
            <p>{!! BaseHelper::clean(trans('plugins/custom-field::base.form.choices_helper')) !!}</p>
        </div>
        <div class="col-9">
            <h5>{!! BaseHelper::clean(trans('plugins/custom-field::base.form.choices')) !!}</h5>
            <textarea class="form-control" rows="5"></textarea>
        </div>
        <div class="clearfix"></div>
    </div>
</script>

<script id="_options-buttonlabel_template" type="text/x-custom-template">
    <div class="line" data-option="buttonlabel">
        <div class="col-3">
            <h5>{!! BaseHelper::clean(trans('plugins/custom-field::base.form.button_label')) !!}</h5>
        </div>
        <div class="col-9">
            <h5>{!! BaseHelper::clean(trans('plugins/custom-field::base.form.button_label')) !!}</h5>
            <input type="text" class="form-control" placeholder="Add new" value="">
        </div>
        <div class="clearfix"></div>
    </div>
</script>

<script id="_new-field-source_template" type="text/x-custom-template">
    <li class="ui-sortable-handle active">
        <div class="field-column">
            <div class="row">
                <div class="text col-4 field-label">{{ trans('plugins/custom-field::base.form.new_field') }}</div>
                <div class="text col-4 field-slug"></div>
                <div class="text col-4 field-type">{!! BaseHelper::clean(trans('plugins/custom-field::base.form.types.text')) !!}</div>
            </div>
            <a class="show-item-details" title="" href="#"><i class="fa fa-angle-down"></i></a>
            <div class="clearfix"></div>
        </div>
        <div class="item-details">
            <div class="line" data-option="title">
                <div class="col-3">
                    <h5>{!! BaseHelper::clean(trans('plugins/custom-field::base.form.field_label')) !!}</h5>
                    <p>{!! BaseHelper::clean(trans('plugins/custom-field::base.form.field_label_helper')) !!}</p>
                </div>
                <div class="col-9">
                    <h5>{!! BaseHelper::clean(trans('plugins/custom-field::base.form.field_label')) !!}</h5>
                    <input type="text" class="form-control" placeholder="" value="New field">
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="line" data-option="slug">
                <div class="col-3">
                    <h5>{!! BaseHelper::clean(trans('plugins/custom-field::base.form.field_name')) !!}</h5>
                    <p>{!! BaseHelper::clean(trans('plugins/custom-field::base.form.field_name_helper')) !!}</p>
                </div>
                <div class="col-9">
                    <h5>{!! BaseHelper::clean(trans('plugins/custom-field::base.form.field_name')) !!}</h5>
                    <input type="text" class="form-control" placeholder="" value="">
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="line" data-option="type">
                <div class="col-3">
                    <h5>{!! BaseHelper::clean(trans('plugins/custom-field::base.form.field_type')) !!}</h5>
                    <p>{!! BaseHelper::clean(trans('plugins/custom-field::base.form.field_type_helper')) !!}</p>
                </div>
                <div class="col-9">
                    <h5>{!! BaseHelper::clean(trans('plugins/custom-field::base.form.field_type')) !!}</h5>
                    <select class="form-control change-field-type">
                        <optgroup label="{{ trans('plugins/custom-field::base.form.groups.basic') }}">
                            <option value="text"
                                    selected="selected">{{ trans('plugins/custom-field::base.form.types.text') }}</option>
                            <option value="textarea">{{ trans('plugins/custom-field::base.form.types.textarea') }}</option>
                            <option value="number">{{ trans('plugins/custom-field::base.form.types.number') }}</option>
                            <option value="email">{{ trans('plugins/custom-field::base.form.types.email') }}</option>
                            <option value="password">{{ trans('plugins/custom-field::base.form.types.password') }}</option>
                        </optgroup>
                        <optgroup label="{{ trans('plugins/custom-field::base.form.groups.content') }}">
                            <option value="wysiwyg">{{ trans('plugins/custom-field::base.form.types.wysiwyg') }}</option>
                            <option value="image">{{ trans('plugins/custom-field::base.form.types.image') }}</option>
                            <option value="file">{{ trans('plugins/custom-field::base.form.types.file') }}</option>
                        </optgroup>
                        <optgroup label="{{ trans('plugins/custom-field::base.form.groups.choice') }}">
                            <option value="select">{{ trans('plugins/custom-field::base.form.types.select') }}</option>
                            <option value="checkbox">{{ trans('plugins/custom-field::base.form.types.checkbox') }}</option>
                            <option value="radio">{{ trans('plugins/custom-field::base.form.types.radio') }}</option>
                        </optgroup>
                        <optgroup label="{{ trans('plugins/custom-field::base.form.groups.other') }}">
                            <option value="repeater">{{ trans('plugins/custom-field::base.form.types.repeater') }}</option>
                        </optgroup>
                    </select>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="line" data-option="instructions">
                <div class="col-3">
                    <h5>{!! BaseHelper::clean(trans('plugins/custom-field::base.form.field_instructions')) !!}</h5>
                    <p>{!! BaseHelper::clean(trans('plugins/custom-field::base.form.field_instructions_helper')) !!}</p>
                </div>
                <div class="col-9">
                    <h5>{!! BaseHelper::clean(trans('plugins/custom-field::base.form.field_instructions')) !!}</h5>
                    <textarea class="form-control" rows="5"></textarea>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="options">___options___</div>
            <div class="text-end p10">
                <a class="btn red btn-remove" title=""
                   href="#">{{ trans('plugins/custom-field::base.form.remove_field') }}</a>
                <a class="btn blue btn-close-field" title=""
                   href="#">{{ trans('plugins/custom-field::base.form.close_field') }}</a>
            </div>
        </div>
    </li>
</script>
