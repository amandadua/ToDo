<?php
session_start();

if (!isset($_SESSION['user_email'])) {
    header('Location: login.php');
    exit();
}
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ToDo | Perfil</title>
    <link rel="stylesheet" href="../Templates/Assets/CSS/global.css">
    <link rel="stylesheet" href="../Templates/Assets/CSS/userpage.css">
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/3.0.0/uicons-solid-straight/css/uicons-solid-straight.css'>
    <link rel="shortcut icon" href="../Images/icone.ico" type="image/x-icon">
</head>
<body>
    <div class="container">
        <header class="header">
            <a href="Dashboard.php"><button class="botao-voltar"></a>
                <i class="fi fi-rr-arrow-small-left"></i>
                <span>Voltar</span>
            </button>
        </header>

        <main class="conteudo-principal">
            <aside class="barra-lateral">
                <div class="cabecalho-barra-lateral">
                    <h2>Conta</h2>
                    <p>Altere as informações da sua conta.</p>
                </div>
                
                <nav class="navegacao-barra-lateral">
                    <div class="item-navegacao-ativo">
                        <i class="fi fi-rr-user"></i>
                        <span>Perfil</span>
                    </div>
                    <div class="item-navegacao">
                        <i class="fi fi-ss-shield-check"></i>
                        <span>Segurança</span>
                    </div>
                </nav>
            </aside>

            <section class="secao-perfil">
                <h1>Detalhes do perfil</h1>
                
                <div class="campo-perfil">
                    <div class="linha">
                        <label>Perfil</label>
                        <div class="foto-perfil">
                            <img src="../Images/user.jpg" alt="Foto de perfil">
                        </div>
                        <button class="link-atualizar">Atualizar foto de perfil</button>
                    </div>
                </div>

                <div class="campo-perfil">
                    <div class="linha">
                        <label>Nome de usuário</label>
                        <span class="valor" id="nome-usuario">Nicollas</span>
                        <button class="link-atualizar">Atualizar nome de usuário</button>
                    </div>
                </div>

                <div class="campo-perfil">
                    <div class="linha">
                        <label>Endereço de email</label>
                        <span class="valor" id="email-usuario">nicollasrio227@gmail.com</span>
                        <button class="botao-opcoes">
                            <i class="fi fi-rr-menu-dots"></i>
                        </button>
                    </div>
                </div>
            </section>
        </main>
    </div>

</body>
</html>

