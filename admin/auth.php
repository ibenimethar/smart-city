<?php
// Include the header file
include_once '../user/header.php'; // Adjust the path accordingly

include_once 'ConnectionSingleton.php';

// Start the session
session_start();

// Initialize message variable
$message = '';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve login credentials from POST request
    if (empty($_POST['login']) || empty($_POST['pwd'])) {
        $message = "Veuillez entrer votre nom d'utilisateur et votre mot de passe."; // Message when fields are empty
    } else {
        $login = htmlspecialchars($_POST['login']);
        $pwd = $_POST['pwd']; // Hash the password

        // Prepare the SQL statement
        $req = $connection->prepare("SELECT * FROM users WHERE login = ? AND password = ?");
        if ($req) {
            $req->bind_param("ss", $login, $pwd); // Bind parameters
            $req->execute();
            $result = $req->get_result(); // Get the result set from the prepared statement

            // Check if the user exists
            if ($result->num_rows === 1) {
                $_SESSION['username'] = $login; // Store username in session
                header("Location:success.php"); 
                exit();
            } else {
                $message = "Identifiants incorrects"; // Error message for incorrect credentials
            }
            $req->close(); // Close the prepared statement
        } else {
            $message = "Erreur lors de la préparation de la requête."; // Message for query preparation failure
        }
    }
}

// Close the connection
$connection->close(); 
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>Login</title>
</head>
<body class="flex h-screen bg-gradient-to-r from-green-500 via-green-500 to-purple-500">
<br>
<div class="w-full h-full bg-gradient-to-r from-green-500 via-green-500 to-purple-500 flex flex-col justify-center items-center text-white rounded-2xl">
    <br><br><br>
    <h3 class="text-center mt-4 text-3xl mb-8">Bienvenue Administrateur</h3>

    <?php if (!empty($message)): ?>
        <div style="color: red;"><?php echo $message; ?></div>
    <?php endif; ?>

    <form action="" method="post" class="bg-white p-8 rounded-lg shadow-lg">
        <div class="mb-4">
            <input type="text" name="login" placeholder="Nom d'utilisateur" required
                   class="w-full p-2 border-b-2 border-purple-500 focus:outline-none focus:border-purple-700 text-black">
        </div>
        <div class="mb-6">
            <input type="password" name="pwd" placeholder="Mot de passe" required
                   class="w-full p-2 border-b-2 border-purple-500 focus:outline-none focus:border-purple-700 text-black">
        </div>
        <button type="submit"
                class="bg-purple-500 text-white py-2 px-4 rounded-full hover:bg-purple-700 transition duration-300 ease-in-out">
            Se connecter
        </button>
    </form>
    <br><br><br>
</div>
<br><br><br>
</body>
</html>

<?php
// Include the footer file
include_once '../user/footer.php'; // Adjust the path accordingly
?>