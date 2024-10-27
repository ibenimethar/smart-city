<?php
session_start();
include 'db.php';

// Check if the user is an admin
if ($_SESSION['droit'] !== 'admin') {
    header('Location: dashboard.php');
    exit();
}

// Fetch all users
$result = $conn->query("SELECT * FROM users");

// Handle account activation or deactivation
if (isset($_GET['activate'])) {
    $user_id = $_GET['activate'];
    $conn->query("UPDATE users SET etat='active' WHERE id='$user_id'");
} elseif (isset($_GET['deactivate'])) {
    $user_id = $_GET['deactivate'];
    $conn->query("UPDATE users SET etat='inactive' WHERE id='$user_id'");
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des utilisateurs - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">Gestion des utilisateurs</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Email</th>
                <th>État</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($user = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo $user['nom']; ?></td>
                    <td><?php echo $user['email']; ?></td>
                    <td><?php echo $user['etat']; ?></td>
                    <td>
                        <?php if ($user['etat'] === 'inactive'): ?>
                            <a href="admin_dashboard.php?activate=<?php echo $user['id']; ?>" class="btn btn-success btn-sm">Activer</a>
                        <?php else: ?>
                            <a href="admin_dashboard.php?deactivate=<?php echo $user['id']; ?>" class="btn btn-danger btn-sm">Désactiver</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
