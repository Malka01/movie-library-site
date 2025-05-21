<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = [
        "firstName" => trim($_POST["firstName"] ?? ""),
        "lastName" => trim($_POST["lastName"] ?? ""),
        "email" => trim($_POST["email"] ?? ""),
        "telephone" => trim($_POST["telephone"] ?? ""),
        "message" => trim($_POST["message"] ?? "")
    ];

    // Validate required fields
    if (
        empty($data["firstName"]) ||
        empty($data["lastName"]) ||
        empty($data["email"]) ||
        empty($data["message"])
    ) {
        die("Error: Required fields are missing.");
    }

    if (!filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
        die("Error: Invalid email format.");
    }

    // Save to JSON file
    $jsonFile = __DIR__ . '/../data/submissions.json';
    $existing = file_exists($jsonFile) ? json_decode(file_get_contents($jsonFile), true) : [];
    $existing[] = $data;
    file_put_contents($jsonFile, json_encode($existing, JSON_PRETTY_PRINT));

    // Auto-response email to user
    $userSubject = "Thank you for contacting us!";
    $userMessage = "Hi {$data['firstName']},\n\nThank you for your message:\n\"{$data['message']}\"\n\nWe'll get back to you soon.\n\nBest Regards,\nTeam, \neBEYONDS";
    $userHeaders = "From: noreply@yourdomain.com\r\n";

    mail($data["email"], $userSubject, $userMessage, $userHeaders);

    // Admin email
    $adminMessage = "New contact form submission:\n\n";
    $adminMessage .= "Name: {$data['firstName']} {$data['lastName']}\n";
    $adminMessage .= "Email: {$data['email']}\n";
    $adminMessage .= "Telephone: " . (!empty($data['telephone']) ? $data['telephone'] : "N/A") . "\n";
    $adminMessage .= "Message:\n{$data['message']}\n";

    $adminMessage .= "\n--- End of message ---";

    $adminEmails = [
        "dumidu.kodithuwakku@ebeyonds.com",
        "prabhath.senadheera@ebeyonds.com"
    ];
    $adminHeaders = "From: noreply@yourdomain.com\r\n";

    foreach ($adminEmails as $email) {
        mail($email, "New Contact Form Submission", $adminMessage, $adminHeaders);
    }

    echo "Thank you for your submission!";
} else {
    echo "Invalid request.";
}
