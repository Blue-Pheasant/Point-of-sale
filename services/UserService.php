<?php

namespace app\services;

use app\core\Database;
use app\models\User;
use app\Common\Pagination;
use app\Common\Query;
use PDO;

class UserService
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAllUsers($pagerCondition) : array
    {
        $limit = $pagerCondition['limit'];
        $page = $pagerCondition['page'] ;
        
        $totalCount = Query::getCount("SELECT * FROM users");
        $pagination = Pagination::paginate($limit, $page, $totalCount);
        
        $offset = $pagination['offset'];
        $query = Query::get('users', [], [], $limit, $offset);
        
        $req = $this->db->query($query)->fetchAll();
        $list = [];

        foreach ($req as $item) {
            $list[] = new User($item);
        }

        $result = [
            'list' => $list,
            'pagination' => $pagination
        ];

        return $result;
    }

    public function getUserById($id): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        $stmt->execute();
    
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
        return $result ? new User($result) : null;
    }

    public function getUserRole($roleId, $pagerCondition = []): array
    {
        $limit = $pagerCondition['limit'] ?? 10;
        $page = $pagerCondition['page'] ?? 1;
        
        $totalCount = Query::getCount("SELECT * FROM users WHERE role_id = $roleId");
        $pagination = Pagination::paginate($limit, $page, $totalCount);
        
        $offset = $pagination['offset'];
        $query = Query::get('users', [], ['role_id' => $roleId], $limit, $offset);
        
        $req = $this->db->query($query)->fetchAll();
        $list = [];

        foreach ($req as $item) {
            $list[] = new User($item);
        }

        $result = [
            'list' => $list,
            'pagination' => $pagination
        ];

        return $result;
    }

    public function createUser($data): bool
    {
        $query = "INSERT INTO users (";
        $columns = [];
        $values = [];
        foreach ($data as $key => $value) {
            $columns[] = $key;
            $values[] = "'$value'";
        }
        $query .= implode(", ", $columns);
        $query .= ") VALUES (";
        $query .= implode(", ", $values);
        $query .= ")";

        return $this->db->exec($query);
    }

    public function updateUser($id, $data): bool
    {
        $query = "UPDATE users SET ";
        $set = [];
        foreach ($data as $key => $value) {
            $set[] = "$key = '$value'";
        }
        $query .= implode(", ", $set);
        $query .= " WHERE id = $id";

        return $this->db->exec($query);
    }

    public function deleteUser($id): bool
    {
        $query = "DELETE FROM users WHERE id = $id";
        return $this->db->exec($query);
    }

    public function findUserByKeyWord($keyword, $pagerCondition) : array
    {
        $limit = $pagerCondition['limit'];
        $page = $pagerCondition['page'] ;

        $query = "SELECT * FROM users WHERE name LIKE '%$keyword%'";
        $totalCount = Query::getCount("SELECT * FROM users WHERE name LIKE '%$keyword%'"); 
        $pagination = Pagination::paginate($limit, $page, $totalCount);

        $offset = $pagination['offset'];
        $query = Query::get('users', [], ['name' => $keyword], $limit, $offset);
        $req = $this->db->query($query)->fetchAll();
        
        $list = array_map(function ($item) {
            return new User($item);
        }, $products);

        $result = [
            'list' => $list,
            'pagination' => $pagination
        ];

        return $result;
    }

    public function getUserByEmail($email): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
    
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
        return $result ? new User($result) : null;
    }

    public function getUserByPhone($phone): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE phone = :phone LIMIT 1");
        $stmt->bindValue(':phone', $phone, PDO::PARAM_STR);
        $stmt->execute();
    
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
        return $result ? new User($result) : null;
    }

    public function softDeleteUser($id): bool
    {
        try {
            $this->db->beginTransaction();
        
            $query = "UPDATE users SET deleted_at = NOW() WHERE id = :id";
            $statement = $this->db->prepare($query);
            $statement->bindValue(':id', $id);
            $result = $statement->execute();
        
            if ($result) {
                $this->db->commit();
            } else {
                $this->db->rollBack();
            }
        
            return $result;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}