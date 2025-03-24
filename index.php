<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Ensure PHPMailer is installed via Composer

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $errors = [];

    // Get form values and sanitize inputs
    $fullName = htmlspecialchars(trim($_POST['fullName']));
    $email = htmlspecialchars(trim($_POST['email']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirmPassword']);
    $events = isset($_POST['events']) ? $_POST['events'] : [];

    // Validate Full Name
    if (empty($fullName)) {
        $errors[] = "Full Name is required.";
    }

    // Validate Email
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "A valid Email is required.";
    }

    // Validate Phone Number
    if (empty($phone) || !preg_match('/^\d{10}$/', $phone)) {
        $errors[] = "Phone Number must be 10 digits long.";
    }

    // Validate Password
    if (empty($password) || strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }

    // Check Password Confirmation
    if ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match.";
    }

    // Validate Event Selection
    if (empty($events)) {
        $errors[] = "Please select at least one event.";
    }

    // If no errors, send email using PHPMailer
    if (empty($errors)) {
        $mail = new PHPMailer(true);

        try {
            // SMTP Configuration
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';           // Use your mail provider (Gmail SMTP example)
            $mail->SMTPAuth = true;
            $mail->Username = getenv('SMTP_USER');
            $mail->Password = getenv('SMTP_PASS'); // Use an App Password if using Gmail
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Email Content
            $mail->setFrom('your-email@gmail.com', 'Event Organizer');
            $mail->addAddress('recipient@example.com'); // Send to your inbox
            $mail->Subject = 'New Event Registration';
            $mail->Body = "New registration details:\n\n"
                . "Full Name: $fullName\n"
                . "Email: $email\n"
                . "Phone: $phone\n"
                . "Selected Events: " . implode(", ", $events) . "\n";

            $mail->send();
            echo "<p style='color: green;'>Registration successful. Confirmation email sent.</p>";
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
    <title>Event Registration</title>
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
