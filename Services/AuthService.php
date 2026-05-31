<?php

namespace app\Services;

use app\Core\Session;
use app\Core\Uuid;
use app\Models\User;
use PDO;

class AuthService
{
    private const COOKIE_NAME    = 'member_login';
    private const COOKIE_DAYS    = 30;

    public function __construct(private PDO $db)
    {
    }

    public function login(string $email, string $password): ?User
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM users WHERE email = :email AND deleted_at IS NULL LIMIT 1'
        );
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        $user = $stmt->fetchObject(User::class);
        if (!($user instanceof User)) {
            return null;
        }

        if (!password_verify($password, $user->password)) {
            return null;
        }

        return $user;
    }

    public function register(array $data): bool
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare(
                'INSERT INTO users (email, password, role) VALUES (:email, :password, :role)'
            );
            $stmt->bindValue(':email', $data['email'], PDO::PARAM_STR);
            $stmt->bindValue(':password', $data['password'], PDO::PARAM_STR);
            $stmt->bindValue(':role', $data['role'], PDO::PARAM_STR);
            $stmt->execute();

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }

    public function logout(): void
    {
        Session::remove('user');

        if (isset($_COOKIE[self::COOKIE_NAME])) {
            $this->deleteRememberToken($_COOKIE[self::COOKIE_NAME]);
            $secure = (($_ENV['APP_ENV'] ?? 'dev') === 'production');
            setcookie(self::COOKIE_NAME, '', [
                'expires'  => time() - 3600,
                'path'     => '/',
                'secure'   => $secure,
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
        }

        session_destroy();
    }

    /**
     * Persist a random remember-me token to the DB and set the cookie.
     */
    public function setRememberToken(string $userId): void
    {
        $token    = bin2hex(random_bytes(32));
        $hash     = hash('sha256', $token);
        $id       = Uuid::v4();
        $expires  = date('Y-m-d H:i:s', time() + 3600 * 24 * self::COOKIE_DAYS);

        $stmt = $this->db->prepare(
            'INSERT INTO remember_tokens (id, user_id, token_hash, expires_at)
             VALUES (:id, :user_id, :token_hash, :expires_at)'
        );
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_STR);
        $stmt->bindValue(':token_hash', $hash, PDO::PARAM_STR);
        $stmt->bindValue(':expires_at', $expires, PDO::PARAM_STR);
        $stmt->execute();

        $secure = (($_ENV['APP_ENV'] ?? 'dev') === 'production');
        setcookie(self::COOKIE_NAME, $token, [
            'expires'  => time() + 3600 * 24 * self::COOKIE_DAYS,
            'path'     => '/',
            'secure'   => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }

    /**
     * Auto-login from the remember-me cookie by verifying the token hash.
     */
    public function loginWithCookie(): void
    {
        if (Session::exists('user')) {
            return;
        }

        if (!isset($_COOKIE[self::COOKIE_NAME])) {
            return;
        }

        $token = $_COOKIE[self::COOKIE_NAME];
        $hash  = hash('sha256', $token);

        try {
            $stmt = $this->db->prepare(
                'SELECT rt.user_id FROM remember_tokens rt
                  WHERE rt.token_hash = :hash
                    AND rt.expires_at > NOW()
                  LIMIT 1'
            );
            $stmt->bindValue(':hash', $hash, PDO::PARAM_STR);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_OBJ);
            if (!$row) {
                return;
            }

            $userStmt = $this->db->prepare(
                'SELECT * FROM users WHERE id = :id AND deleted_at IS NULL LIMIT 1'
            );
            $userStmt->bindValue(':id', $row->user_id, PDO::PARAM_STR);
            $userStmt->execute();

            $user = $userStmt->fetchObject(User::class);
            if (!($user instanceof User)) {
                return;
            }

            Session::set('user', $user->id);
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }
    }

    private function deleteRememberToken(string $token): void
    {
        $hash = hash('sha256', $token);
        $stmt = $this->db->prepare('DELETE FROM remember_tokens WHERE token_hash = :hash');
        $stmt->bindValue(':hash', $hash, PDO::PARAM_STR);
        $stmt->execute();
    }
}
