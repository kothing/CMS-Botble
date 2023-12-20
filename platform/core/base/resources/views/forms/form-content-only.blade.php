@if ($showStart)
    {!! Form::open(Arr::except($formOptions, ['template'])) !!}
@endif

<div class="form-body">
    @if ($showFields)
        @foreach ($fields as $field)
            @if (!in_array($field->getName(), $exclude))
                {!! $field->render() !!}
            @endif
        @endforeach
    @endif
</div>

@if ($showEnd)
    {!! Form::close() !!}
@endif

@if ($form->getValidatorClass())
    @push('footer')
        {!! $form->renderValidatorJs() !!}
    @endpush
@endif
