<?php

namespace app\Core;

/**
 * Class Model
 *
 * This class provides a set of constants that represent the names of the validation rules.
 *
 * @package app\Core
 */
abstract class Model
{
    /**
     * @var string RULE_REQUIRED The 'required' validation rule.
     */
    public const RULE_REQUIRED = 'required';

    /**
     * @var string RULE_EMAIL The 'email' validation rule.
     */
    public const RULE_EMAIL = 'email';

    /**
     * @var string RULE_MIN The 'min' validation rule.
     */
    public const RULE_MIN = 'min';

    /**
     * @var string RULE_MAX The 'max' validation rule.
     */
    public const RULE_MAX = 'max';

    /**
     * @var string RULE_MATCH The 'match' validation rule.
     */
    public const RULE_MATCH = 'match';

    /**
     * @var string RULE_UNIQUE The 'unique' validation rule.
     */
    public const RULE_UNIQUE = 'unique';

    /**
     * @var string RULE_NUMBER The 'number' validation rule.
     */
    public const RULE_NUMBER = 'number';

    /**
     * @var string RULE_INVALID_EMAIL The 'invalid email' validation rule.
     */
    public const RULE_INVALID_EMAIL = 'invalid email';

    /**
     * @var string RULE_WRONG_PASSWORD The 'wrong password' validation rule.
     */
    public const RULE_WRONG_PASSWORD = 'wrong password';

    /**
     * @var string RULE_INVALID_ID The 'invalid id' validation rule.
     */
    public const RULE_INVALID_ID = 'invalid id';

    /**
     * @var string RULE_MIN_VALUE The 'minint' validation rule.
     */
    public const RULE_MIN_VALUE = 'minint';

    /**
     * @var string RULE_MAX_VALUE The 'maxint' validation rule.
     */
    public const RULE_MAX_VALUE = 'maxint';

    /**
     * Method rules
     *
     * Returns the validation rules.
     *
     * @return array
     */
    abstract public function rules(): array;

    /**
     * Method getLabel
     *
     * Returns the label of the attribute.
     *
     * @param string $attribute The attribute to get the label of.
     * @return string
     */
    public function getLabel(string $attribute): string
    {
        return $this->labels()[$attribute] ?? $attribute;
    }

    /**
     * Method labels
     *
     * Returns the labels of the attributes.
     *
     * @return array
     */
    public array $errors = [];

    /**
     * Method loadData
     *
     * Loads the data into the model.
     *
     * @param array $data The data to load into the model.
     */
    public function loadData(array $data): void
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    /**
     * Method validate
     * 
     * Validates the data of the form request.
     * 
     * @return bool
     */
    public function validate(): bool
    {
        foreach ($this->rules() as $attribute => $rules) {
            $value = $this->{$attribute};
            foreach ($rules as $rule) {
                $ruleName = $rule;
                if (!is_string($ruleName)) {
                    $ruleName = $rule[0];
                }
                if ($ruleName === self::RULE_REQUIRED && !$value) {
                    $this->addError($attribute, self::RULE_REQUIRED);
                }
                if ($ruleName === self::RULE_EMAIL && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($attribute, self::RULE_EMAIL);
                }
                if ($ruleName === self::RULE_MIN && strlen($value) < $rule['min']) {
                    $this->addError($attribute, self::RULE_MIN, $rule);
                }
                if ($ruleName === self::RULE_MAX && strlen($value) > $rule['max']) {
                    $this->addError($attribute, self::RULE_MAX, $rule);
                }
                if ($ruleName === self::RULE_MATCH && $value !== $this->{$rule['match']}) {
                    $this->addError($attribute, self::RULE_MATCH, $rule);
                }
                if ($ruleName === self::RULE_REQUIRED && !$value) {
                    $this->addError($attribute, self::RULE_REQUIRED);
                }
                if ($ruleName === self::RULE_MIN_VALUE && (int)$value < $rule['minint']) {
                    $this->addError($attribute, self::RULE_MIN_VALUE, $rule);
                }
                if ($ruleName === self::RULE_MAX_VALUE && (int)$value > $rule['maxint']) {
                    $this->addError($attribute, self::RULE_MAX_VALUE, $rule);
                }
                if ($ruleName === self::RULE_UNIQUE) {
                    $className = $rule['class'];
                    $uniqueAttribute = $rule['attribute'] ?? $attribute;
                    $tableName = $className::tableName();
                    $statement = Application::$app->db->prepare("
                        SELECT * FROM $tableName WHERE $uniqueAttribute = :$uniqueAttribute;
                    ");
                    $statement->bindValue(":$uniqueAttribute", $value);
                    $statement->execute();
                    $record = $statement->fetchObject();
                    if ($record) {
                        $this->addError($attribute, self::RULE_UNIQUE, ['field' => $attribute]);
                    }
                }
            }
        }

        return empty($this->errors);
    }


    /**
     * Adds an error to the form request.
     *
     * @param string $attribute The attribute of the error.
     * @param string $rule The rule of the error.
     * @param array $params The parameters of the error.
     */
    public function addError(string $attribute, string $rule, array $params = []): void
    {
        $message = $this->errorMessage()[$rule] ?? '';
        foreach ($params as $key => $value) {
            $message = str_replace("{{$key}}", $value, $message);
        }
        $this->errors[$attribute][] = $message;
    }

    /**
     * Returns the error messages of the form request.
     *
     * @return array The error messages of the form request.
     */
    public function errorMessage(): array
    {
        return [
            self::RULE_UNIQUE => '{field} đã tồn tại.',
            self::RULE_REQUIRED => 'Trường dữ liệu này bắt buộc.',
            self::RULE_EMAIL => 'Trường dữ liệu này phải là email hợp lệ.',
            self::RULE_MIN => 'Ít nhất {min} ký tự.',
            self::RULE_MAX => 'Nhiều nhất {max} ký tự.',
            self::RULE_MIN_VALUE => 'Số lượng ít nhất phải lớn hơn {minint}.',
            self::RULE_MAX_VALUE => 'Số lượng ít nhất phải bé hơn {maxint}.',
            self::RULE_MATCH => 'Trường dữ liệu này phải trùng với {match}.',
            self::RULE_NUMBER => 'Trường dữ liệu này phải là dạng số.',
            self::RULE_INVALID_EMAIL => 'Email chưa được đăng ký.',
            self::RULE_INVALID_ID => 'Người dùng chưa được đăng ký.',
            self::RULE_WRONG_PASSWORD => 'Mật khẩu không chính xác.',
        ];
    }

    /**
     * Adds an error to the form request.
     *
     * @param string $attribute The attribute of the error.
     * @return bool Whether the form request has an error for the attribute.
     */
    public function hasError(string $attribute): bool
    {
        return $this->errors[$attribute] ?? false;
    }

    /**
     * Returns the first error for the given attribute.
     *
     * This method returns the first error for the given attribute in the 'errors' property of the Model object.
     * If there is no error for the attribute, it returns false.
     *
     * @param string $attribute The attribute to get the first error for.
     * @return string|bool The first error for the attribute, or false if there is no error.
     */
    public function getFirstError(string $attribute): bool|string
    {
        return $this->errors[$attribute][0] ?? false;
    }
}