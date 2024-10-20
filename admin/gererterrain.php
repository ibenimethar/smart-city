<?php
include_once 'headerAdmin.php';
include_once '../admin/ConnectionSingleton.php'; // Include your database connection

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php'; // Adjust path as necessary

// Handle deletion of a reservation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_id'])) {
    $reservationId = $_POST['delete_id'];

    $sql = "DELETE FROM reservations WHERE id = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $reservationId);

    if ($stmt->execute()) {
        $message = "Reservation deleted successfully.";
    } else {
        $error = "Error deleting reservation: " . $connection->error;
    }

    $stmt->close();
}

// Handle acceptance of a reservation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['accept_id'])) {
    $reservationId = $_POST['accept_id'];

    // Fetch reservation details
    $sql = "SELECT * FROM reservations WHERE id = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $reservationId);
    $stmt->execute();
    $result = $stmt->get_result();
    $reservation = $result->fetch_assoc();
    $stmt->close();

    // Update reservation status
    $updateSql = "UPDATE reservations SET status = 'terrain réservé' WHERE id = ?";
    $updateStmt = $connection->prepare($updateSql);
    $updateStmt->bind_param("i", $reservationId);

    if ($updateStmt->execute()) {
        // Send confirmation email
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; // Set the SMTP server to send through
            $mail->SMTPAuth   = true; // Enable SMTP authentication
            $mail->Username   = 'bnimatharinsaf2021wxc@gmail.com'; // SMTP username
            $mail->Password   = 'dkdb gwzi revn bxoj'; // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption
            $mail->Port       = 587; // TCP port to connect to

            // Recipients
            $mail->setFrom('bnimatharinsaf2021wxc@gmail.com', 'Admin');
            $mail->addAddress($reservation['emailReservant']); // User email from reservation

            // Content
            $mail->isHTML(true); // Set email format to HTML
            $mail->Subject = 'Reservation Confirmation';
            $mail->Body    = "Hello " . htmlspecialchars($reservation['nomReservant']) . ",<br><br>Your reservation has been accepted. Details:<br>
                                Terrain Name: " . htmlspecialchars($reservation['nomTerrain']) . "<br>
                                Start Date: " . htmlspecialchars($reservation['dateDebut']) . "<br>
                                End Date: " . htmlspecialchars($reservation['dateFin']) . "<br><br>
                                Status: terrain réservé<br><br>Thank you!";

            $mail->send();
            $message = "Réservation acceptée et e-mail de confirmation envoyé.";
        } catch (Exception $e) {
            $error = "Le message n'a pas pu être envoyé. Erreur du Mailer : {$mail->ErrorInfo}";
        }
    } else {
        $error = "Erreur lors de la mise à jour du statut de la réservation :" . $connection->error;
    }

    $updateStmt->close();
}

// Fetch all reservations from the database
$reservations = [];
$sql = "SELECT * FROM reservations ORDER BY dateDebut ASC";
$result = $connection->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        // Check if the reservation period is finished and update the status if necessary
        $today = date('Y-m-d');
        if ($row['dateFin'] < $today && $row['status'] != 'periode terminée') {
            // Update the status to 'periode terminée'
            $updateSql = "UPDATE reservations SET status = 'periode terminée' WHERE id = ?";
            $updateStmt = $connection->prepare($updateSql);
            $updateStmt->bind_param("i", $row['id']);
            $updateStmt->execute();
            $updateStmt->close();
            $row['status'] = 'periode terminée'; // Update local array to reflect the change
        }

        $reservations[] = $row;
    }
} else {
    $error = "Erreur lors de la récupération des réservations : " . $connection->error;
}
?>

<!DOCTYPE html>
<html class="h-full">
<head>
    <title>Toutes les Réservations</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Prevent text wrapping in table headers */
        .no-wrap {
            white-space: nowrap;
        }
    </style>
    <script>
        function confirmDelete() {
            return confirm("Êtes-vous sûr de vouloir supprimer cette réservation ?");
        }
        function confirmAccept() {
            return confirm("Êtes-vous sûr de vouloir accepter cette réservation ?");
        }
    </script>
</head>
<body class="flex flex-col h-full justify-between bg-gray-100">
    <div class="container mx-auto mt-8">
        <h1 class="text-4xl font-bold mb-4 text-blue-500 text-center">Toutes les Réservations</h1>

        <?php if (isset($message)): ?>
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative mb-4">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php elseif (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <table class="min-w-full bg-white border border-gray-300">
            <thead>
                <tr class="bg-gray-200">
                    <th class="py-2 px-4 border-b no-wrap">ID</th>
                    <th class="py-2 px-4 border-b no-wrap">Terrain Name</th>
                    <th class="py-2 px-4 border-b no-wrap">Start Date</th>
                    <th class="py-2 px-4 border-b no-wrap">End Date</th>
                    <th class="py-2 px-4 border-b no-wrap">Reservant Name</th>
                    <th class="py-2 px-4 border-b no-wrap">Phone Number</th>
                    <th class="py-2 px-4 border-b no-wrap">Email</th>
                    <th class="py-2 px-4 border-b no-wrap">Status</th> <!-- New status column -->
                    <th class="py-2 px-4 border-b no-wrap">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($reservations)): ?>
                    <?php foreach ($reservations as $reservation): ?>
                        <tr class="hover:bg-gray-100 ">
                            <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($reservation['id']); ?></td>
                            <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($reservation['nomTerrain']); ?></td>
                            <td class="py-2 px-2 border-b  "><?php echo htmlspecialchars($reservation['dateDebut']); ?></td>
                            <td class="py-2 px-2 border-b "><?php echo htmlspecialchars($reservation['dateFin']); ?></td>
                            <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($reservation['nomReservant']); ?></td>
                            <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($reservation['numeroTel']); ?></td>
                            <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($reservation['emailReservant']); ?></td>
                            <td class="py-2 px-2 border-b no-wrap " ><?php echo htmlspecialchars($reservation['status']); ?></td> <!-- Display the status -->
                            <td class="py-2 px-4 border-b">
                                <div class="flex space-x-2">
                                    <form action="" method="POST" onsubmit="return confirmDelete();">
                                        <input type="hidden" name="delete_id" value="<?php echo htmlspecialchars($reservation['id']); ?>">
                                        <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600">Supprimer</button>
                                    </form>
                                    <form action="" method="POST" onsubmit="return confirmAccept();">
                                        <input type="hidden" name="accept_id" value="<?php echo htmlspecialchars($reservation['id']); ?>">
                                        <button type="submit" class="bg-blue-500 text-white px-3 py-1 rounded-md hover:bg-blue-600">Accepter</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center py-4">No reservations found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<?php
// Include the footer file
include_once 'footerAdmin.php';

// Close the connection
$connection->close();
?>
