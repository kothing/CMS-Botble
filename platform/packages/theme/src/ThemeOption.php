<?php

namespace Botble\Theme;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\Form;
use Botble\Language\Facades\Language;
use Botble\Setting\Facades\Setting;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;

class ThemeOption
{
    public array $fields = [];

    public array $sections = [];

    public array $help = [];

    public array $args = [];

    public array $priority = [];

    public array $errors = [];

    public string $optName = 'theme';

    /**
     * Prepare args of theme options
     */
    public function constructArgs(): array
    {
        return $this->args[$this->optName] ?? [];
    }

    /**
     * Prepare sections to display theme options page
     */
    public function constructSections(): array
    {
        $sections = [];

        if (! isset($this->sections[$this->optName])) {
            return $sections;
        }

        foreach ($this->sections[$this->optName] as $sectionId => $section) {
            $section['fields'] = $this->constructFields($sectionId);
            $priority = $section['priority'];
            while (isset($sections[$priority])) {
                $priority++;
            }
            $sections[$priority] = $section;
        }

        ksort($sections);

        return $sections;
    }

    /**
     * Prepare fields to display theme options page
     */
    public function constructFields(string $sectionId = ''): array
    {
        $fields = [];
        if (! empty($this->fields[$this->optName])) {
            foreach ($this->fields[$this->optName] as $field) {
                if (Arr::get($field, 'section_id') == $sectionId) {
                    $priority = $field['priority'];
                    while (isset($fields[$priority])) {
                        echo $priority++;
                    }
                    $fields[$priority] = $field;
                }
            }
        }

        ksort($fields);

        return $fields;
    }

    public function getSection(string $id = ''): bool
    {
        $this->checkOptName();

        if (! empty($this->optName) && ! empty($id)) {
            if (! isset($this->sections[$this->optName][$id])) {
                $id = strtolower(sanitize_html_class($id));
            }

            return $this->sections[$this->optName][$id] ?? false;
        }

        return false;
    }

    public function checkOptName(): void
    {
        if (empty($this->optName)) {
            return;
        }

        if (! isset($this->sections[$this->optName])) {
            $this->sections[$this->optName] = [];
            $this->priority[$this->optName]['sections'] = 1;
        }

        if (! isset($this->args[$this->optName])) {
            $this->args[$this->optName] = [];
            $this->priority[$this->optName]['args'] = 1;
        }

        if (! isset($this->fields[$this->optName])) {
            $this->fields[$this->optName] = [];
            $this->priority[$this->optName]['fields'] = 1;
        }

        if (! isset($this->help[$this->optName])) {
            $this->help[$this->optName] = [];
            $this->priority[$this->optName]['help'] = 1;
        }

        if (! isset($this->errors[$this->optName])) {
            $this->errors[$this->optName] = [];
        }
    }

    public function getSections(): array
    {
        $this->checkOptName();

        if (! empty($this->sections[$this->optName])) {
            return $this->sections[$this->optName];
        }

        return [];
    }

    public function setSections(array $sections = []): self
    {
        $this->checkOptName();
        if (! empty($sections)) {
            foreach ($sections as $section) {
                $this->setSection($section);
            }
        }

        return $this;
    }

    public function setSection(array $section = []): self
    {
        $this->checkOptName();

        if (empty($section)) {
            return $this;
        }

        if (! isset($section['id'])) {
            if (isset($section['type']) && $section['type'] == 'divide') {
                $section['id'] = time();
            } elseif (isset($section['title'])) {
                $section['id'] = strtolower($section['title']);
            } else {
                $section['id'] = time();
            }

            if (isset($this->sections[$this->optName][$section['id']])) {
                $orig = $section['id'];
                $index = 0;
                while (isset($this->sections[$this->optName][$section['id']])) {
                    $section['id'] = $orig . '_' . $index;
                }
            }
        }

        if (! empty($this->optName) && is_array($section)) {
            if (! isset($section['title'])) {
                $this->errors[$this->optName]['section']['missing_title'] = 'Unable to create a section due to missing id and title.';

                return $this;
            }

            if (! isset($section['priority'])) {
                $section['priority'] = $this->getPriority('sections');
            }

            if (isset($section['fields'])) {
                if (! empty($section['fields']) && is_array($section['fields'])) {
                    $this->processFieldsArray($section['id'], $section['fields']);
                }
                unset($section['fields']);
            }

            $this->sections[$this->optName][$section['id']] = $section;
        } else {
            $this->errors[$this->optName]['section']['empty'] = 'Unable to create a section due an empty section array or the section variable passed was not an array.';

            return $this;
        }

        return $this;
    }

