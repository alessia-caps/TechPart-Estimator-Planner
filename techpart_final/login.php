<?php
session_start();
include 'db.php'; // Ensure your database connection is included

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if the user exists
    $stmt = $conn->prepare("SELECT id, first_name, last_name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];

            // Redirect to index.php
            header("Location: index.php");
            exit();
        } else {
            // Redirect back to login.html with an error message
            header("Location: login.html?error=" . urlencode("Incorrect password."));
            exit();
        }
    } else {
        // Redirect back to login.html with an error message
        header("Location: login.html?error=" . urlencode("No user found with that email."));
        exit();
    }
}
?>
