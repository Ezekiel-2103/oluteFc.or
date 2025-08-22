<?php
include 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input data
    $firstName = filter_input(INPUT_POST, 'firstName', FILTER_SANITIZE_STRING);
    $lastName = filter_input(INPUT_POST, 'lastName', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $ageGroup = filter_input(INPUT_POST, 'ageGroup', FILTER_SANITIZE_STRING);
    $program = filter_input(INPUT_POST, 'program', FILTER_SANITIZE_STRING);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);
    
    // Validate required fields
    if (empty($firstName) || empty($lastName) || empty($email) || empty($phone) || empty($ageGroup) || empty($program)) {
        $_SESSION['error'] = 'Please fill in all required fields.';
        header('Location: index.php#register');
        exit;
    }
    
    // Insert into database
    try {
        $stmt = $pdo->prepare("INSERT INTO registrations (first_name, last_name, email, phone, age_group, program, message, created_at) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$firstName, $lastName, $email, $phone, $ageGroup, $program, $message]);
        
        // Send email notification
        $to = SITE_EMAIL;
        $subject = 'New Registration: ' . $firstName . ' ' . $lastName;
        $body = "A new registration has been submitted:\n\n" .
                "Name: $firstName $lastName\n" .
                "Email: $email\n" .
                "Phone: $phone\n" .
                "Age Group: $ageGroup\n" .
                "Program: $program\n" .
                "Message: $message";
        $headers = 'From: ' . SITE_EMAIL;
        
        mail($to, $subject, $body, $headers);
        
        $_SESSION['success'] = 'Thank you for your registration! We will contact you soon.';
        header('Location: index.php#register');
        exit;
    } catch(PDOException $e) {
        $_SESSION['error'] = 'There was an error processing your registration. Please try again.';
        header('Location: index.php#register');
        exit;
    }
} else {
    header('Location: index.php');
    exit;
}
?>