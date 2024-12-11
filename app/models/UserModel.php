<?php

namespace App\Models;
use App\Core\BaseModel;

class UserModel extends BaseModel {
    // Fetch all users from the database
    public function getAllUsers() {
        $sql = "SELECT id, username FROM users"; // Adjust fields if necessary
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $users;
    }

    public function getUserById($id) {
        $sql = "SELECT id, username FROM users WHERE id = :id"; // Fetch user details by id
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        
        // Return user data or null if no user is found
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    // Check if a user exists by email
    public function getUserByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // Register a new user
    public function register($username, $email, $password) {
        // Hash the password using bcrypt for security
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Insert the new user into the database
        $sql = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);

        return $stmt->execute();
    }

    // Verify user credentials during login
    public function login($email, $password) {
        // Prepare the SQL query to fetch user data by email
        $sql = "SELECT id, username, email, password FROM users WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        // Fetch the user record from the database
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        // If the user exists and the password matches
        if ($user && password_verify($password, $user['password'])) {
            return $user; // Return user data if credentials are correct
        }

        return false; // Return false if no match
    }
}
