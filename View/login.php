<?php
session_start();
require_once '../Controller/UserController.php';
use Controller\UserController;

$userController = new UserController();

$loginMessage = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if ($userController->login($email, $password)) {

        $_SESSION['user_email'] = $email;
        header('Location: ../View/Dashboard.php');

        exit();
    } else {
        $loginMessage = "Email ou senha incorretos.";
    }
}


?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ToDo | Login</title>
    <link rel="stylesheet" href="../Templates/Assets/CSS/loginCadastro.css">
    <link rel="shortcut icon" href="../Images/WhatsApp_Image_2025-07-22_at_08.35.46-removebg-preview.png" type="image/x-icon">
</head>

<body>
    <main>
        <div class="topo">
            <img src="../Images/WhatsApp_Image_2025-07-22_at_08.35.46-removebg-preview.png" alt="">
            <h2>Faça login em ToDo</h2>
            <p>Bem-vindo de volta! Por favor faça login para continuar</p>
        </div>

        <form method="POST" action="">
            <label for="Email"> Endereço de email
                <input class="caixa" type="email" name="email" id="Email" required>
            </label>
            <label for="Senha"> Senha
                <input class="caixa" type="password" name="password" id="Senha" required>
            </label>
            <a href="Dashboard.php"><input class="botão" type="submit" value="Continuar"></a>
        </form>

        <p style="color: red; text-align: center;"><?php echo $loginMessage; ?></p>

        <div class="base">
            <p> Não tem uma conta? </p>
            <a href="../View/cadastro.php">Cadastre-se</a>
        </div>
    </main>
</body>

</html>


