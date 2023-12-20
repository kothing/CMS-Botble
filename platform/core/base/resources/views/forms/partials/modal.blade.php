<x-core-base::modal
    :id="$name"
    :title="$title"
    :type="$type"
    :size="$modal_size"
    :button-id="$action_id"
    :button-label="$action_name"
>
    {!! $content !!}
</x-core-base::modal>
