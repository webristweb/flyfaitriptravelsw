<?php
// Contact Form Submission Handler
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Sanitize and validate input
    $name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : '';
    $email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
    $phone = isset($_POST['phone']) ? htmlspecialchars(trim($_POST['phone'])) : '';
    $destination = isset($_POST['destination']) ? htmlspecialchars(trim($_POST['destination'])) : '';
    $message = isset($_POST['message']) ? htmlspecialchars(trim($_POST['message'])) : '';
    
    // Validation
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required";
    }
    
    if (empty($phone) || !preg_match('/^[0-9]{10}$/', $phone)) {
        $errors[] = "Valid 10-digit phone number is required";
    }
    
    if (empty($destination)) {
        $errors[] = "Please select a service";
    }
    
    if (empty($message)) {
        $errors[] = "Message is required";
    }
    
    // If there are errors, return them
    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'message' => implode(', ', $errors)
        ]);
        exit;
    }
    
    // Save to file (you can also save to database)
    $data = [
        'date' => date('Y-m-d H:i:s'),
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'service' => $destination,
        'message' => $message
    ];
    
    // Create inquiries directory if it doesn't exist
    if (!file_exists('inquiries')) {
        mkdir('inquiries', 0777, true);
    }
    
    // Save to CSV file
    $filename = 'inquiries/contacts_' . date('Y-m') . '.csv';
    $file_exists = file_exists($filename);
    
    $fp = fopen($filename, 'a');
    
    // Add header if file is new
    if (!$file_exists) {
        fputcsv($fp, ['Date', 'Name', 'Email', 'Phone', 'Service', 'Message']);
    }
    
    // Add data
    fputcsv($fp, $data);
    fclose($fp);
    
    // Send email notification (optional - configure your email settings)
    $to = "flyfaitriptravels@gmail.com";
    $subject = "New Inquiry from FlyFaiTrip Website";
    $email_message = "
    New inquiry received from FlyFaiTrip website:
    
    Name: $name
    Email: $email
    Phone: $phone
    Service: $destination
    Message: $message
    
    Date: " . date('Y-m-d H:i:s') . "
    ";
    
    $headers = "From: noreply@flyfaitriptravels.com\r\n";
    $headers .= "Reply-To: $email\r\n";
    
    // Uncomment below line to send email (requires mail server configuration)
    // mail($to, $subject, $email_message, $headers);
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Thank you for your inquiry! We will contact you soon.'
    ]);
    
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?>
