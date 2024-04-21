<?php

namespace app\Core;

/**
 * Class Session
 *
 * This class is responsible for handling the session operations of the application.
 *
 * @package app\Core
 */
class Session
{
    /**
     * @var string FLASH_KEY The key of the flash messages.
     */
    protected const FLASH_KEY = 'flash_messages';

    /**
     * Session constructor.
     */
    public function __construct()
    {
        session_start();
        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];
        foreach ($flashMessages as $key => &$flashMessage) {
            $flashMessage['remove'] = true;
        }
        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }

    /**
    * Sets a flash message.
    *
    * This method sets a flash message in the $_SESSION super-global array.
    * The flash message is an associative array with a 'remove' key set to false and a 'value' key set to the specified message.
    *
    * @param string $key The key of the flash message.
    * @param string $message The message of the flash message.
    * @return void
    */
    public static function setFlash(string $key, string $message): void
    {
        $_SESSION[self::FLASH_KEY][$key] = [
            'remove' => false,
            'value' => $message
        ];
    }

    /**
     * Retrieves a flash message.
     *
     * This method retrieves a flash message from the $_SESSION super-global array.
     * If the flash message does not exist, it returns false.
     *
     * @param string $key The key of the flash message to retrieve.
     * @return mixed The flash message if it exists, false otherwise.
     */
    public static function getFlash(string $key): mixed
    {
        return $_SESSION[self::FLASH_KEY][$key]['value'] ?? false;
    }

    /**
     * Removes a flash message.
     *
     * This method removes a flash message from the $_SESSION super-global array.
     *
     * @param string $key The key of the flash message to remove.
     * @param string $value The value of the flash message to remove.
     * @return void
     */
    public static function set(string $key, string $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Retrieves a session value.
     *
     * This method retrieves a session value from the $_SESSION super-global array.
     * If the session value does not exist, it returns false.
     *
     * @param string $key The key of the session value to retrieve.
     * @return mixed The session value if it exists, false otherwise.
     */
    public static function get(string $key): mixed
    {
        return $_SESSION[$key] ?? false;
    }

    /**
     * Removes a session value.
     *
     * This method removes a session value from the $_SESSION super-global array.
     *
     * @param string $key The key of the session value to remove.
     * @return void
     */
    public static function remove(string $key): void
    {
        if(self::exists($key)){
			unset($_SESSION[$key]);
		}
    }

    /**
     * Destroys the session.
     *
     * This method destroys the session and unsets the $_SESSION super-global array.
     *
     * @return void
     */
    public function __destruct()
    {
        $this->removeFlashMessages();
    }

    /**
     * Removes flash messages.
     *
     * This method removes flash messages from the $_SESSION super-global array.
     *
     * @return void
     */
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

    /**
     * Checks if a session variable exists.
     *
     * This method checks if a session variable with the specified key exists in the $_SESSION super-global array.
     * It returns true if the session variable exists, and false otherwise.
     *
     * @param string $key The key of the session variable to check.
     * @return bool True if the session variable exists, false otherwise.
     */
    public static function exists(string $key): bool
    {
		return (isset($_SESSION[$key]));
	}
}