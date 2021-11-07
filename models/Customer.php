<?php

namespace app\models;

use app\core\CustomerModel;
use app\core\Model;

class Customer extends CustomerModel
{
    public string $id = '';
    public string $firstname = '';
    public string $lastname = '';
    public string $email = '';
    public string $password = '';
    public string $passwordConfirm = '';
    public string $address = '';
    public string $phone_number = '';


    public static function tableName(): string
    {
        return 'customers';
    }

    public function attributes(): array
    {
        return ['id', 'firstname', 'lastname', 'email', 'password', 'phone_number', 'address'];
    }

    public function labels(): array
    {
        return [
            'firstname' => 'First name',
            'lastname' => 'Last name',
            'email' => 'Email',
            'password' => 'Password',
            'passwordConfirm' => 'Password Confirm',
            'phone_number' => 'Phone number',
            'address' => 'Address',
        ];
    }

    public function rules(): array
    {
        return [
            'firstname' => [self::RULE_REQUIRED],
            'lastname' => [self::RULE_REQUIRED],
            'email' => [self::RULE_REQUIRED, self::RULE_EMAIL, [
                self::RULE_UNIQUE, 'class' => self::class
            ]],
            'password' => [self::RULE_REQUIRED, [self::RULE_MIN, 'min' => 8]],
            'passwordConfirm' => [[self::RULE_MATCH, 'match' => 'password']],
        ];
    }

    public function save()
    {
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        $this->id = uniqid();
        return parent::save();
    }

    public function getDisplayName(): string
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function view()
    {
        $this->view->render('customer/view');
    }
}