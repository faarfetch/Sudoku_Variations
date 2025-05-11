<?php
//pagina di registrazione dellutente 

if (!isset($_SESSION)) {
    session_start();
}
if (isset($_SESSION["autenticato"])) {
    if ($_SESSION["autenticato"] == 1) {
        header("Location: home.php");
        exit();
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script>
        function toggleForm(formType) {
            if (formType === 'login') {
                document.getElementById('loginForm').style.display = 'block';
                document.getElementById('registerForm').style.display = 'none';
            } else {
                document.getElementById('loginForm').style.display = 'none';
                document.getElementById('registerForm').style.display = 'block';
            }
        }
    </script>
</head>
<link rel="stylesheet" href="style/loginStyle.css">


<body>
    <?php include 'header.php'; ?>
    <div id="container">
        <?php
        if (isset($_GET["message"])) {
            echo ("<h1 style='color: red'>" . $_GET["message"] . "</h1>");
        }
        ?>

        <div style="display: flex;">
            <button onclick="toggleForm('login')" style="color: black;">Login</button>
            <button onclick="toggleForm('register')" style="color: black;">Registrazione</button>
        </div>
        <div id="loginForm" style="display: none;">
            <h2>Login</h2>
            <form action="gestori/gestoreLogin.php" method="post">
                <label for="loginUsername">Username</label>
                <input type="text" name="username" id="loginUsername" style="color: black;"><br>
                <label for="loginPassword">Password</label>
                <input type="password" name="password" id="loginPassword" style="color: black;"><br>
                <input type="submit" value="Login" style="color: black;">
            </form>
        </div>

        <div id="registerForm" style="display: none;">
            <h2>Registrazione</h2>
            <form action="gestori/gestoreLogin.php" method="post">
                <label for="registerUsername">Username</label>
                <input type="text" name="username" id="registerUsername" style="color: black;"><br>
                <label for="registerPassword">Password</label>
                <input type="password" name="password" id="registerPassword" style="color: black;"><br>
                <label for="registerPassword2">Conferma Password</label>
                <input type="password" name="password2" id="registerPassword2" style="color: black;"><br>
                <input type="submit" value="Registrati" style="color: black;">
            </form>
        </div>

        <script>
            // Default to showing the login form
            toggleForm('login');
        </script>

    </div>
</body>

</html>