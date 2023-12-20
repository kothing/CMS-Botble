<script type="text/x-custom-template" id="rules_group_template">
    <div class="line rule-line mb10">
        <select class="form-control float-start rule-a">
            @foreach($ruleGroups as $key => $row)
                <optgroup label="{{ trans('plugins/custom-field::rules.groups.' . $key) }}">
                    @foreach($row['items'] as $item)
                        <option value="{{ $item['slug'] ?? '' }}">{{ $item['title'] ?? '' }}</option>
                    @endforeach
                </optgroup>
            @endforeach
        </select>
        <select class="form-control float-start rule-type">
            <option value="==">{{ trans('plugins/custom-field::base.form.rules.is_equal_to') }}</option>
            <option value="!=">{{ trans('plugins/custom-field::base.form.rules.is_not_equal_to') }}</option>
        </select>
        <div class="rules-b-group float-start">
            @foreach($ruleGroups as $key => $row)
                @foreach($row['items'] as $item)
                    <select class="form-control rule-b" data-rel="{{ $item['slug'] ?? '' }}">
                        @foreach($item['data'] as $keyData => $rowData)
                            <option value="{{ $keyData ?? '' }}">{{ $rowData ?? '' }}</option>
                        @endforeach
                    </select>
                @endforeach
            @endforeach
        </div>
        <a class="location-add-rule-and location-add-rule btn btn-info float-start" href="#">
            {{ trans('plugins/custom-field::base.form.rules.and') }}
        </a>
        <a href="#" title="" class="remove-rule-line"><span>&nbsp;</span></a>
        <div class="clearfix"></div>
    </div>
</script>

<script type="text/x-custom-template" id="rules_line_group_template">
    <div class="line-group" data-text="{{ trans('plugins/custom-field::base.form.rules.or') }}"></div>
</script>
