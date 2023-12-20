<div class="widget meta-boxes form-actions form-actions-default action-{{ $direction ?? 'horizontal' }}">
    <div class="widget-title">
        <h4>
            <span>{{ trans('plugins/custom-field::base.publish') }}</span>
        </h4>
    </div>
    <div class="widget-body">
        <div class="btn-set">
            @if (isset($object))
                <a href="{{ route('custom-fields.export', ['id' => $object->id]) }}"
                   class="btn btn-sm purple"
                   download="{{ $object->title }}">
                    <i class="fa fa-download"></i>
                    {{ trans('plugins/custom-field::base.export') }}
                </a>
            @endif
            <button class="btn btn-info" type="submit" name="submit" value="save">
                <i class="fa fa-save"></i> {{ trans('core/base::forms.save') }}
            </button>
            <button class="btn btn-success"
                    type="submit"
                    name="submit"
                    value="apply">
                <i class="fa fa-check-circle"></i> {{ trans('core/base::forms.save_and_continue') }}
            </button>
        </div>
    </div>
</div>
<div id="waypoint"></div>
<div class="form-actions form-actions-fixed-top hidden">
    {!! Breadcrumbs::render('main', PageTitle::getTitle(false)) !!}
    <div class="btn-set">
        @if (isset($object))
            <a href="{{ route('custom-fields.export', ['id' => $object->id]) }}"
               class="btn btn-sm purple"
               download="{{ $object->title }}">
                <i class="fa fa-download"></i>
                {{ trans('plugins/custom-field::base.export') }}
            </a>
        @endif
        <button class="btn btn-primary" type="submit" name="submit" value="save">
            <i class="fa fa-save"></i> {{ trans('core/base::forms.save') }}
        </button>
        <button class="btn btn-success"
                type="submit"
                name="submit"
                value="save">
            <i class="fa fa-check-circle"></i> {{ trans('core/base::forms.save_and_continue') }}
        </button>
    </div>
</div>
