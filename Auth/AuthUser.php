<?php

namespace app\Auth;

use app\Core\Session;
use app\Models\User;

/**
 * Class AuthUser
 *
 * This class is responsible for handling the authentication state of the user in the application.
 * It provides methods to check if the user is authenticated, if the user is a guest, if the user is an admin,
 * and to get the active authenticated user.
 *
 * @package app\Auth
 */
class AuthUser
{
    /**
     * Method isAuth
     *
     * Checks if the user is authenticated.
     *
     * @return bool Returns true if the user is authenticated, false otherwise.
     */
    public static function isAuth(): bool
    {
        return Session::get('user') != null;
    }

    /**
     * Method isGuest
     *
     * Checks if the user is a guest.
     *
     * @return bool Returns true if the user is a guest, false otherwise.
     */
    public static function isGuest(): bool
    {
        return Session::get('user') == null;
    }

    /**
     * Method isAdmin
     *
     * Checks if the user is an admin.
     *
     * @return bool Returns true if the user is an admin, false otherwise.
     */
    public static function isAdmin(): bool
    {
        return Session::get('user') && User::findOne(['id' => Session::get('user')])->role === 'admin';
    }

    /**
     * Method active
     *
     * Gets the active authenticated user.
     *
     * @return User|null Returns the active authenticated user if exists, null otherwise.
     */
    public static function active(): ?User
    {
        $userId = Session::get('user');

        if (!$userId) {
            return null;
        }

        return User::findOne(['id' => $userId]);
    }

    /**
     * Method authUser
     *
     * Gets the active authenticated user.
     *
     * @return User|null Returns the active authenticated user if exists, null otherwise.
     */
    public static function authUser(): ?User
    {
        return self::active();
    }
}