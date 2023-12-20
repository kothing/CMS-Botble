<a href="{{ route('categories.edit', $item->id) }}" class="btn btn-icon btn-primary" data-bs-toggle="tooltip" data-bs-original-title="{{ trans('core/base::tables.edit') }}"><i class="fa fa-edit"></i></a>
<a href="#" class="btn btn-icon btn-danger deleteDialog" data-bs-toggle="tooltip" data-section="{{ route('categories.destroy', $item->id) }}" role="button" data-bs-original-title="{{ trans('core/base::tables.delete_entry') }}" >
    <i class="fa fa-trash"></i>
</a>

