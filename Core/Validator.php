<?php

namespace app\Core;

/**
 * Central validation engine.
 *
 * Rules are declared as an array keyed by attribute name. Each attribute maps to
 * a list of rule descriptors. A descriptor is either:
 *   - a plain string:  'required', 'email', 'number', 'numeric', 'integer', 'url',
 *                      'date', 'time', 'datetime'
 *   - a colon string:  'min:5', 'max:100', 'match:password_confirm',
 *                      'unique:app\Models\User', 'date:before:end_date',
 *                      'date:after:start_date'
 *   - an array:        [self::RULE_MIN, 'min' => 5]
 *                      [self::RULE_MAX, 'max' => 100]
 *                      [self::RULE_MATCH, 'match' => 'password_confirm']
 *                      [self::RULE_UNIQUE, 'class' => User::class, 'attribute' => 'email']
 *                      [self::RULE_MIN_VALUE, 'minint' => 0]
 *                      [self::RULE_MAX_VALUE, 'maxint' => 999]
 */
class Validator
{
    public const RULE_REQUIRED    = 'required';
    public const RULE_EMAIL       = 'email';
    public const RULE_MIN         = 'min';
    public const RULE_MAX         = 'max';
    public const RULE_MATCH       = 'match';
    public const RULE_UNIQUE      = 'unique';
    public const RULE_NUMBER      = 'number';
    public const RULE_NUMERIC     = 'numeric';
    public const RULE_INTEGER     = 'integer';
    public const RULE_URL         = 'url';
    public const RULE_DATE        = 'date';
    public const RULE_TIME        = 'time';
    public const RULE_DATETIME    = 'datetime';
    public const RULE_DATE_BEFORE = 'date:before';
    public const RULE_DATE_AFTER  = 'date:after';
    public const RULE_MIN_VALUE   = 'minint';
    public const RULE_MAX_VALUE   = 'maxint';

    public const RULE_INVALID_EMAIL    = 'invalid email';
    public const RULE_WRONG_PASSWORD   = 'wrong password';
    public const RULE_INVALID_ID       = 'invalid id';

    /** @var array<string, list<string>> */
    private array $errors = [];

    /**
     * Validate $data against $rules.
     *
     * $data  – flat key/value map of the values being validated.
     * $rules – attribute => list of rule descriptors (see class docblock).
     *
     * Returns true when all rules pass.
     */
    public function validate(array $data, array $rules): bool
    {
        foreach ($rules as $attribute => $attributeRules) {
            $value = $data[$attribute] ?? null;
            foreach ($attributeRules as $rule) {
                $this->applyRule($attribute, $value, $rule, $data);
            }
        }

        return empty($this->errors);
    }

    /** @return array<string, list<string>> */
    public function getErrors(): array
    {
        return $this->errors;
    }

    public function hasError(string $attribute): bool
    {
        return !empty($this->errors[$attribute]);
    }

    public function getFirstError(string $attribute): string|false
    {
        return $this->errors[$attribute][0] ?? false;
    }

    public function addError(string $attribute, string $rule, array $params = []): void
    {
        $message = $this->errorMessages()[$rule] ?? $rule;
        foreach ($params as $key => $value) {
            $message = str_replace("{{$key}}", (string) $value, $message);
        }
        $this->errors[$attribute][] = $message;
    }

    // -----------------------------------------------------------------------
    // Internal helpers
    // -----------------------------------------------------------------------

