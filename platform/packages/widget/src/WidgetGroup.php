<?php

namespace Botble\Widget;

use Botble\Widget\Misc\ViewExpressionTrait;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Arr;
use stdClass;

class WidgetGroup
{
    use ViewExpressionTrait;

    protected string $id;

    protected string $name;

    protected string|null $description = null;

    protected array $widgets = [];

    protected int $position = 100;

    protected string $separator = '';

    protected int $count = 0;

    public function __construct(array $args, protected Application $app)
    {
        $this->id = $args['id'];
        $this->name = $args['name'];
        $this->description = Arr::get($args, 'description');
    }

    /**
     * Display all widgets from this group in correct order.
     */
    public function display(): string
    {
        ksort($this->widgets);

        $output = '';
        $count = 0;
        foreach ($this->widgets as $position => $widgets) {
            foreach ($widgets as $widget) {
                $count++;
                $output .= $this->displayWidget($widget, $position);
                if ($this->count !== $count) {
                    $output .= $this->separator;
                }
            }
        }

        return $this->convertToViewExpression($output);
    }

    /**
     * Display a widget according to its type.
     */
    protected function displayWidget(array $widget, int|null $position): string|null
    {
        $widget['arguments'][] = $this->id;
        $widget['arguments'][] = $position;

        $factory = $this->app->make('botble.widget');

        return $factory->run(...$widget['arguments']);
    }

    public function position(int $position): WidgetGroup
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Add a widget to the group.
     */
    public function addWidget(): void
    {
        $this->addWidgetWithType('sync', func_get_args());
    }

    /**
     * Add a widget with a given type to the array.
     */
    protected function addWidgetWithType(string $type, array $arguments = []): void
    {
        if (! isset($this->widgets[$this->position])) {
            $this->widgets[$this->position] = [];
        }

        $this->widgets[$this->position][$arguments[0]] = [
            'arguments' => $arguments,
            'type' => $type,
        ];

        $this->count++;

        $this->resetPosition();
    }

    /**
     * Reset the position property back to the default.
     * So it does not affect the next widget.
     */
    protected function resetPosition(): void
    {
        $this->position = 100;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * Set a separator to display between widgets in the group.
     */
    public function setSeparator(string $separator): self
    {
        $this->separator = $separator;

        return $this;
    }

    /**
     * Check if there are any widgets in the group.
     */
    public function any(): bool
    {
        return ! $this->isEmpty();
    }

    /**
     * Check if there are no widgets in the group.
     */
    public function isEmpty(): bool
    {
        return empty($this->widgets);
    }

    /**
     * Count the number of widgets in this group.
     */
    public function count(): int
    {
        $count = 0;
        foreach ($this->widgets as $widgets) {
            $count += count($widgets);
        }

        return $count;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): string|null
    {
        return $this->description;
    }

    public function setDescription(string|null $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getWidgets(): array
    {
        $result = [];
        foreach ($this->widgets as $index => $item) {
            foreach (array_keys($item) as $key) {
                $obj = new stdClass();
                $obj->widget_id = $key;
                $obj->position = $index;
                $obj->name = Arr::get($item[$key], 'arguments.1.name');
                $obj->sidebar_id = $this->id;
                $result[] = $obj;
            }
        }

        return $result;
    }
}
