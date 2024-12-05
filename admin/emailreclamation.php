<?php 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

$mail = new PHPMailer(true);

if (isset($_POST['reclamation_id'])) {
    $reclamationId = $_POST['reclamation_id'];

    $connection = new mysqli('localhost', 'root', '', 'smartcity');

    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

    $sql = "SELECT * FROM signalements WHERE id = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param('i', $reclamationId);
    $stmt->execute();
    $result = $stmt->get_result();
    $reclamation = $result->fetch_assoc();
    $stmt->close();

    if ($reclamation) {
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'bnimatharinsaf2021wxc@gmail.com';
            $mail->Password   = 'dkdb gwzi revn bxoj'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('bnimatharinsaf2021wxc@gmail.com', 'Admin');
            $mail->addAddress($reclamation['email']);

            $mail->isHTML(true);
            $mail->Subject = 'Réclamation Acceptée';
            $mail->Body    = "Bonjour " . htmlspecialchars($reclamation['prenom']) . ",<br><br>
                              Votre réclamation a été acceptée. Détails:<br>
                              ID Réclamation: " . htmlspecialchars($reclamation['id']) . "<br>
                              Description: " . htmlspecialchars($reclamation['description']) . "<br>
                              Statut: accepté<br><br>Merci!";

            if ($mail->send()) {
                $updateSql = "UPDATE signalements SET email_sent = 1 WHERE id = ?";
                $updateStmt = $connection->prepare($updateSql);
                $updateStmt->bind_param('i', $reclamationId);
                $updateStmt->execute();
                $updateStmt->close();
                
                header("Location: affichereclamation.php");
                exit();
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