    private function applyRule(string $attribute, mixed $value, mixed $rule, array $data): void
    {
        // Normalise to [$ruleName, ...params].
        [$ruleName, $params] = $this->parseRule($rule);

        switch ($ruleName) {
            case self::RULE_REQUIRED:
                if ($value === null || $value === '' || $value === false) {
                    $this->addError($attribute, self::RULE_REQUIRED);
                }
                break;

            case self::RULE_EMAIL:
                if ($value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($attribute, self::RULE_EMAIL);
                }
                break;

            case self::RULE_MIN:
                $min = (int) ($params['min'] ?? 0);
                if ($value !== null && strlen((string) $value) < $min) {
                    $this->addError($attribute, self::RULE_MIN, ['min' => $min]);
                }
                break;

            case self::RULE_MAX:
                $max = (int) ($params['max'] ?? PHP_INT_MAX);
                if ($value !== null && strlen((string) $value) > $max) {
                    $this->addError($attribute, self::RULE_MAX, ['max' => $max]);
                }
                break;

            case self::RULE_MATCH:
                $matchField = $params['match'] ?? '';
                $matchValue = $data[$matchField] ?? null;
                if ($value !== $matchValue) {
                    $this->addError($attribute, self::RULE_MATCH, ['match' => $matchField]);
                }
                break;

            case self::RULE_NUMBER:
            case self::RULE_NUMERIC:
                if ($value !== null && $value !== '' && !is_numeric($value)) {
                    $this->addError($attribute, self::RULE_NUMBER);
                }
                break;

            case self::RULE_INTEGER:
                if ($value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_INT)) {
                    $this->addError($attribute, self::RULE_INTEGER);
                }
                break;

            case self::RULE_URL:
                if ($value !== null && $value !== '' && !filter_var($value, FILTER_VALIDATE_URL)) {
                    $this->addError($attribute, self::RULE_URL);
                }
                break;

            case self::RULE_DATE:
                if ($value !== null && $value !== '' && !date_create((string) $value)) {
                    $this->addError($attribute, self::RULE_DATE);
                }
                break;

            case self::RULE_TIME:
                if ($value !== null && $value !== '' && !date_create_from_format('H:i:s', (string) $value)) {
                    $this->addError($attribute, self::RULE_TIME);
                }
                break;

            case self::RULE_DATETIME:
                if ($value !== null && $value !== '' && !date_create_from_format('Y-m-d H:i:s', (string) $value)) {
                    $this->addError($attribute, self::RULE_DATETIME);
                }
                break;

            case self::RULE_DATE_BEFORE:
                $beforeField = $params['before'] ?? '';
                $date        = $value !== null ? date_create((string) $value) : null;
                $beforeDate  = isset($data[$beforeField]) ? date_create((string) $data[$beforeField]) : null;
                if ($date && $beforeDate && $date > $beforeDate) {
                    $this->addError($attribute, self::RULE_DATE_BEFORE, ['before' => $beforeField]);
                }
                break;

            case self::RULE_DATE_AFTER:
                $afterField = $params['after'] ?? '';
                $date       = $value !== null ? date_create((string) $value) : null;
                $afterDate  = isset($data[$afterField]) ? date_create((string) $data[$afterField]) : null;
                if ($date && $afterDate && $date < $afterDate) {
                    $this->addError($attribute, self::RULE_DATE_AFTER, ['after' => $afterField]);
                }
                break;

            case self::RULE_MIN_VALUE:
                $minint = (int) ($params['minint'] ?? 0);
                if ($value !== null && $value !== '' && (int) $value < $minint) {
                    $this->addError($attribute, self::RULE_MIN_VALUE, ['minint' => $minint]);
                }
                break;

            case self::RULE_MAX_VALUE:
                $maxint = (int) ($params['maxint'] ?? PHP_INT_MAX);
                if ($value !== null && $value !== '' && (int) $value > $maxint) {
                    $this->addError($attribute, self::RULE_MAX_VALUE, ['maxint' => $maxint]);
                }
                break;

            case self::RULE_UNIQUE:
                $className       = $params['class'] ?? '';
                $uniqueAttribute = $params['attribute'] ?? $attribute;
                if ($className !== '' && $value !== null && $value !== '') {
                    $record = $className::findOne([$uniqueAttribute => $value]);
                    if ($record) {
                        $this->addError($attribute, self::RULE_UNIQUE, ['field' => $attribute]);
                    }
                }
                break;
        }
    }

    /**
     * Parse any rule descriptor into [ruleName, params].
     *
     * Accepts:
     *   - 'required'                       → ['required', []]
     *   - 'min:5'                          → ['min',      ['min' => '5']]
     *   - 'max:100'                        → ['max',      ['max' => '100']]
     *   - 'match:confirm'                  → ['match',    ['match' => 'confirm']]
     *   - 'unique:App\Models\User'         → ['unique',   ['class' => 'App\Models\User']]
     *   - 'date:before:end_date'           → ['date:before', ['before' => 'end_date']]
     *   - 'date:after:start_date'          → ['date:after',  ['after'  => 'start_date']]
     *   - [RULE_MIN, 'min' => 5]           → ['min',      ['min' => 5]]
     *   - [RULE_UNIQUE, 'class' => User::class] → ['unique', ['class' => User::class]]
     *
     * @return array{0: string, 1: array<string, mixed>}
     */
    private function parseRule(mixed $rule): array
    {
        if (is_array($rule)) {
            $name   = (string) $rule[0];
            $params = array_filter(
                $rule,
                static fn ($k) => $k !== 0,
                ARRAY_FILTER_USE_KEY
            );
            return [$name, $params];
        }

        $rule = (string) $rule;

        // date:before:<field> / date:after:<field>
        if (str_starts_with($rule, 'date:before:')) {
            return [self::RULE_DATE_BEFORE, ['before' => substr($rule, 12)]];
        }

        if (str_starts_with($rule, 'date:after:')) {
            return [self::RULE_DATE_AFTER, ['after' => substr($rule, 11)]];
        }

        // name:value  (min:5, max:100, match:field, unique:ClassName)
        if (str_contains($rule, ':')) {
            [$name, $value] = explode(':', $rule, 2);
            $paramKey = match ($name) {
                'min'    => 'min',
                'max'    => 'max',
                'match'  => 'match',
                'unique' => 'class',
                default  => $name,
            };
            return [$name, [$paramKey => $value]];
        }

        return [$rule, []];
    }

    private function errorMessages(): array
    {
        return [
            self::RULE_UNIQUE        => '{field} đã tồn tại.',
            self::RULE_REQUIRED      => 'Trường dữ liệu này bắt buộc.',
            self::RULE_EMAIL         => 'Trường dữ liệu này phải là email hợp lệ.',
            self::RULE_MIN           => 'Ít nhất {min} ký tự.',
            self::RULE_MAX           => 'Nhiều nhất {max} ký tự.',
            self::RULE_MIN_VALUE     => 'Số lượng ít nhất phải lớn hơn {minint}.',
            self::RULE_MAX_VALUE     => 'Số lượng ít nhất phải bé hơn {maxint}.',
            self::RULE_MATCH         => 'Trường dữ liệu này phải trùng với {match}.',
            self::RULE_NUMBER        => 'Trường dữ liệu này phải là dạng số.',
            self::RULE_NUMERIC       => 'Trường dữ liệu này phải là dạng số.',
            self::RULE_INTEGER       => 'Trường dữ liệu này phải là số nguyên.',
            self::RULE_URL           => 'Trường dữ liệu này phải là URL hợp lệ.',
            self::RULE_DATE          => 'Trường dữ liệu này phải là ngày hợp lệ.',
            self::RULE_TIME          => 'Trường dữ liệu này phải là giờ hợp lệ.',
            self::RULE_DATETIME      => 'Trường dữ liệu này phải là ngày giờ hợp lệ.',
            self::RULE_DATE_BEFORE   => 'Ngày phải trước {before}.',
            self::RULE_DATE_AFTER    => 'Ngày phải sau {after}.',
            self::RULE_INVALID_EMAIL => 'Email chưa được đăng ký.',
            self::RULE_INVALID_ID    => 'Người dùng chưa được đăng ký.',
            self::RULE_WRONG_PASSWORD => 'Mật khẩu không chính xác.',
        ];
    }
}
