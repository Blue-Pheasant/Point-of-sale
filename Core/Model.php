<?php

namespace app\Core;

/**
 * Base model providing validation via the central Validator engine.
 *
 * Subclasses declare rules() using Validator rule constants; validate()
 * delegates entirely to Validator so there is no duplicated logic.
 */
abstract class Model
{
    // -----------------------------------------------------------------------
    // Rule constants — thin aliases to Validator so callers don't change.
    // -----------------------------------------------------------------------
    public const RULE_REQUIRED      = Validator::RULE_REQUIRED;
    public const RULE_EMAIL         = Validator::RULE_EMAIL;
    public const RULE_MIN           = Validator::RULE_MIN;
    public const RULE_MAX           = Validator::RULE_MAX;
    public const RULE_MATCH         = Validator::RULE_MATCH;
    public const RULE_UNIQUE        = Validator::RULE_UNIQUE;
    public const RULE_NUMBER        = Validator::RULE_NUMBER;
    public const RULE_NUMERIC       = Validator::RULE_NUMERIC;
    public const RULE_INTEGER       = Validator::RULE_INTEGER;
    public const RULE_MIN_VALUE     = Validator::RULE_MIN_VALUE;
    public const RULE_MAX_VALUE     = Validator::RULE_MAX_VALUE;
    public const RULE_INVALID_EMAIL = Validator::RULE_INVALID_EMAIL;
    public const RULE_WRONG_PASSWORD = Validator::RULE_WRONG_PASSWORD;
    public const RULE_INVALID_ID    = Validator::RULE_INVALID_ID;

    /** @var array<string, list<string>> */
    public array $errors = [];

    abstract public function rules(): array;

    public function labels(): array
    {
        return [];
    }

    public function getLabel(string $attribute): string
    {
        return $this->labels()[$attribute] ?? $attribute;
    }

    public function loadData(array $data): void
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    /**
     * Collect current attribute values into a flat array for the Validator.
     */
    protected function attributeData(): array
    {
        $data = [];
        foreach (array_keys($this->rules()) as $attribute) {
            $data[$attribute] = property_exists($this, $attribute) ? $this->{$attribute} : null;
        }
        return $data;
    }

    public function validate(): bool
    {
        $validator = new Validator();
        $result    = $validator->validate($this->attributeData(), $this->rules());
        $this->errors = $validator->getErrors();
        return $result;
    }

    public function addError(string $attribute, string $rule, array $params = []): void
    {
        $validator = new Validator();
        $validator->addError($attribute, $rule, $params);
        // Merge single error into $this->errors
        foreach ($validator->getErrors() as $attr => $messages) {
            foreach ($messages as $msg) {
                $this->errors[$attr][] = $msg;
            }
        }
    }

    public function hasError(string $attribute): bool
    {
        return !empty($this->errors[$attribute]);
    }

    public function getFirstError(string $attribute): string|false
    {
        return $this->errors[$attribute][0] ?? false;
    }

    /**
     * @deprecated Returns current model errors; kept for backward compatibility only.
     */
    public function errorMessage(): array
    {
        return $this->errors;
    }
}