    public function getPriority(string $type): int
    {
        $priority = $this->priority[$this->optName][$type];
        $this->priority[$this->optName][$type] += 1;

        return $priority;
    }

    public function processFieldsArray(string $sectionId = '', array $fields = []): void
    {
        if (! empty($this->optName) && ! empty($sectionId) && is_array($fields) && ! empty($fields)) {
            foreach ($fields as $field) {
                if (! is_array($field)) {
                    continue;
                }

                $field['section_id'] = $sectionId;
                $this->setField($field);
            }
        }
    }

    public function setField(array $field = []): self
    {
        $this->checkOptName();

        if (! empty($this->optName) && is_array($field) && ! empty($field)) {
            if (! isset($field['priority'])) {
                $field['priority'] = $this->getPriority('fields');
            }

            if (isset($field['id'])) {
                $this->fields[$this->optName][$field['id']] = $field;
            }
        }

        return $this;
    }

    public function removeSection(string $id = '', bool $fields = false): self
    {
        if (! empty($this->optName) && ! empty($id)) {
            if (isset($this->sections[$this->optName][$id])) {
                $priority = '';

                foreach ($this->sections[$this->optName] as $key => $section) {
                    if ($key == $id) {
                        $priority = $section['priority'];
                        $this->priority[$this->optName]['sections']--;
                        unset($this->sections[$this->optName][$id]);

                        continue;
                    }

                    if ($priority != '') {
                        $newPriority = $section['priority'];
                        $section['priority'] = $priority;
                        $this->sections[$this->optName][$key] = $section;
                        $priority = $newPriority;
                    }
                }

                if (isset($this->fields[$this->optName]) && ! empty($this->fields[$this->optName]) && $fields) {
                    foreach ($this->fields[$this->optName] as $key => $field) {
                        if (Arr::get($field, 'section_id') == $id) {
                            unset($this->fields[$this->optName][$key]);
                        }
                    }
                }
            }
        }

        return $this;
    }

    public function hideSection(string $id = '', bool $hide = true): void
    {
        $this->checkOptName();

        if (! empty($this->optName) && ! empty($id) && isset($this->sections[$this->optName][$id])) {
            $this->sections[$this->optName][$id]['hidden'] = $hide;
        }
    }

    public function getField(string $id = ''): bool|array
    {
        $this->checkOptName();

        if (! empty($this->optName) && ! empty($id)) {
            return $this->fields[$this->optName][$id] ?? false;
        }

        return false;
    }

    public function hideField(string $id = '', bool $hide = true): void
    {
        $this->checkOptName();

        if (! empty($this->optName) && ! empty($id) && isset($this->fields[$this->optName][$id])) {
            if (! $hide) {
                $this->fields[$this->optName][$id]['class'] = str_replace(
                    'hidden',
                    '',
                    $this->fields[$this->optName][$id]['class']
                );
            } else {
                $this->fields[$this->optName][$id]['class'] .= 'hidden';
            }
        }
    }

