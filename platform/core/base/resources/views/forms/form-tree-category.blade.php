@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')
    @php
        $categories = $form->getFormOption('categories', collect());
        $canCreate = $form->getFormOption('canCreate');
        $canEdit = $form->getFormOption('canEdit');
        $canDelete = $form->getFormOption('canDelete');
        $indexRoute = $form->getFormOption('indexRoute');
        $createRoute = $form->getFormOption('createRoute');
        $editRoute = $form->getFormOption('editRoute');
        $deleteRoute = $form->getFormOption('deleteRoute');
    @endphp
    <div class="row">
        <div class="col-12">
            <div class="my-2 text-end">
                @php do_action(BASE_ACTION_META_BOXES, 'head', $form->getModel()) @endphp
            </div>
        </div>
        <div class="col-md-4">
            <div class="card tree-categories-container position-relative">
                <div class="tree-loading">
                    @include('core/base::elements.loading')
                </div>
                <div class="tree-categories-body card-body">
                    <div class="mb-3 d-flex">
                        <button class="btn btn-primary toggle-tree"
                            type="button"
                            data-expand="{{ trans('core/base::forms.expand_all') }}"
                            data-collapse="{{ trans('core/base::forms.collapse_all') }}">
                            {{ trans('core/base::forms.collapse_all') }}
                        </button>
                        @if ($createRoute)
                            <a class="tree-categories-create btn btn-info mx-2
                                @if (!$canCreate) d-none  @endif"
                                href="{{ route($createRoute) }}">
                                @include('core/table::partials.create')
                            </a>
                        @endif
                    </div>

                    <div class="file-tree-wrapper" data-url="{{ $indexRoute ? route($indexRoute) : '' }}">
                        @include('core/base::forms.partials.tree-categories')
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card tree-form-container position-relative">
                <div class="tree-loading d-none">
                    @include('core/base::elements.loading')
                </div>
                <div class="tree-form-body card-body">
                    @include('core/base::forms.form-no-wrap')
                </div>
            </div>
        </div>
    </div>
@stop

@push('footer')
    @include('core/table::modal')
@endpush
