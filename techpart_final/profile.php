<?php
session_start();
require_once 'db.php';

// Redirect if not logged in.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$error_message = '';
$success_message = '';

// Fetch user data to prefill form fields
$user_id = $_SESSION['user_id'];
$query = "SELECT first_name, last_name, email FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if ($user) {
    $_SESSION['first_name'] = $user['first_name'];
    $_SESSION['last_name'] = $user['last_name'];
    $_SESSION['email'] = $user['email'];
}

// Process form submission.
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Keep existing values if fields are empty
    $first_name = !empty($_POST["first_name"]) ? trim($_POST["first_name"]) : $_SESSION['first_name'];
    $last_name  = !empty($_POST["last_name"]) ? trim($_POST["last_name"]) : $_SESSION['last_name'];
    $email      = !empty($_POST["email"]) ? trim($_POST["email"]) : $_SESSION['email'];

    // Validate email format if it was changed
    if ($email !== $_SESSION['email'] && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } else {
        // Check if a new password was provided.
        if (!empty($_POST["new_password"])) {
            if ($_POST["new_password"] !== $_POST["confirm_password"]) {
                $error_message = "Passwords do not match.";
            } else {
                // Hash the new password.
                $hashedPassword = password_hash($_POST["new_password"], PASSWORD_DEFAULT);
                // Update including password.
                $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, password = ? WHERE id = ?");
                $stmt->bind_param("ssssi", $first_name, $last_name, $email, $hashedPassword, $user_id);
            }
        } else {
            // Update without password.
            $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ? WHERE id = ?");
            $stmt->bind_param("sssi", $first_name, $last_name, $email, $user_id);
        }

        if ($stmt->execute()) {
            // Update session variables only if update is successful
            $_SESSION['first_name'] = $first_name;
            $_SESSION['last_name']  = $last_name;
            $_SESSION['email']      = $email;
            $success_message = "Profile updated successfully!";
        } else {
            $error_message = "Error updating profile.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="indexstyles.css">
    <title>Edit Profile - TechPart</title>
    <style>
        .profile-container {
            max-width: 500px;
            margin: 30px auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0,0,0,0.1);
        }
        .profile-container h2 {
            margin-bottom: 20px;
            text-align: center;
        }
        .profile-container form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .profile-container form input[type="text"],
        .profile-container form input[type="email"],
        .profile-container form input[type="password"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .profile-container form input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #D196B0;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .profile-container .message {
            text-align: center;
            margin-bottom: 15px;
        }
        .profile-container p a {
            color: #65786D;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <img src="logo.png" alt="Web Name">
        </div>
        <nav>
            <a href="index.php">Products</a>
            <a href="PcBuild page/pcbuild.php">PC Builds</a>
            <div class="auth-section">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <span>Welcome, <a href="profile.php"><?php echo htmlspecialchars($_SESSION['first_name'] . " " . $_SESSION['last_name']); ?></a>!</span>
                    <a href="logout.php">Sign Out</a>
                <?php else: ?>
                    <a href="login.html">Sign In</a>
                <?php endif; ?>
            </div>
        </nav>
    </div>

    <div class="profile-container">
        <h2>Edit Your Profile</h2>
        <?php if (!empty($error_message)): ?>
            <p class="message" style="color: red;"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
        <?php if (!empty($success_message)): ?>
            <p class="message" style="color: green;"><?php echo htmlspecialchars($success_message); ?></p>
        <?php endif; ?>
        <form method="post" action="profile.php">
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" 
                   value="<?php echo htmlspecialchars($_SESSION['first_name']); ?>">
            
            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" 
                   value="<?php echo htmlspecialchars($_SESSION['last_name']); ?>">
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" 
                   value="<?php echo htmlspecialchars($_SESSION['email']); ?>">
            
                   <br><br><p>Change Your Password</p> <br>
            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" name="new_password">
            
            <label for="confirm_password">Confirm New Password:</label>
            <input type="password" id="confirm_password" name="confirm_password">
            
            <input type="submit" value="Update Profile">
        </form>
        <p style="text-align: center; margin-top: 15px;"><a href="index.php">Return to Dashboard</a></p>
    </div>
</body>
</html>
