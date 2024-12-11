# Simple MVC CRUD Application

This is a simple MVC-based PHP CRUD application.

---

## Installation Instructions

1. **Download the Code:**
   - Download the repository and place it inside your Laragon `www` directory as follows:
     ```
     C:\laragon\www\mvc
     ```

2. **Set Up the Database:**
   - Open `phpMyAdmin` in your browser.
   - Create a new database called `pdo_crud`.
   - Create the `users` table manually with the following structure:
     ```sql
     CREATE TABLE users (
         id INT AUTO_INCREMENT PRIMARY KEY,
         name VARCHAR(100) NOT NULL,
         email VARCHAR(100) NOT NULL,
         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
     );
     ```

3. **Run the Application:**
   - Open the **Laragon Command Line**.
   - Navigate to the project directory:
     ```bash
     cd mvc
     ```
   - Start the PHP built-in server:
     ```bash
     php -S localhost:8000 -t public
     ```
   - Access the application in your browser at `http://localhost:8000`.

---

## Tasks

1. **Enhance the Application Design:**
   - Use **Bootstrap** to design and style the interface. Make the application responsive and user-friendly.

2. **Add Authentication:**
   - Implement **login**, **register**, and **logout** functionalities.
   - Ensure user authentication is secure and uses session management.

3. **Assignment Submission:**
   - This is an **individual assignment**.
   - The **deadline** is **next week**, before the next class.
   - Submit the assignment on **eLearning**.

4. **Submission Requirements:**
   - **Code:** Upload the project code to the designated platform.
   - **Report:** Include a report with:
     - Screenshots of the interface.
     - Explanations for each interface feature and functionality.

---

## Notes

- Use the provided `Model`, `View`, and `Controller` structure to extend the functionality.
- Ensure code quality and readability. Follow proper naming conventions and file organization.
- This project serves as a foundational exercise in PHP MVC development. Feel free to explore and expand upon it.

---

