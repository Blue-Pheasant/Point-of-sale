<?php

namespace app\models;

use app\core\Database;
use app\core\UserModel;


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

    // Save này chỉ dùng lưu user, viết lại save khác cho model khác pls
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
            $userModel = new User;
            $params = array($item['id'], $item['firstname'], $item['lastname'], $item['email'], $item['password'], $item['phone_number'], $item['address'], $item['role']);
            $userModel->load($params);
            array_push($list, $userModel);
        }

        return $list;
    }

    public static function getUserInfo($id)
    {
        $db = Database::getInstance();
        $req = $db->query("SELECT * FROM users WHERE id = '$id'");
        $item = $req->fetchAll()[0];
        $user = new User();
        $user->id = $item['id'];
        $user->firstname = $item['firstname'];
        $user->lastname = $item['lastname'];
        $user->email = $item['email'];
        $user->address = $item['address'];
        $user->phone_number = $item['phone_number'];
        $user->role = $item['role'];
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

    public function update(User $user)
    {
        $statement = self::prepare(
            "UPDATE users 
            SET 
                firstname = '$user->firstname', 
                lastname = '$user->lastname',
                email = '$user->email',
                password = 'password_hash($user->password, PASSWORD_DEFAULT)',
                phone_number = '$user->phone_number',
                role = '$user->role',
                address = '$user->address'
            WHERE id = '$user->id';
            "
        );
        $statement->execute();
        return true;
    }

    public function delete()
    {
        $tablename = $this->tableName();
        $sql = "DELETE FROM $tablename WHERE id=?";
        $stmt= self::prepare($sql);
        $stmt->execute([$this->id]);
        return true;     
    }   
}