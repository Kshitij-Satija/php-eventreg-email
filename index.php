<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errors = [];

    $fullName = htmlspecialchars(trim($_POST['fullName']));
    $email = htmlspecialchars(trim($_POST['email']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirmPassword']);
    $events = isset($_POST['events']) ? $_POST['events'] : [];

    if (empty($fullName)) {
        $errors[] = "Full Name is required.";
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "A valid Email is required.";
    }

    if (empty($phone) || !preg_match('/^\d{10}$/', $phone)) {
        $errors[] = "Phone Number must be 10 digits long.";
    }

    if (empty($password) || strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }

    if ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($events)) {
        $errors[] = "Please select at least one event.";
    }

    if (empty($errors)) {
        $mail = new PHPMailer(true);

        try {
            // SMTP Configuration
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';          
            $mail->SMTPAuth = true;
            $mail->Username = getenv('SMTP_USER');    
            $mail->Password = getenv('SMTP_PASS');   
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Email Content
            $mail->setFrom('your-email@gmail.com', 'Event Organizer'); 
            $mail->addAddress($email);
            $mail->Subject = 'Event Registration Confirmation';
            $mail->Body = "Hello $fullName,\n\nThank you for registering!\n\n"
                . "Your details:\n"
                . "Email: $email\n"
                . "Phone: $phone\n"
                . "Selected Events: " . implode(", ", $events) . "\n\n"
                . "We look forward to seeing you!";

            $mail->send();
            echo "<p style='color: green;'>Registration successful. Confirmation email sent to $email.</p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>Error sending email: {$mail->ErrorInfo}</p>";
        }
    } else {
        foreach ($errors as $error) {
            echo "<p style='color: red;'>$error</p>";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Registration</title>
    <link rel="stylesheet" href="/style.css"> 
</head>
<body>
    <h2>Event Registration Form</h2>
    <form method="post" action="">
        <label>Full Name:</label><br>
        <input type="text" name="fullName" value="<?php echo htmlspecialchars($_POST['fullName'] ?? ''); ?>"><br><br>

        <label>Email:</label><br>
        <input type="text" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"><br><br>

        <label>Phone Number:</label><br>
        <input type="text" name="phone" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"><br><br>

        <label>Password:</label><br>
        <input type="password" name="password"><br><br>

        <label>Confirm Password:</label><br>
        <input type="password" name="confirmPassword"><br><br>

        <label>Select Events:</label><br>
        <input type="checkbox" name="events[]" value="Workshop"> Workshop<br>
        <input type="checkbox" name="events[]" value="Seminar"> Seminar<br>
        <input type="checkbox" name="events[]" value="Networking"> Networking Session<br><br>

        <button type="submit">Register</button>
    </form>
</body>
</html>
