<?php

namespace app\services;

use app\core\Database;
use app\core\Application;
use app\models\User;
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
            $this->db->beginTransaction();
        
            $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email AND password = :password LIMIT 1");
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->bindValue(':password', $password, PDO::PARAM_STR);
            $stmt->execute();
        
            $this->db->commit();
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
        session_destroy();
    }

    public function loginWithCookie(): void 
    {
        if(!Application::$app->session::exists('user')) {
            if(isset($_COOKIE["member_login"])) {
                $userId = $_COOKIE["member_login"];

                try {
                    $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
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