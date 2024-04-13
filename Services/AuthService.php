<?php

namespace app\Services;

use app\Core\Database;
use app\Core\Application;
use app\Core\Session;
use app\Models\User;
use app\Common\Pagination;
use app\Common\Query;
use PDO;

class AuthService
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function login($email, $password): ?User
    {
        try { 
            $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email AND password = :password AND deleted_at IS NULL LIMIT 1");
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->bindValue(':password', $password, PDO::PARAM_STR);
            $stmt->execute();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function register($data): bool
    {
        try {
            $this->db->beginTransaction();
        
            $stmt = $this->db->prepare("INSERT INTO users (email, password, role) VALUES (:email, :password, :role)");
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

    public function logout()
    {
        Session::remove('user');

        if (isset($_COOKIE['member_login'])) {
            setcookie('member_login', '', time() - 3600, '/');
        }

        session_destroy();

        return isset($_COOKIE['member_login']);
    }

    public function loginWithCookie(): void 
    {
        if(!Session::exists('user')) {
            if(isset($_COOKIE["member_login"])) {
                $userId = $_COOKIE["member_login"];

                try {
                    $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id AND deleted_at IS NULL LIMIT 1");
                    $stmt->bindValue(':id', $userId, PDO::PARAM_INT);
                    $stmt->execute();

                    $user = $stmt->fetch(PDO::FETCH_OBJ);
                    $this->db->commit();

                    if ($user) {
                        Application::$app->session->set('user', $user);
                        setcookie("member_login", $userId, time() + 3600 * 24 * 30);
                    } else {
                        throw new \Exception('User does not exist');
                    }
                } catch (\Exception $e) {
                    error_log($e->getMessage());    
                }
            }
        }
    }
}
