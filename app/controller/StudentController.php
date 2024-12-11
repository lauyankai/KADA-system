<?php

namespace App\Controller; 
use App\Controller\BaseController;
use App\Models\StudentModel;  

class StudentController extends BaseController {
    public function index() {
        $studentModel = new StudentModel();
        $students = $studentModel->getAllStudents();
        $this->render('/student/index', ['students' => $students]);
    }

    // Method to render the 'Add Student' form
    public function add() {
        // Render the form view (students/add.php)
        $this->render('/student/add');
    }

    // Method to handle form submission and create a new student
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize and validate form data
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $course = trim($_POST['course']);

            // Create a new StudentModel instance and call the create method
            $studentModel = new StudentModel();
            $studentModel->create($name, $email, $course);

            // Redirect to the students list or success page
            $this->redirect('/student/index');
        } else {
            // If the form isn't submitted as POST, redirect to the add form
            $this->redirect('/student/add');
        }
    }

    public function edit($id) {
        // Create a new instance of StudentModel to interact with the database
        $studentModel = new StudentModel();
        
        // Fetch student details by ID
        $student = $studentModel->getStudentById($id);
        
        // If student data is found, render the edit view with the student data
        if ($student) {
            $this->render('/student/edit', ['student' => $student]);
        } else {
            // If no student data is found, show an error message
            echo "Student not found!";
        }
    }
    
    public function update($id) {
        // Sanitize and get form data
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $course = trim($_POST['course']);
        $registration_date = $_POST['registration_date'];  // Assuming this is a date field
    
        // Create a new instance of StudentModel to interact with the database
        $studentModel = new StudentModel();
        
        // Call the model to update the student record
        $updated = $studentModel->update($id, $name, $email, $course, $registration_date);
        
        if ($updated) {
            // Redirect to the students list page or display success
            header("Location: /student");
            exit;
        } else {
            // Handle the failure case
            echo "Error updating student.";
        }
    }

    public function delete($id) {
        // Create an instance of the StudentModel
        $studentModel = new StudentModel();

        // Call the delete method in the model
        $deleted = $studentModel->delete($id);

        if ($deleted) {
            // Redirect to the students list
            header("Location: /student");
            exit;
        } else {
            echo "Error deleting student.";
        }
    }
}