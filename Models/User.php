<?php

namespace app\Models;

use app\Core\Database;
use app\Core\UserModel;


class User extends UserModel
{
    public string $id = '';
    public string $firstname = '';
    public string $lastname = '';
    public string $email = '';
    public string $password = '';
    public string $passwordConfirm = '';
    public string $address = '';
    public string $phone_number = '';
    public string $role = '';


    public function getId() { return $this->id; }
    public function getRole() { return $this->role; }
    public function setRole($role) { $this->role = $role; }
    public function getName() { return $this->getDisplayName(); }
    public function getEmail() { return $this->email; }
    public function getPhoneNumer() { return $this->phone_number; }
    public function getAddress() { return $this->address; }

    public function __construct($attributes = [])
    {
        parent::__construct($attributes);
    }

    public function load($params)
    {
        $this->id = $params[0];
        $this->firstname = $params[1];
        $this->lastname = $params[2];
        $this->email = $params[3];
        $this->password = $params[4];
        $this->phone_number = $params[5];
        $this->address = $params[6];
        $this->role = $params[7];
    }

    public static function tableName(): string
    {
        return 'users';
    }

    public function attributes(): array
    {
        return ['id', 'firstname', 'lastname', 'email', 'password', 'phone_number', 'address', 'role'];
    }

    public function labels(): array
    {
        return [
            'firstname' => 'Họ và tên đệm',
            'lastname' => 'Tên',
            'email' => 'Email',
            'password' => 'Mật khẩu',
            'passwordConfirm' => 'Nhập lại mật khẩu',
            'phone_number' => 'Số điện thoại',
            'address' => 'Địa chỉ',
            'role' => 'Vai trò'
        ];
    }

    public function getLabel($attribute)
    {
        return $this->labels()[$attribute];
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

    public function saveAdmin($role)
    {
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        $this->id = uniqid();
        $this->role = $role;
        return parent::save();
    }

    public function save()
    {
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        $this->id = uniqid();
        $this->role = 'client';
        return parent::save();
    }

    public function getDisplayName(): string
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    public static function getAllUsers()
    {
        $list = [];
        $db = Database::getInstance();
        $req = $db->query("SELECT * FROM users");

        foreach ($req->fetchAll() as $item) {
            $userModel = new User($item);
            array_push($list, $userModel);
        }

        return $list;
    }

    public static function getUserInfo($id)
    {
        $db = Database::getInstance();
        $req = $db->query("SELECT * FROM users WHERE id = '$id'");
        $item = $req->fetchAll()[0];
        $user = new User($item);
        return $user;
    }

    public static function updateProfile($user)
    {
        $statement = self::prepare(
            "UPDATE users 
            SET 
                firstname = '$user->firstname', 
                lastname = '$user->lastname',
                phone_number = '$user->phone_number',
                address = '$user->address'
            WHERE id = '$user->id';
            "
        );
        $statement->execute();
        return true;
    }
}