<?php

namespace app\Services;

use app\Core\Database;
use app\Models\User;
use app\Common\Pagination;
use app\Common\Query;
use Exception;
use PDO;

/**
 * Class UserService
 *
 * This class is responsible for handling the user-related operations of the application.
 * It provides methods to get all users, get a user by ID, get users by role, create, update, and delete a user,
 * find a user by keyword, get a user by email or phone, soft delete a user, and get the total number of users.
 *
 * @package app\Services
 */
class UserService
{
    private PDO $db;

    /**
     * UserService constructor.
     *
     * Initializes the database connection.
     */
    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Method getAllUsers
     *
     * Fetches all users with pagination.
     *
     * @param array $pagerCondition The pagination conditions.
     * @return array Returns an array containing the list of users and the pagination details.
     */
    public function getAllUsers(array $pagerCondition) : array
    {
        $limit = $pagerCondition['limit'];
        $page = $pagerCondition['page'] ;
        
        $totalCount = Query::getCount("SELECT * FROM users WHERE deleted_at IS NULL");
        $pagination = Pagination::paginate($limit, $page, $totalCount);
        
        $offset = $pagination['offset'];
        $query = Query::get('users', [], [], $limit, $offset);
        
        $req = $this->db->query($query)->fetchAll();
        $list = [];

        foreach ($req as $item) {
            $list[] = new User($item);
        }

        return [
            'list' => $list,
            'pagination' => $pagination
        ];
    }

    /**
     * Method getUserById
     *
     * Fetches a user by their ID.
     *
     * @param string $id The ID of the user.
     * @return User|null Returns the User object if found, null otherwise.
     */
    public function getUserById(string $id): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id AND deleted_at IS NULL LIMIT 1");
        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        $stmt->execute();
    
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
        return $result ? new User($result) : null;
    }

    /**
     * Method getUserRole
     *
     * Fetches users by their role with pagination.
     *
     * @param string $roleId The ID of the role.
     * @param array $pagerCondition The pagination conditions.
     * @return array Returns an array containing the list of users and the pagination details.
     */
    public function getUserRole(string $roleId, array $pagerCondition = []): array
    {
        $limit = $pagerCondition['limit'] ?? 10;
        $page = $pagerCondition['page'] ?? 1;
        
        $totalCount = Query::getCount("SELECT * FROM users WHERE role_id = $roleId AND deleted_at IS NULL");
        $pagination = Pagination::paginate($limit, $page, $totalCount);
        
        $offset = $pagination['offset'];
        $query = Query::get('users', [], ['role_id' => $roleId], $limit, $offset);
        
        $req = $this->db->query($query)->fetchAll();
        $list = [];

        foreach ($req as $item) {
            $list[] = new User($item);
        }

        return [
            'list' => $list,
            'pagination' => $pagination
        ];
    }

    /**
     * Method createUser
     *
     * Creates a new user.
     *
     * @param array $data The data of the new user.
     * @return bool Returns true if the user is created successfully, false otherwise.
     */
    public function createUser(array $data): bool
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

    /**
     * Method updateUser
     *
     * Updates a user.
     *
     * @param string $id The ID of the user.
     * @param array $data The new data of the user.
     * @return bool Returns true if the user is updated successfully, false otherwise.
     */
    public function updateUser(string $id, array $data): bool
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

    /**
     * Method deleteUser
     *
     * Deletes a user.
     *
     * @param string $id The ID of the user.
     * @return bool Returns true if the user is deleted successfully, false otherwise.
     */
    public function deleteUser(string $id): bool
    {
        $query = "DELETE FROM users WHERE id = $id";
        return $this->db->exec($query);
    }

    /**
     * Method findUserByKeyWord
     *
     * Finds users by a keyword with pagination.
     *
     * @param string $keyword The keyword to search for.
     * @param array $pagerCondition The pagination conditions.
     * @return array Returns an array containing the list of users and the pagination details.
     */
    public function findUserByKeyWord(string $keyword, array $pagerCondition) : array
    {
        $limit = $pagerCondition['limit'];
        $page = $pagerCondition['page'] ;

        $totalCount = Query::getCount("SELECT * FROM users WHERE name LIKE '%$keyword%' AND deleted_at IS NULL"); 
        $pagination = Pagination::paginate($limit, $page, $totalCount);

        $offset = $pagination['offset'];
        $query = Query::get('users', [], ['name' => $keyword], $limit, $offset);
        $req = $this->db->query($query)->fetchAll();

        $list = array_map(function ($item) {
            return new User($item);
        }, $req);

        return [
            'list' => $list,
            'pagination' => $pagination
        ];
    }

    /**
     * Method getUserByEmail
     *
     * Fetches a user by their email.
     *
     * @param string $email The email of the user.
     * @return User|null Returns the User object if found, null otherwise.
     */
    public function getUserByEmail(string $email): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email AND deleted_at IS NULL LIMIT 1");
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
    
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
        return $result ? new User($result) : null;
    }

    /**
     * Method getUserByPhone
     *
     * Fetches a user by their phone number.
     *
     * @param string $phone The phone number of the user.
     * @return User|null Returns the User object if found, null otherwise.
     */
    public function getUserByPhone($phone): ?User
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE phone = :phone AND deleted_at IS NULL LIMIT 1");
        $stmt->bindValue(':phone', $phone, PDO::PARAM_STR);
        $stmt->execute();
    
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
        return $result ? new User($result) : null;
    }


    /**
     * Method softDeleteUser
     *
     * Soft deletes a user by setting their `deleted_at` field to the current date and time.
     *
     * @param int $id The ID of the user.
     * @return bool Returns true if the user is soft deleted successfully, false otherwise.
     * @throws Exception If there is a database error.
     */
    public function softDeleteUser($id): bool
    {
        try {
            $this->db->beginTransaction();
        
            $query = "UPDATE users SET deleted_at = NOW() WHERE id = :id AND deleted_at IS NULL";
            $statement = $this->db->prepare($query);
            $statement->bindValue(':id', $id);
            $result = $statement->execute();
        
            if ($result) {
                $this->db->commit();
            } else {
                $this->db->rollBack();
            }
        
            return $result;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getTotalUserNumber(): int
    {
        $query = "SELECT COUNT(*) FROM users WHERE deleted_at IS NULL";
        $stmt = $this->db->query($query);
        return $stmt->fetchColumn();
    }
}