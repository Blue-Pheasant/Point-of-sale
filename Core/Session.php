<?php

namespace app\Core;

/**
 * Session wrapper with secure cookie params and flash message support.
 */
class Session
{
    protected const FLASH_KEY = 'flash_messages';

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            $secure = (($_ENV['APP_ENV'] ?? 'dev') === 'production');
            session_set_cookie_params([
                'lifetime' => 0,
                'path'     => '/',
                'domain'   => '',
                'secure'   => $secure,
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
            session_start();
        }

        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];
        foreach ($flashMessages as $key => &$flashMessage) {
            $flashMessage['remove'] = true;
        }
        unset($flashMessage);
        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }

    public static function setFlash(string $key, string $message): void
    {
        $_SESSION[self::FLASH_KEY][$key] = [
            'remove' => false,
            'value'  => $message,
        ];
    }

    public static function getFlash(string $key): mixed
    {
        return $_SESSION[self::FLASH_KEY][$key]['value'] ?? false;
    }

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key): mixed
    {
        return $_SESSION[$key] ?? false;
    }

    public static function remove(string $key): void
    {
        if (self::exists($key)) {
            unset($_SESSION[$key]);
        }
    }

    public static function exists(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public function __destruct()
    {
        self::removeFlashMessages();
    }

    private static function removeFlashMessages(): void
    {
        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];
        foreach ($flashMessages as $key => $flashMessage) {
            if ($flashMessage['remove']) {
                unset($flashMessages[$key]);
            }
        }
        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }
}
