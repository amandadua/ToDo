<?php
session_start();
require_once '../Controller/UserController.php';
use Controller\UserController;

$userController = new UserController();
$mensagem = '';

if (!isset($_SESSION['user_email'])) {
    header('Location: login.php');
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'todo');
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

$email = $_SESSION['user_email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['deletar_conta'])) {
        $userController->deletarConta($email);
        session_destroy();
        header('Location: login.php');
        exit();
    }

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
        $extensao = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $nome_arquivo = uniqid() . "." . $extensao;
        $caminho = "uploads/" . $nome_arquivo;

        if (!is_dir("uploads")) {
            mkdir("uploads", 0755, true);
        }

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $caminho)) {
            $sql = "UPDATE user SET foto = ? WHERE user_email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $caminho, $email);
            $stmt->execute();
            $mensagem = "Foto atualizada com sucesso!";
        } else {
            $mensagem = "Erro ao salvar a imagem.";
        }
    }

    if (isset($_POST['salvar_nome_email'])) {
        if (isset($_POST['novo_nome']) && !empty($_POST['novo_nome'])) {
            $novo_nome = $_POST['novo_nome'];
            $sql = "UPDATE user SET user_fullname = ? WHERE user_email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $novo_nome, $email);
            $stmt->execute();
            $mensagem = "Nome atualizado com sucesso!";
        }
        if (isset($_POST['novo_email']) && !empty($_POST['novo_email'])) {
            $novo_email = $_POST['novo_email'];
            $sql = "UPDATE user SET user_email = ? WHERE user_email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $novo_email, $email);
            $stmt->execute();
            $mensagem = "E-mail atualizado com sucesso!";
            $_SESSION['user_email'] = $novo_email;
            $email = $novo_email;
        }
    }

    if (isset($_POST['nova_senha'])) {
        $novaSenha = $_POST['nova_senha'];
        $userController->alterarSenha($email, $novaSenha);
        $mensagem = "Senha alterada com sucesso!";
    }
}

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
    <link rel='stylesheet'
        href='https://cdn-uicons.flaticon.com/3.0.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
    <link rel='stylesheet'
        href='https://cdn-uicons.flaticon.com/3.0.0/uicons-solid-straight/css/uicons-solid-straight.css'>
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
                                <input id="input-foto-perfil" type="file" name="foto" accept="image/*"
                                    style="display:none;">
                                <input type="submit" value="Atualizar Perfil" style="display:none;">
                            </form>
                        </div>
                        <button class="link-atualizar-foto" type="button">Atualizar foto de perfil</button>
                    </div>
                </div>

                <div class="campo-perfil">
                    <div class="linha">
                        <label>Nome de usuário</label>
                        <span id="nome-usuario-valor"><?php echo htmlspecialchars($nome); ?></span>
                        <form id="form-nome" action="" method="POST"
                            style="display:none; align-items:center; gap:10px;">
                            <input type="text" name="novo_nome" value="<?php echo htmlspecialchars($nome); ?>" required>
                            <button class="link-atualizar" type="submit" name="salvar_nome_email">Salvar</button>
                            <button type="button" class="cancelar-edicao">Cancelar</button>
                        </form>
                        <button class="link-atualizar editar-nome" type="button">Alterar</button>
                    </div>
                </div>

                <div class="campo-perfil">
                    <div class="linha">
                        <label>Endereço de email</label>
                        <span id="email-usuario-valor"><?php echo htmlspecialchars($email); ?></span>
                        <form id="form-email" action="" method="POST"
                            style="display:none; align-items:center; gap:10px;">
                            <input type="email" name="novo_email" value="<?php echo htmlspecialchars($email); ?>"
                                required>
                            <button class="link-atualizar" type="submit" name="salvar_nome_email">Salvar</button>
                            <button type="button" class="cancelar-edicao">Cancelar</button>
                        </form>
                        <button class="link-atualizar editar-email" type="button">Alterar</button>
                    </div>
                </div>
            </section>

            <section class="secao-seguranca" style="display: none;">
                <h1>Segurança</h1>

                <div class="campo-perfil">
                    <div class="linha">
                        <label>Senha</label>
                        <button class="link-atualizar-foto" type="button" id="btn-alterar-senha">Alterar senha</button>
                    </div>

                    <form id="form-alterar-senha" method="POST" style="display:none; margin-top: 10px;">
                        <input type="text" name="nova_senha" placeholder="Nova senha" required>
                        <button class="link-atualizar" type="submit">Salvar</button>
                        <button class="cancelar-edicao" type="button" id="cancelar-alterar-senha">Cancelar</button>
                    </form>
                </div>

                <div class="campo-perfil">
                    <div class="linha">
                        <label>Deletar conta</label>
                        <form method="POST"
                            onsubmit="return confirm('Tem certeza que deseja deletar sua conta? Esta ação não pode ser desfeita!');"
                            style="margin:0;">
                            <button id="deletar" class="link-atualizar editar-nome" type="submit"
                                name="deletar_conta">Deletar conta</button>
                        </form>
                    </div>
                </div>
            </section>
        </main>
    </div>

