<?php

namespace app\Auth;

use app\Core\Session;
use app\Models\User;

class AuthUser
{
    public static function isAuth()
    {
        return Session::get('user') != null;
    }

    public static function isGuest()
    {
        return Session::get('user') == null;
    }

    public static function isAdmin()
    {
        return Session::get('user') && User::findOne(['id' => Session::get('user')])->role === 'admin';
    }

    public static function active()
    {
        $userId = Session::get('user');

        if (!$userId) {
            return;
        }

        $user = User::findOne(['id' => $userId]);

        if ($user) {
            return $user;
        }
    }

    public static function authUser()
    {
        return self::active();
    }
}