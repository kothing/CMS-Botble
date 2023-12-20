<?php

namespace Botble\Revision;

use Botble\ACL\Models\User;
use Botble\Base\Models\BaseModel;
use Exception;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class Revision extends BaseModel
{
    public $table = 'revisions';

    protected array $revisionFormattedFields = [];

    public function revisionable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Field Name
     *
     * Returns the field that was updated, in the case that it's a foreign key
     * denoted by a suffix of "_id", then "_id" is simply stripped
     */
    public function fieldName(): string
    {
        if ($formatted = $this->formatFieldName($this->key)) {
            return $formatted;
        } elseif (strpos($this->key, '_id')) {
            return str_replace('_id', '', $this->key);
        }

        return $this->key;
    }

    /**
     * Format field name.
     *
     * Allow overrides for field names.
     */
    protected function formatFieldName(string $key): bool|string
    {
        $relatedModel = $this->revisionable_type;
        $relatedModel = new $relatedModel();
        $revisionFormattedFieldNames = $relatedModel->getRevisionFormattedFieldNames();

        if (isset($revisionFormattedFieldNames[$key])) {
            return $revisionFormattedFieldNames[$key];
        }

        return false;
    }

    /**
     * Old Value.
     *
     * Grab the old value of the field, if it was a foreign key
     * attempt to get an identifying name for the model.
     */
    public function oldValue(): string|null
    {
        return $this->getValue('old');
    }

    /**
     * Responsible for actually doing the grunt work for getting the
     * old or new value for the revision.
     *
     * @param string $which old or new
     */
    protected function getValue(string $which = 'new'): string|null
    {
        $whichValue = $which . '_value';

        // First find the main model that was updated
        $mainModel = $this->revisionable_type;
        // Load it, WITH the related model
        if (class_exists($mainModel)) {
            $mainModel = new $mainModel();

            try {
                if ($this->isRelated()) {
                    $relatedModel = $this->getRelatedModel();

                    // Now we can find out the namespace of related model
                    if (! method_exists($mainModel, $relatedModel)) {
                        $relatedModel = Str::camel($relatedModel); // for cases like published_status_id
                        if (! method_exists($mainModel, $relatedModel)) {
                            throw new Exception('Relation ' . $relatedModel . ' does not exist for ' . $mainModel);
                        }
                    }
                    $relatedClass = $mainModel->$relatedModel()->getRelated();

                    // Finally, now that we know the namespace of the related model
                    // we can load it, to find the information we so desire
                    $item = $relatedClass::find($this->$whichValue);

                    if ($this->$whichValue == '') {
                        $item = new $relatedClass();

                        return $item->getRevisionNullString();
                    }
                    if (! $item) {
                        $item = new $relatedClass();

                        return $this->format($this->key, $item->getRevisionUnknownString());
                    }

                    // Check if model use RevisionableTrait
                    if (method_exists($item, 'identifiableName')) {
                        // see if there's an available mutator
                        $mutator = 'get' . Str::studly($this->key) . 'Attribute';
                        if (method_exists($item, $mutator)) {
                            return $this->format($item->$mutator($this->key), $item->identifiableName());
                        }

                        return $this->format($this->key, $item->identifiableName());
                    }
                }
            } catch (Exception $exception) {
                // Just a fail-safe, in the case the data setup isn't as expected
                // Nothing to do here.
                info('Revisionable: ' . $exception);
            }

            // if there was an issue
            // or, if it's a normal value

            $mutator = 'get' . Str::studly($this->key) . 'Attribute';
            if (method_exists($mainModel, $mutator)) {
                return $this->format($this->key, $mainModel->$mutator($this->$whichValue));
            }
        }

        return $this->format($this->key, $this->$whichValue);
    }

    /**
     * Return true if the key is for a related model.
     */
    protected function isRelated(): bool
    {
        $isRelated = false;
        $idSuffix = '_id';
        $pos = strrpos($this->key, $idSuffix);

        if ($pos !== false && strlen($this->key) - strlen($idSuffix) === $pos) {
            $isRelated = true;
        }

        return $isRelated;
    }

    /**
     * Return the name of the related model.
     */
    protected function getRelatedModel(): string
    {
        $idSuffix = '_id';

        return substr($this->key, 0, strlen($this->key) - strlen($idSuffix));
    }

    /**
     * Format the value according to the $revisionFormattedFields array.
     */
    public function format(string $key, string|null $value): string|null
    {
        $relatedModel = $this->revisionable_type;
        $relatedModel = new $relatedModel();
        $revisionFormattedFields = $relatedModel->getRevisionFormattedFields();

        if (isset($revisionFormattedFields[$key])) {
            return FieldFormatter::format($key, $value, $revisionFormattedFields);
        }

        return $value;
    }

    /**
     * New Value.
     *
     * Grab the new value of the field, if it was a foreign key
     * attempt to get an identifying name for the model.
     */
    public function newValue(): string|null
    {
        return $this->getValue();
    }

    /**
     * User Responsible.
     *
     * @return bool|User
     */
    public function userResponsible()
    {
        if (empty($this->user_id)) {
            return false;
        }

        $userModel = config('auth.model');

        if (empty($userModel)) {
            $userModel = config('auth.providers.users.model');
            if (empty($userModel)) {
                return false;
            }
        }

        if (! class_exists($userModel)) {
            return false;
        }

        return $userModel::find($this->user_id);
    }

    /**
     * Returns the object we have the history of
     *
     * @return Object|false
     */
    public function historyOf()
    {
        if (class_exists($class = $this->revisionable_type)) {
            return $class::find($this->revisionable_id);
        }

        return false;
    }
}
