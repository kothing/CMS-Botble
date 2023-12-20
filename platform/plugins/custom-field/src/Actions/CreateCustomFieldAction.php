<?php

namespace Botble\CustomField\Actions;

use Botble\Base\Events\CreatedContentEvent;
use Botble\CustomField\Repositories\Interfaces\FieldGroupInterface;
use Illuminate\Support\Facades\Auth;

class CreateCustomFieldAction extends AbstractAction
{
    public function __construct(protected FieldGroupInterface $fieldGroupRepository)
    {
    }

    public function run(array $data): array
    {
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        $result = $this->fieldGroupRepository->createFieldGroup($data);

        event(new CreatedContentEvent(CUSTOM_FIELD_MODULE_SCREEN_NAME, request(), $result));

        if (! $result) {
            return $this->error();
        }

        return $this->success(null, [
            'id' => $result,
        ]);
    }
}
