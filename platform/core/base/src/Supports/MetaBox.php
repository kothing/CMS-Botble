<?php

namespace Botble\Base\Supports;

use Botble\Base\Models\MetaBox as MetaBoxModel;
use Closure;
use Exception;
use Illuminate\Database\Eloquent\Model;

class MetaBox
{
    protected array $metaBoxes = [];

    public function addMetaBox(
        string $id,
        string $title,
        string|array|callable|Closure $callback,
        string|null $reference = null,
        string $context = 'advanced',
        string $priority = 'default',
        array|null $callbackArgs = null
    ): void {
        if (! isset($this->metaBoxes[$reference])) {
            $this->metaBoxes[$reference] = [];
        }
        if (! isset($this->metaBoxes[$reference][$context])) {
            $this->metaBoxes[$reference][$context] = [];
        }

        foreach (array_keys($this->metaBoxes[$reference]) as $currentContext) {
            foreach (['high', 'core', 'default', 'low'] as $currentPriority) {
                if (! isset($this->metaBoxes[$reference][$currentContext][$currentPriority][$id])) {
                    continue;
                }

                // If a core box was previously added or removed by a plugin, don't add.
                if ('core' == $priority) {
                    // If core box previously deleted, don't add
                    if (false === $this->metaBoxes[$reference][$currentContext][$currentPriority][$id]) {
                        return;
                    }

                    /*
                     * If box was added with default priority, give it core priority to
                     * maintain sort order.
                     */
                    if ('default' == $currentPriority) {
                        $this->metaBoxes[$reference][$currentContext]['core'][$id] = $this->metaBoxes[$reference][$currentContext]['default'][$id];
                        unset($this->metaBoxes[$reference][$currentContext]['default'][$id]);
                    }

                    return;
                }
                /* If no priority given and id already present, use existing priority.
                 *
                 * Else, if we're adding to the sorted priority, we don't know the title
                 * or callback. Grab them from the previously added context/priority.
                 */
                if (empty($priority)) {
                    $priority = $currentPriority;
                } elseif ('sorted' == $priority) {
                    $title = $this->metaBoxes[$reference][$currentContext][$currentPriority][$id]['title'];
                    $callback = $this->metaBoxes[$reference][$currentContext][$currentPriority][$id]['callback'];
                    $callbackArgs = $this->metaBoxes[$reference][$currentContext][$currentPriority][$id]['args'];
                }
                // An id can be in only one priority and one context.
                if ($priority != $currentPriority || $context != $currentContext) {
                    unset($this->metaBoxes[$reference][$currentContext][$currentPriority][$id]);
                }
            }
        }

        if (empty($priority)) {
            $priority = 'low';
        }

        if (! isset($this->metaBoxes[$reference][$context][$priority])) {
            $this->metaBoxes[$reference][$context][$priority] = [];
        }

        $this->metaBoxes[$reference][$context][$priority][$id] = [
            'id' => $id,
            'title' => $title,
            'callback' => $callback,
            'args' => $callbackArgs,
        ];
    }

    public function doMetaBoxes(string $context, Model|string|null $object = null): void
    {
        $data = '';
        $reference = get_class($object);
        if (isset($this->metaBoxes[$reference][$context])) {
            foreach (['high', 'sorted', 'core', 'default', 'low'] as $priority) {
                if (! isset($this->metaBoxes[$reference][$context][$priority])) {
                    continue;
                }

                foreach ((array)$this->metaBoxes[$reference][$context][$priority] as $box) {
                    if (! $box || ! $box['title']) {
                        continue;
                    }
                    $data .= view('core/base::elements.meta-box-wrap', [
                        'box' => $box,
                        'callback' => call_user_func_array($box['callback'], [$object, $reference, $box]),
                    ])->render();
                }
            }
        }

        echo view('core/base::elements.meta-box', compact('data', 'context'))->render();
    }

    public function removeMetaBox(string $id, string|null $reference, string $context): void
    {
        if (! isset($this->metaBoxes[$reference])) {
            $this->metaBoxes[$reference] = [];
        }

        if (! isset($this->metaBoxes[$reference][$context])) {
            $this->metaBoxes[$reference][$context] = [];
        }

        foreach (['high', 'core', 'default', 'low'] as $priority) {
            $this->metaBoxes[$reference][$context][$priority][$id] = false;
        }
    }

    public function saveMetaBoxData(Model $object, string $key, $value, $options = null): void
    {
        $key = apply_filters('stored_meta_box_key', $key, $object);

        try {
            $data = [
                'meta_key' => $key,
                'reference_id' => $object->getKey(),
                'reference_type' => $object::class,
            ];

            $fieldMeta = MetaBoxModel::query()->where($data)->first();

            if (! $fieldMeta) {
                $fieldMeta = MetaBoxModel::query()->getModel();
                $fieldMeta->fill($data);
            }

            if (! empty($options)) {
                $fieldMeta->options = $options;
            }

            $fieldMeta->meta_value = [$value];
            $fieldMeta->save();
        } catch (Exception $exception) {
            info($exception->getMessage());
        }
    }

    public function getMetaData(
        Model $object,
        string $key,
        bool $single = false,
        array $select = ['meta_value']
    ): string|array|null {
        if ($object instanceof MetaBoxModel) {
            $field = $object;
        } else {
            $field = $this->getMeta($object, $key, $select);
        }

        if (! $field) {
            return $single ? '' : [];
        }

        return $single ? $field->meta_value[0] : $field->meta_value;
    }

    public function getMeta(Model $object, string $key, array $select = ['meta_value']): ?Model
    {
        $key = apply_filters('stored_meta_box_key', $key, $object);

        return MetaBoxModel::query()->where([
            'meta_key' => $key,
            'reference_id' => $object->getKey(),
            'reference_type' => get_class($object),
        ], $select)->first();
    }

    public function deleteMetaData(Model $object, string $key): bool
    {
        $key = apply_filters('stored_meta_box_key', $key, $object);

        return MetaBoxModel::query()->where([
            'meta_key' => $key,
            'reference_id' => $object->getKey(),
            'reference_type' => get_class($object),
        ])->delete();
    }

    public function getMetaBoxes(): array
    {
        return $this->metaBoxes;
    }
}