</body>

</html>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const perfilSection = document.querySelector('.secao-perfil');
        const segurancaSection = document.querySelector('.secao-seguranca');
        const navItems = document.querySelectorAll('.navegacao-barra-lateral .item-navegacao, .navegacao-barra-lateral .item-navegacao-ativo');

        const activeTab = sessionStorage.getItem("activeTab");

        if (activeTab === "seguranca") {
            perfilSection.style.display = 'none';
            segurancaSection.style.display = 'block';
        } else {
            perfilSection.style.display = 'block';
            segurancaSection.style.display = 'none';
        }

        navItems.forEach(item => {
            item.addEventListener('click', function () {
                if (this.innerText.includes('Perfil')) {
                    perfilSection.style.display = 'block';
                    segurancaSection.style.display = 'none';
                    sessionStorage.setItem("activeTab", "perfil");
                } else {
                    perfilSection.style.display = 'none';
                    segurancaSection.style.display = 'block';
                    sessionStorage.setItem("activeTab", "seguranca");
                }

                navItems.forEach(i => i.classList.remove('item-navegacao-ativo'));
                this.classList.add('item-navegacao-ativo');
            });
        });

        const btnAlterarSenha = document.getElementById("btn-alterar-senha");
        const formAlterarSenha = document.getElementById("form-alterar-senha");
        const btnCancelarAlterarSenha = document.getElementById("cancelar-alterar-senha");

        if (btnAlterarSenha && formAlterarSenha && btnCancelarAlterarSenha) {
            btnAlterarSenha.addEventListener("click", function () {
                formAlterarSenha.style.display = "block";
                btnAlterarSenha.style.display = "none";
            });

            btnCancelarAlterarSenha.addEventListener("click", function () {
                formAlterarSenha.style.display = "none";
                btnAlterarSenha.style.display = "inline-block";
            });
        }

        const btnAtualizarFoto = document.querySelector(".link-atualizar-foto");
        const inputFoto = document.getElementById("input-foto-perfil");
        const formFoto = document.getElementById("form-foto-perfil");

        if (btnAtualizarFoto && inputFoto && formFoto) {
            btnAtualizarFoto.addEventListener("click", () => inputFoto.click());
            inputFoto.addEventListener("change", () => {
                if (inputFoto.files.length > 0) formFoto.submit();
            });
        }

        function toggleField(editBtnClass, spanId, formId) {
            const editBtn = document.querySelector(editBtnClass);
            const span = document.getElementById(spanId);
            const form = document.getElementById(formId);

            if (editBtn && span && form) {
                editBtn.addEventListener("click", () => {
                    span.style.display = "none";
                    form.style.display = "flex";
                    editBtn.style.display = "none";
                    form.querySelector("input").focus();
                });

                form.querySelector(".cancelar-edicao").addEventListener("click", () => {
                    span.style.display = "";
                    form.style.display = "none";
                    editBtn.style.display = "";
                });
            }
        }

        toggleField(".editar-nome", "nome-usuario-valor", "form-nome");
        toggleField(".editar-email", "email-usuario-valor", "form-email");
    });
</script>