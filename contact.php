<?php
// contact.php - Complete Working Version
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only allow POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed. Use POST.'
    ]);
    exit();
}

// PHPMailer autoload
require_once 'PHPMailer/src/Exception.php';
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Get and sanitize input
$name = isset($_POST['name']) ? trim(htmlspecialchars(strip_tags($_POST['name']))) : '';
$email = isset($_POST['email']) ? trim(htmlspecialchars(strip_tags($_POST['email']))) : '';
$phone = isset($_POST['phone']) ? trim(htmlspecialchars(strip_tags($_POST['phone']))) : '';
$message = isset($_POST['message']) ? trim(htmlspecialchars(strip_tags($_POST['message']))) : '';

// Validation
$errors = [];
if (empty($name)) $errors[] = "Name is required";
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required";
if (empty($message)) $errors[] = "Message is required";

if (!empty($errors)) {
    echo json_encode([
        'success' => false,
        'message' => implode(', ', $errors)
    ]);
    exit();
}

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->SMTPDebug = 0; // Disable debug output
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'adward.ayfronex@gmail.com';
    $mail->Password = 'elxh oamh gzao wuua'; // 🔑 CHANGE THIS TO YOUR APP PASSWORD
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->CharSet = 'UTF-8';
    
    // Recipients
    $mail->setFrom('adward.ayfronex@gmail.com', 'AYFRONEX Website');
    $mail->addAddress('adward.ayfronex@gmail.com', 'AYFRONEX Team');
    $mail->addReplyTo($email, $name);
    
    // Content
    $mail->isHTML(true);
    $mail->Subject = "New Contact Message from $name";
    
    $mail->Body = "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
                .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
                .header { background: #f97316; color: white; padding: 20px; border-radius: 8px 8px 0 0; margin: -30px -30px 20px -30px; }
                .header h2 { margin: 0; font-size: 22px; }
                .field { padding: 12px 0; border-bottom: 1px solid #eee; }
                .field-label { font-weight: 600; color: #555; font-size: 13px; text-transform: uppercase; }
                .field-value { font-size: 16px; color: #222; margin-top: 4px; }
                .footer { text-align: center; margin-top: 20px; padding-top: 15px; border-top: 2px solid #f0f0f0; color: #999; font-size: 13px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>📩 New Contact Form Submission</h2>
                </div>
                
                <div class='field'>
                    <div class='field-label'>👤 Name</div>
                    <div class='field-value'>$name</div>
                </div>
                
                <div class='field'>
                    <div class='field-label'>📧 Email</div>
                    <div class='field-value'><a href='mailto:$email'>$email</a></div>
                </div>
                
                <div class='field'>
                    <div class='field-label'>📱 Phone</div>
                    <div class='field-value'>" . ($phone ?: 'Not provided') . "</div>
                </div>
                
                <div class='field'>
                    <div class='field-label'>💬 Message</div>
                    <div class='field-value'>" . nl2br($message) . "</div>
                </div>
                
                <div class='footer'>
                    <p>Sent from AYFRONEX website contact form</p>
                    <p>" . date('F j, Y, g:i a') . "</p>
                </div>
            </div>
        </body>
        </html>
    ";
    
    $mail->AltBody = "New Contact Message\n\nName: $name\nEmail: $email\nPhone: " . ($phone ?: 'Not provided') . "\nMessage: $message";
    
    $mail->send();
    
    echo json_encode([
        'success' => true,
        'message' => 'Thank you! Your message has been sent successfully.'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to send message. Please try again later.',
        'debug' => $mail->ErrorInfo
    ]);
}
?>