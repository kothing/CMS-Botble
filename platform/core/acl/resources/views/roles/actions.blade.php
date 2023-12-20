<div class="widget meta-boxes form-actions form-actions-default action-{{ $direction ?? 'horizontal' }}">
    <div class="widget-title">
        <h4>
            <span>{{ trans('core/base::forms.actions') }}</span>
        </h4>
    </div>
    <div class="widget-body">
        <div class="btn-set">
            @if ($role && $role->id)
                <a href="{{ route('roles.duplicate', $role->id) }}" class="btn btn-warning"><i class="fa fa-copy"></i> {{ trans('core/acl::permissions.duplicate') }}</a>
            @endif
            <button type="submit" name="submit" value="save" class="btn btn-info">
                <i class="fa fa-save"></i> {{ trans('core/base::forms.save') }}
            </button>
            <button type="submit" name="submit" value="apply" class="btn btn-success">
                <i class="fa fa-check-circle"></i> {{ trans('core/base::forms.save_and_continue') }}
            </button>
        </div>
    </div>
</div>
<div id="waypoint"></div>
<div class="form-actions form-actions-fixed-top hidden">
    {!! Breadcrumbs::render('main', PageTitle::getTitle(false)) !!}
    <div class="btn-set">
        @if ($role && $role->id)
            <a href="{{ route('roles.duplicate', $role->id) }}" class="btn btn-warning"><i class="fa fa-copy"></i> {{ trans('core/acl::permissions.duplicate') }}</a>
        @endif
        <button type="submit" name="submit" value="save" class="btn btn-info">
            <i class="fa fa-save"></i> {{ trans('core/base::forms.save') }}
        </button>
        <button type="submit" name="submit" value="apply" class="btn btn-success">
            <i class="fa fa-check-circle"></i> {{ trans('core/base::forms.save_and_continue') }}
        </button>
    </div>
</div>