    public function removeField(string $id = ''): self
    {
        $this->checkOptName();

        if (! empty($this->optName) && ! empty($id)) {
            if (isset($this->fields[$this->optName][$id])) {
                foreach ($this->fields[$this->optName] as $key => $field) {
                    if ($key == $id) {
                        $priority = $field['priority'];
                        $this->priority[$this->optName]['fields']--;
                        unset($this->fields[$this->optName][$id]);

                        continue;
                    }

                    if (isset($priority) && $priority != '') {
                        $newPriority = $field['priority'];
                        $field['priority'] = $priority;
                        $this->fields[$this->optName][$key] = $field;
                        $priority = $newPriority;
                    }
                }
            }
        }

        return $this;
    }

    public function getArgs(): array
    {
        $this->checkOptName();

        if (! empty($this->optName) && ! empty($this->args[$this->optName])) {
            return $this->args[$this->optName];
        }

        return [];
    }

    public function setArgs(array $args = []): self
    {
        $this->checkOptName();

        if (! empty($this->optName) && ! empty($args) && is_array($args)) {
            if (isset($this->args[$this->optName]['clearArgs'])) {
                $this->args[$this->optName] = [];
            }

            $this->args[$this->optName] = parse_args($args, $this->args[$this->optName]);
        }

        return $this;
    }

    public function getArg(string $key = ''): string|null
    {
        $this->checkOptName();

        if (! empty($this->optName) && ! empty($key) && ! empty($this->args[$this->optName])) {
            return Arr::get($this->args[$this->optName], $key);
        }

        return null;
    }

    public function setOption(string $key, array|string|null $value = ''): self
    {
        $option = Arr::get($this->fields[$this->optName], $key);

        if ($option && Arr::get($option, 'clean_tags', true)) {
            $value = BaseHelper::clean($value);
        }

        if (is_array($value)) {
            $value = json_encode($value);
        }

        Setting::set($this->getOptionKey($key, $this->getCurrentLocaleCode()), $value);

        return $this;
    }

    public function getOptionKey(string $key, string|null $locale = '', string $theme = null): string
    {
        if (! $theme) {
            $theme = setting('theme');
            if (! $theme) {
                $theme = Arr::first(BaseHelper::scanFolder(theme_path()));
            }
        }

        $defaultLocale = App::getLocale();

        if (! $locale && defined('LANGUAGE_MODULE_SCREEN_NAME')) {
            $defaultLocale = Language::getDefaultLocaleCode();
        }

        $locale = $locale && $locale != $defaultLocale ? '-' . ltrim($locale, '-') : null;

        return $this->optName . '-' . $theme . $locale . '-' . $key;
    }

    protected function getCurrentLocaleCode(): string|null
    {
        if (! defined('LANGUAGE_MODULE_SCREEN_NAME')) {
            return null;
        }

        $currentLocale = is_in_admin() ? Language::getCurrentAdminLocaleCode() : Language::getCurrentLocaleCode();

        return $currentLocale && $currentLocale != Language::getDefaultLocaleCode() ? '-' . $currentLocale : null;
    }

    public function renderField(array $field): string|null
    {
        try {
            if ($this->hasOption($field['attributes']['name'])) {
                $field['attributes']['value'] = $this->getOption($field['attributes']['name']);
            }

            return call_user_func_array([Form::class, $field['type']], array_values($field['attributes']));
        } catch (Exception $exception) {
            info($exception->getMessage());

            return null;
        }
    }

    public function hasOption(string $key): bool
    {
        return setting()->has($this->getOptionKey($key, $this->getCurrentLocaleCode()));
    }

    public function getOption(string $key = '', string|null|array $default = ''): string|null
    {
        if (is_array($default)) {
            $default = json_encode($default);
        }

        $default = setting($this->getOptionKey($key), $default);

        $value = setting($this->getOptionKey($key, $this->getCurrentLocaleCode()), $default);

        $value = $value ?: $default;

        if (is_array($value)) {
            $value = json_encode($value);
        }

        return $value;
    }

    public function saveOptions(): bool
    {
        return setting()->save();
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function hasField(string $id): bool
    {
        $this->checkOptName();

        if (! empty($this->optName)) {
            return isset($this->fields[$this->optName][$id]);
        }

        return false;
    }
}
