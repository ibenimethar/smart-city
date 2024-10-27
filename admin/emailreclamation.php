<?php 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; // Adjust path as necessary

// Create an instance of PHPMailer
$mail = new PHPMailer(true);

// Get the reclamation ID from the POST request
if (isset($_POST['reclamation_id'])) {
    $reclamationId = $_POST['reclamation_id'];

    // Create database connection
    $connection = new mysqli('localhost', 'root', '', 'smartcity');

    // Check connection
    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

    // Fetch reclamation details
    $sql = "SELECT * FROM signalements WHERE id = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param('i', $reclamationId);
    $stmt->execute();
    $result = $stmt->get_result();
    $reclamation = $result->fetch_assoc();
    $stmt->close();

    // Send the email if the reclamation exists
    if ($reclamation) {
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'bnimatharinsaf2021wxc@gmail.com';
            $mail->Password   = 'dkdb gwzi revn bxoj'; // Ensure this is secure and not hardcoded in production
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Recipients
            $mail->setFrom('bnimatharinsaf2021wxc@gmail.com', 'Admin');
            $mail->addAddress($reclamation['email']);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Réclamation Acceptée';
            $mail->Body    = "Bonjour " . htmlspecialchars($reclamation['prenom']) . ",<br><br>
                              Votre réclamation a été acceptée. Détails:<br>
                              ID Réclamation: " . htmlspecialchars($reclamation['id']) . "<br>
                              Description: " . htmlspecialchars($reclamation['description']) . "<br>
                              Statut: accepté<br><br>Merci!";

            // Send the email
            if ($mail->send()) {
                // Update the email status in the database
                $updateSql = "UPDATE signalements SET email_sent = 1 WHERE id = ?";
                $updateStmt = $connection->prepare($updateSql);
                $updateStmt->bind_param('i', $reclamationId);
                $updateStmt->execute();
                $updateStmt->close();
                
                // Redirect to affichereclamation.php after sending the email
                header("Location: affichereclamation.php");
                exit(); // Ensure no further code is executed after redirection
            } else {
                echo "Le message n'a pas pu être envoyé. Erreur du Mailer: {$mail->ErrorInfo}";
            }
        } catch (Exception $e) {
            echo "Le message n'a pas pu être envoyé. Erreur du Mailer: {$mail->ErrorInfo}";
        }
    } else {
        echo "Aucune réclamation trouvée.";
    }

    $connection->close();
}
?>
