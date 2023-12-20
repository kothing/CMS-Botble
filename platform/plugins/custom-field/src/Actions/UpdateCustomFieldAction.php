<?php

namespace Botble\CustomField\Actions;

use Botble\Base\Events\UpdatedContentEvent;
use Botble\CustomField\Models\FieldGroup;
use Botble\CustomField\Repositories\Interfaces\FieldGroupInterface;
use Illuminate\Support\Facades\Auth;

class UpdateCustomFieldAction extends AbstractAction
{
    public function __construct(protected FieldGroupInterface $fieldGroupRepository)
    {
    }

    public function run(FieldGroup $fieldGroup, array $data): array
    {
        $data['updated_by'] = Auth::id();

        $result = $this->fieldGroupRepository->updateFieldGroup($fieldGroup->id, $data);

        event(new UpdatedContentEvent(CUSTOM_FIELD_MODULE_SCREEN_NAME, request(), $result));

        if (! $result) {
            return $this->error();
        }

        return $this->success(null, [
            'id' => $fieldGroup->id,
        ]);
    }
}
