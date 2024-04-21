<?php

namespace app\Models;

use app\Core\Model;
use app\Core\Session;

class LoginForm extends Model
{
    public string $email = '';
    public string $password = '';
    public string $userId = '';

    public function rules(): array
    {
        return [
            'email' => [self::RULE_REQUIRED],
            'password' => [self::RULE_REQUIRED],
        ];
    }

    public function labels(): array
    {
        return [
            'email' => 'Địa chỉ email',
            'password' => 'Mật khẩu'
        ];
    }

    public function login($input)
    {
        if($input == "email") {
            $user = User::findOne(['email' => $this->email]);
            if (!$user) {
                $this->addError('email', self::RULE_INVALID_ID);
                return false;
            }
            if (!password_verify($this->password, $user->password)) {
                $this->addError('password', self::RULE_WRONG_PASSWORD);
                return false;
            }
        } else {
            $user = User::findOne(['id' => $this->userId]);
            if (!$user) {
                $this->addError('id', self::RULE_INVALID_EMAIL);
                return false;
            }
        }

        // Get cart Id
        $userCart = Cart::getCart($user->id);
        if (!$userCart) {
            $userCart = Cart::create($user->id);
        }

        Session::set('cart_id', $userCart->id);
        Session::set('user', $user->id);
        
        return true;
    }

    public function getLabel(string $attribute): string
    {
        return $this->labels()[$attribute];
    }
}