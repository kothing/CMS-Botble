<div class="table-wrapper">
    <div class="table-responsive">
        {!! $dataTable->table(compact('id', 'class'), false) !!}
    </div>
</div>

@push('footer')
    {!! $dataTable->scripts() !!}
@endpush
