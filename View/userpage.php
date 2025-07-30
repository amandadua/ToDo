<?php
session_start();

if (!isset($_SESSION['user_email'])) {
    header('Location: login.php');
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'todo');
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

$email = $_SESSION['user_email'];

// Atualiza a foto se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
    $extensao = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    $nome_arquivo = uniqid() . "." . $extensao;
    $caminho = "uploads/" . $nome_arquivo;

    // Cria a pasta uploads se não existir
    if (!is_dir("uploads")) {
        mkdir("uploads", 0755, true);
    }

    // Move o arquivo
    if (move_uploaded_file($_FILES['foto']['tmp_name'], $caminho)) {
        // Atualiza no banco
        $sql = "UPDATE user SET foto = ? WHERE user_email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $caminho, $email);
        $stmt->execute();
        // Opcional: mensagem de sucesso
        $mensagem = "Foto atualizada com sucesso!";
    } else {
        $mensagem = "Erro ao salvar a imagem.";
    }
}

// Busca os dados do usuário
$sql = "SELECT user_fullname, user_email, foto FROM user WHERE user_email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $usuario = $result->fetch_assoc();
    $nome = $usuario['user_fullname'];
    $email = $usuario['user_email'];
    $foto = !empty($usuario['foto']) ? $usuario['foto'] : '../Images/user.jpg';
} else {
    $nome = "Usuário";
    $email = "email@exemplo.com";
    $foto = '../Images/user.jpg';
}

$conn->close();
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
            <button class="botao-voltar">
                <i class="fi fi-rr-arrow-small-left"></i>
                <a href="Dashboard.php"><span>Voltar</span></a>
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
                            <form id="form-foto-perfil" action="" method="POST" enctype="multipart/form-data">
                                <img src="<?php echo htmlspecialchars($foto); ?>" alt="Foto de perfil">
                                <label>Foto de Perfil:</label>
                                <input id="input-foto-perfil" type="file" name="foto" accept="image/*" style="display:none;">
                                <input type="submit" value="Atualizar Perfil" style="display:none;">
                            </form>
                            <?php if (isset($mensagem)) echo "<p>$mensagem</p>"; ?>
                        </div>
                        <button class="link-atualizar-foto" type="button">Atualizar foto de perfil</button>
                    </div>
                </div>

                <div class="campo-perfil">
                    <div class="linha">
                        <label>Nome de usuário</label>
                       <span class="valor" id="nome-usuario"><?php echo htmlspecialchars($nome); ?></span>

                        <button class="link-atualizar">Atualizar nome de usuário</button>
                    </div>
                </div>

                <div class="campo-perfil">
                    <div class="linha">
                        <label>Endereço de email</label>
                        <span class="valor" id="email-usuario"><?php echo htmlspecialchars($email); ?></span>

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

<script>
// filepath: c:\xampp\htdocs\ToDo\View\userpage.php
document.addEventListener("DOMContentLoaded", function() {
    const btnAtualizarFoto = document.querySelector(".link-atualizar-foto");
    const inputFoto = document.getElementById("input-foto-perfil");
    const formFoto = document.getElementById("form-foto-perfil");

    btnAtualizarFoto.addEventListener("click", function() {
        inputFoto.click();
    });

    inputFoto.addEventListener("change", function() {
        if (inputFoto.files.length > 0) {
            formFoto.submit();
        }
    });
});
</script>

