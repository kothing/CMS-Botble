<div class="form-group">
    <label for="widget-name">{{ __('Name') }}</label>
    <input type="text" id="widget-name" class="form-control" name="name" value="{{ $config['name'] }}">
</div>
<div class="form-group">
    <label for="widget_menu">{{ __('Select menu') }}</label>
    {!! Form::customSelect('menu_id', app(\Botble\Menu\Repositories\Interfaces\MenuInterface::class)->pluck('name', 'slug'), $config['menu_id'], ['class' => 'form-control select-full']) !!}
</div>
