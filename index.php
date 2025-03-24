<!DOCTYPE html>
<html lang="en">

<head>
    <title>Event Registration</title>
</head>

<body>
    <h2>Event Registration Form</h2>

    <?php
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

        // If no errors, send email
        if (empty($errors)) {
            $to = "organizer@example.com";
            $subject = "New Event Registration";
            $message = "New registration details:\n\n"
                . "Full Name: $fullName\n"
                . "Email: $email\n"
                . "Phone: $phone\n"
                . "Selected Events: " . implode(", ", $events) . "\n";

            $headers = "From: $email";

            if (mail($to, $subject, $message, $headers)) {
                echo "<p style='color: green;'>Registration successful. Confirmation email sent.</p>";
            } else {
                echo "<p style='color: red;'>Error sending email. Try again later.</p>";
            }
        } else {
            foreach ($errors as $error) {
                echo "<p style='color: red;'>$error</p>";
            }
        }
    }
    ?>

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
        <input type="checkbox" name="events[]" value="Workshop" <?php echo isset($_POST['events']) && in_array('Workshop', $_POST['events']) ? 'checked' : ''; ?>> Workshop<br>
        <input type="checkbox" name="events[]" value="Seminar" <?php echo isset($_POST['events']) && in_array('Seminar', $_POST['events']) ? 'checked' : ''; ?>> Seminar<br>
        <input type="checkbox" name="events[]" value="Networking" <?php echo isset($_POST['events']) && in_array('Networking', $_POST['events']) ? 'checked' : ''; ?>> Networking Session<br><br>

        <button type="submit">Register</button>
    </form>
</body>

</html>
