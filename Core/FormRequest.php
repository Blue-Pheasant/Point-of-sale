<?php

namespace app\Core;

/**
 * Base class for request-scoped form validation.
 *
 * Concrete subclasses declare rules(), messages(), and labels(). The __construct
 * now correctly assigns merged params + body into $this->data before validating.
 *
 * All rule constants are re-exported from Validator so callers don't need to
 * change their imports.
 */
abstract class FormRequest extends Request
{
    // Rule constant aliases (kept for backward compatibility with subclasses).
    protected const RULE_REQUIRED    = Validator::RULE_REQUIRED;
    protected const RULE_EMAIL       = Validator::RULE_EMAIL;
    protected const RULE_MIN         = Validator::RULE_MIN;
    protected const RULE_MAX         = Validator::RULE_MAX;
    protected const RULE_MATCH       = Validator::RULE_MATCH;
    protected const RULE_NUMERIC     = Validator::RULE_NUMERIC;
    protected const RULE_INTEGER     = Validator::RULE_INTEGER;
    protected const RULE_URL         = Validator::RULE_URL;
    protected const RULE_DATE        = Validator::RULE_DATE;
    protected const RULE_TIME        = Validator::RULE_TIME;
    protected const RULE_DATETIME    = Validator::RULE_DATETIME;
    protected const RULE_DATE_BEFORE = Validator::RULE_DATE_BEFORE;
    protected const RULE_DATE_AFTER  = Validator::RULE_DATE_AFTER;
    protected const RULE_UNIQUE      = Validator::RULE_UNIQUE;

    /** @var array<string, mixed> */
    protected array $data = [];

    /** @var array<string, list<string>> */
    protected array $errors = [];

    /**
     * Merges query params and request body into $this->data, then validates.
     * Bug fix: the old code called array_merge() without assigning the result.
     */
    public function __construct()
    {
        $this->data = array_merge($this->getParams(), $this->getBody());
        $this->validate();
    }

    abstract protected function rules(): array;

    abstract protected function messages(): array;

    abstract protected function labels(): array;

    protected function getLabel(string $attribute): string
    {
        return $this->labels()[$attribute] ?? $attribute;
    }

    public function validate(): bool
    {
        $validator = new Validator();
        $result    = $validator->validate($this->data, $this->rules());
        $this->errors = $validator->getErrors();
        return $result;
    }

    public function addError(string $attribute, string $rule, array $params = []): void
    {
        $validator = new Validator();
        $validator->addError($attribute, $rule, $params);
        foreach ($validator->getErrors() as $attr => $messages) {
            foreach ($messages as $msg) {
                $this->errors[$attr][] = $msg;
            }
        }
    }

    /** @return array<string, list<string>> */
    public function errors(): array
    {
        return $this->errors;
    }
}
