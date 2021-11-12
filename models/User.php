<?php

namespace app\models;

use app\core\Database;
use app\core\UserModel;
use PDO;
use PDOException;

class User extends UserModel
{
    public string $id;
    public string $firstname;
    public string $lastname;
    public string $email;
    public string $password;
    public string $passwordConfirm;
    public string $address;
    public string $phone_number;

    public function __construct(
        $id  = '',
        $firstname = '',
        $lastname = '',
        $email = '',
        $password = '',
        $passwordConfirm = '',
        $address= '',
        $phone_number = ''
    ) {
        $this->id = $id;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->email = $email;
        $this->password = $password;
        $this->passwordConfirm = $passwordConfirm;
        $this->address = $address;
        $this->phone_number = $phone_number;
    }

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

    public static function getAll()
    {
        $list = [];
        $db = Database::getInstance();
        $req = $db->query('SELECT * FROM users');

        foreach ($req->fetchAll() as $item) {
            $list[] = new User($item['id'], $item['firstname'], $item['lastname'], $item['password'], $item['passwordConfirm'], $item['address'], $item['phone_number']);
        }

        return $list;
    }

    public static function get($id)
    {
        $db = Database::getInstance();
        $req = $db->query('SELECT * FROM products WHERE id = "' . $id . '"');
        $item = $req->fetchAll()[0];
        $product = new User($item['id'], $item['firstname'], $item['lastname'], $item['password'], $item['passwordConfirm'], $item['address'], $item['phone_number']);
        return $product;
    }

    public function delete()
    {
        $tablename = $this->tableName();
        $id = $this->id;
        $sql = "DELETE FROM $tablename WHEHRE ID = :ID";
        $statement = self::prepare($sql);
        $statement->bindParam(':ID', $id, PDO::PARAM_INT);
        $statement->execute();        
    }
}