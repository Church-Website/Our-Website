<?php
use PHPMailer\PHPMailer\PHPMailer;

#require_once __DIR__ . 'vendor/autoload.php';
require_once 'vendor/autoload.php';

$errors = [];
$error_message = '';

if (!empty($_POST)) {
	#Access the data entered by customer
    $name = $_POST['fullname'];
    $email = $_POST['useremail'];
    $message = $_POST['enquiry'];
	
	#Set to our group email
    $to_email = 'info@nickskitchen.co.nz';

    #Missing info check
    if (empty($name)) {
        $errors[] = 'No name entered';
    }

    if (empty($email)) {
        $errors[] = 'No email entered';
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email';
    }

    if (empty($message)) {
        $errors[] = 'No message entered';
    }
	
	#Check if any errors are triggered
    if (empty($errors)) {
        #Create new instance of PHPMailer
        $mail = new PHPMailer;

        #Server settings
        #Set PHPMailer to use SMTP
        $mail->isSMTP();
        #Set SMTP host name
        $mail->Host = 'smtp.mailtrap.io';
        #Enable SMTP authentication
        $mail->SMTPAuth = true;
        #SMTP username and password
        $mail->Username = '';
        $mail->Password = '';
        #If SMTP requires TLS encryption then set it
        $mail->SMTPSecure = 'tls';
        #Set TCP port to connect to
        $mail->Port = 2525;

        #Sender details
        $mail->setFrom($email, $name);
        $mail->addReplyTo($email, $name);
        #Owner email
        $mail->addAddress($to_email, 'Nick');
        #Set email format to HTML
        $mail->isHTML(true);
        #Email to owner
        $mail->Subject = "Nick's Kitchen enquiry from, $name";
        $mail->Body = "<html><body><p>Hi Nick,</p><p>You have received a new message from $name.</p><p>Email: $email</p><p>Message: $message</p></body></html>";
        $mail->AltBody = "Hi Nick,\n\nYou have received a new message from $name.\n\nEmail: $email\nMessage: $message";

        if ($mail->send()) {
            #Clear PHPMailer object
            $mail->clearAllRecipients();
            #Sender email
            $mail->addAddress($email, $name);
            #Email to sender
            $mail->Subject = "Thank you for contacting Nick's Kitchen!";
            $mail->Body = "<html><body><p>Hi $name,</p><p>Thank you for contacting us. We have received your enquiry and will get back to you as soon as possible.</p><p>Best regards,</p><p>Nick's Kitchen Team</p></body></html>";
            $mail->AltBody = "Hi $name,\n\nThank you for contacting us. We have received your enquiry and will get back to you as soon as possible.\n\nBest regards,\nNick's Kitchen Team";

            if ($mail->send()) {
                echo "Message has been sent successfully.";
            } else {
                echo "Confirmation email could not be sent.";
            }
        } else {
            echo "Mailer Error: " . $mail->ErrorInfo;
        }

        #Return to section of website once form is submitted
        header('location:index.html');
    } else {
        #Display error to user
        $all_errors = join('<br/>', $errors);
        $error_message = "<p style='color: red;'>{$all_errors}</p>";
    }
}
?>