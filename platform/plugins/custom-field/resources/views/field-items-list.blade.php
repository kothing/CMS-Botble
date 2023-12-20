<div class="custom-fields-list">
    <div class="nestable-group">
        <div class="add-new-field">
            <ul class="row field-table-header clearfix">
                <li class="col-4 list-group-item w-bold">{{ trans('plugins/custom-field::base.form.field_label') }}</li>
                <li class="col-4 list-group-item w-bold">{{ trans('plugins/custom-field::base.form.field_name') }}</li>
                <li class="col-4 list-group-item w-bold">{{ trans('plugins/custom-field::base.form.field_type') }}</li>
            </ul>
            <div class="clearfix"></div>
            <ul class="sortable-wrapper edit-field-group-items field-group-items"
                id="custom_field_group_items"></ul>
            <div class="text-end pt10">
                <a class="btn btn-info btn-add-field"
                   href="#">{{ trans('plugins/custom-field::base.form.add_field') }}</a>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
</div>
