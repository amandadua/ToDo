<?php
session_start();

$registerUserMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['user_fullname'], $_POST['email'], $_POST['password'])) {
        $user_fullname = $_POST['user_fullname'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        require_once '../Controller/UserController.php';
          $userController = new \Controller\UserController();

        if ($userController->checkUserByEmail($email)) {
            $registerUserMessage = "Já existe um usuário cadastrado com esse endereço de e-mail.";
        } else {
            if ($userController->createUser($user_fullname, $email, $password)) {
                header('Location: ../View/login.php');
                exit();
            } else {
                $registerUserMessage = 'Erro ao registrar informações. Tente novamente!';
            }
        }
    } else {
        $registerUserMessage = 'Por favor, preencha todos os campos.';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ToDo | Cadastro </title>
    <link rel="stylesheet" href="../Templates/Assets/CSS/loginCadastro.css">
    <link rel="shortcut icon" href="../Images/WhatsApp_Image_2025-07-22_at_08.35.46-removebg-preview.png" type="image/x-icon">
</head>
<body>
    <main>

        <div class="topo">
            <img src="../Images/WhatsApp_Image_2025-07-22_at_08.35.46-removebg-preview.png" alt="">
            <h2>Crie sua conta</h2>
            <p>Bem-vindo! Preencha os detalhes para começar</p>
        </div>

        <form action="" method="POST">

            <label for="NomeDeUsuário">Nome de usuário
                <input class="caixa" type="text" name="user_fullname" required>
            </label>

            <label for="Email">Endereço de email
                <input class="caixa" type="email" name="email" required>
            </label>

            <label for="Senha">Senha
                <input class="caixa" type="password" name="password" required>
            </label>

            <a href="login.php"><input class="botão" type="submit" value="Criar conta"></a>

        </form>

        <?php if ($registerUserMessage): ?>
            <p class="mensagem"><?php echo $registerUserMessage; ?></p>
        <?php endif; ?>

        <div class="base">
            <p>Já tem uma conta?</p>
            <a href="../View/login.php">Faça login</a>
        </div>

    </main>
</body>
</html>
