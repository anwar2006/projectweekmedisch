<?php include 'include_dbs.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <script src="javascript/script.js"></script> <!-- De enige script tag -->
</head>

<body>
    <!-- Inlogformulier -->
    <div id="login-form">
        <h2>Login</h2>
        <form action="" method="POST">
            Naam: <input type="text" name="naam" required><br>
            Wachtwoord: <input type="password" name="wachtwoord" required><br>
            <input type="submit" name="login" value="Login">
        </form>
        <p>Nog geen account? <a href="#" onclick="toggleForm('register'); return false;">Maak een account aan.</a></p>
    </div>

    <!-- Registratieformulier -->
    <div id="register-form" style="display: none;">
        <h2>Registreren</h2>
        <form action="" method="POST">
            Naam: <input type="text" name="naam" required><br>
            Adres: <input type="text" name="adres" required><br>
            Telefoonnummer: <input type="tel" name="telefoonnummer" required><br>
            Email: <input type="email" name="email" required><br>
            Wachtwoord: <input type="password" name="wachtwoord" required><br>
            <input type="submit" name="register" value="Registreren">
        </form>
        <p>Heb je al een account? <a href="#" onclick="toggleForm('login'); return false;">Login.</a></p>
    </div>

</body>

</html>

<?php

// Controleren of er een formulier is ingediend
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Verwerken van de login
    if (isset($_POST['login'])) {
        // Ingevoerde gegevens ophalen
        $naam = $_POST['naam'];
        $wachtwoord = $_POST['wachtwoord'];

        // Gebruiker opzoeken in de database
        $stmt = $conn->prepare("SELECT * FROM gebruikers WHERE naam = ?");
        $stmt->bind_param("s", $naam); // 's' staat voor string
        $stmt->execute();
        $result = $stmt->get_result();
        $gebruiker = $result->fetch_assoc();

        // Controleren of het wachtwoord overeenkomt met het gehashte wachtwoord in de database
        if ($gebruiker && password_verify($wachtwoord, $gebruiker['wachtwoord'])) {
            echo "Login succesvol!";
        } else {
            echo "Ongeldige inloggegevens.";
        }
    }

    // Verwerken van de registratie
    if (isset($_POST['register'])) {
        // Gegevens uit het formulier ophalen
        $naam = $_POST['naam'];
        $adres = $_POST['adres'];
        $telefoonnummer = $_POST['telefoonnummer'];
        $email = $_POST['email'];
        $wachtwoord = password_hash($_POST['wachtwoord'], PASSWORD_BCRYPT); // Wachtwoord veilig hashen

        // Gebruiker toevoegen aan de database
        $stmt = $conn->prepare("INSERT INTO gebruikers (naam, adres, telefoonnummer, email, wachtwoord) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $naam, $adres, $telefoonnummer, $email, $wachtwoord);
        $stmt->execute();
        echo "Registratie succesvol!";
    }
}

?>
