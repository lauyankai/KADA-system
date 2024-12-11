<?php

namespace App\Models;
use App\Core\BaseModel;

class StudentModel extends BaseModel {
    // Fetch all students from the database
     public function getAllStudents() {
        $sql = "SELECT * FROM students";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $students = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Convert registration date to DD-MM-YYYY format
        foreach ($students as &$student) {
            $student['registration_date'] = date('d-m-Y', strtotime($student['registration_date']));
        }

        return $students;
    }

    // Method to create a student record in the database
    public function insert($name, $email, $course) {
        $sql = "INSERT INTO students (name, email, course, registration_date) 
            VALUES (:name, :email, :course, NOW())"; // Use NOW() to insert the current date and time
    
        $stmt = $this->pdo->prepare($sql);
        
        // Bind the parameters
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':course', $course);
        
        // Execute the statement
        return $stmt->execute();
    }

    // Define a create() method to call insert() like this
    public function create($name, $email, $course) {
        return $this->insert($name, $email, $course); // Call the insert method
    }

    public function getStudentById($id) {
        $sql = "SELECT id, name, email, course, registration_date FROM students WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    public function update($id, $name, $email, $course, $registration_date) {
        $sql = "UPDATE students SET name = :name, email = :email, course = :course, registration_date = :registration_date WHERE id = :id";
        
        // Prepare the SQL statement
        $stmt = $this->pdo->prepare($sql);
        
        // Bind the parameters
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':course', $course);
        $stmt->bindParam(':registration_date', $registration_date);
        
        // Execute the update query
        return $stmt->execute();
    }  

    public function delete($id) {
        $sql = "DELETE FROM students WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
           
        // Execute the query and return the result
        return $stmt->execute();
    }
}
