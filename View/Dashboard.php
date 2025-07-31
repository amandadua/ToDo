<?php
session_start();

if (!isset($_SESSION['user_email'])) {
    header('Location: login.php');
    exit();
}

// Conecta o banco
$conn = new mysqli('localhost', 'root', '', 'todo');
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Pega o email da sessão
$email = $_SESSION['user_email'];

// Busca dos dados do usuário
$sql = "SELECT id, user_fullname, user_email, foto FROM user WHERE user_email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $usuario = $result->fetch_assoc();
    $usuario_id = $usuario['id'];
    $nome = $usuario['user_fullname'];
    $email = $usuario['user_email'];
    $foto = !empty($usuario['foto']) ? $usuario['foto'] : '../Images/user.jpg';
} else {
    header('Location: login.php');
    exit();
}

$tarefas_result = null;
if (isset($_GET['projeto_id'])) {
    $projeto_id = (int) $_GET['projeto_id'];

    $stmt = $conn->prepare("SELECT * FROM task WHERE projeto_id = ?");
    $stmt->bind_param("i", $projeto_id);
    $stmt->execute();
    $tarefas_result = $stmt->get_result();
}

$stmt = $conn->prepare("SELECT id, name FROM project WHERE user_id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$projetos_result = $stmt->get_result();


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nome_projeto'])) {
    $nomeProjeto = trim($_POST['nome_projeto']);
    if (!empty($nomeProjeto)) {
        // cor aleatória, pode ajustar como quiser
        $cores = ['red', 'purple', 'blue', 'green', 'orange'];
        $cor = $cores[array_rand($cores)];

        $stmt = $conn->prepare("INSERT INTO project (user_id, name) VALUES (?, ?)");
        $stmt->bind_param("is", $usuario_id, $nomeProjeto);
        $stmt->execute();

        // Redireciona para atualizar a página e ver o projeto criado
        header("Location: dashboard.php");
        exit();
    }
}

$nomeProjetoAtual = '';
if (isset($projeto_id)) {
    $stmt = $conn->prepare("SELECT name FROM project WHERE id = ?");
    $stmt->bind_param("i", $projeto_id);
    $stmt->execute();
    $resultNomeProjeto = $stmt->get_result();
    if ($resultNomeProjeto->num_rows === 1) {
        $rowProjeto = $resultNomeProjeto->fetch_assoc();
        $nomeProjetoAtual = $rowProjeto['name'];
    }
}

$numTarefas = 0;
if ($tarefas_result) {
    $numTarefas = $tarefas_result->num_rows;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ToDo | Dashboard</title>
    <link rel="stylesheet" href="../Templates/Assets/CSS/Dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="shortcut icon" href="../Images/WhatsApp_Image_2025-07-22_at_08.35.46-removebg-preview.png"
        type="image/x-icon">
</head>

<body>

    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <!-- Logo -->
            <div class="logo">

                <div class="logo-icon">
                    <img src="../Images/logo_gerenciador_tarefas_menos_3d-removebg-preview.png" alt="">
                </div>
                <span class="logo-text">ToDo</span>
            </div>

            <!-- Menu de Navegação -->
            <nav class="nav-menu">
                <div class="nav-item active">
                    <i class="fas fa-chart-pie"></i>

                    <span>Dashboard</span>
                </div>
                <div class="nav-item">
                    <i class="fas fa-list-check"></i>
                    <a href="Gerenciador.php" class="nav-item">
                        <span>Lista de tarefas</span>
                    </a>
                </div>
            </nav>

            <!-- Seção de Projetos -->
            <div class="projects-section">
                <div class="section-header">
                    <i class="fas fa-folder-plus"></i>
                    <button class="Add-Projeto">Adicionar Projeto</button>

                    <form id="formNovoProjeto" action="dashboard.php" method="POST"
                        style="display:none; margin-top:10px;">
                        <input type="text" name="nome_projeto" placeholder="Nome do projeto" required>
                        <button type="submit">Criar Projeto</button>
                        <button type="button" id="btnCancelarProjeto">Cancelar</button>
                    </form>

                </div>
                <div class="project-list">

                    <div id="project-item" class="project-item">
                        <?php while ($projeto = $projetos_result->fetch_assoc()): ?>
                            <div class="project-item">
                                <a href="?projeto_id=<?php echo $projeto['id']; ?>" class="project-link">
                                    <span class="project-name"><?php echo htmlspecialchars($projeto['name']); ?></span>
                                    <div
                                        class="project-indicator <?php echo htmlspecialchars($projeto['cor'] ?? 'blue'); ?>">
                                    </div>
                                </a>
                            </div>
                        <?php endwhile; ?>
                    </div>

                </div>
            </div>

            <!-- Perfil do Usuário -->
            <div class="user-profile">
                <div class="user-avatar">
                    <?php if (!empty($foto) && $foto !== '../Images/user.jpg'): ?>
                        <img src="<?php echo htmlspecialchars($foto); ?>" alt="Avatar"
                            style="width:40px;height:40px;border-radius:50%;object-fit:cover;">
                    <?php else: ?>
                        <i class="fas fa-user"></i>
                    <?php endif; ?>
                </div>
                <div class="user-info">
                    <div class="user-name"><?php echo htmlspecialchars($nome); ?></div>
                    <div class="user-email"><?php echo htmlspecialchars($email); ?></div>
                </div>
                <div class="user-menu">
                    <a href="userpage.php" class="user-menu">
                        <i class="fas fa-ellipsis-vertical"></i>
                    </a>
                </div>
            </div>
        </aside>

        <!-- Conteúdo Principal -->
        <main class="main-content">
            <!-- Header -->
            <header class="header">
                <div class="breadcrumb">
                    <span><?php echo htmlspecialchars($nomeProjetoAtual ?: 'Nenhum projeto'); ?></span>

                    <i class="fas fa-chevron-right"></i>
                    <span>Dashboard</span>
                </div>
            </header>

            <!-- Seção de Boas-Vindas -->
            <section class="welcome-section">
                <h1 class="welcome-title">Olá, <?php echo htmlspecialchars($nome); ?></h1>
            </section>

            <!-- Cards do Dashboard -->
            <section class="dashboard-content">
                <div class="cards-row top-row">

                    <div class="card large-card">
                        <?php if ($tarefas_result && $tarefas_result->num_rows > 0): ?>
                            <?php while ($tarefa = $tarefas_result->fetch_assoc()): ?>
                                <div class="task-item">
                                    <i class="fas fa-cog task-icon"></i>
                                    <div class="task-info">
                                        <span class="task-name"><?php echo htmlspecialchars($tarefa['titulo']); ?></span>
                                        <span class="task-time">
                                            <?php echo date('d/m/Y H:i', strtotime($tarefa['data_criacao'])); ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p>Nenhuma tarefa para este projeto.</p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="card large-card">
                        <div class="card-header">
                            <h3>Notificações Recentes</h3>
                        </div>
                        <div class="card-content notification-content">
                            <div class="notification-number">0</div>
                        </div>
                    </div>
                </div>

                <div class="cards-row bottom-row">
                    <div class="card stat-card">
                        <div class="card-header">
                            <h3>Tarefas em Backlog</h3>
                        </div>
                        <div class="card-content">
                            <div class="stat-number">2</div>
                        </div>
                    </div>

                    <div class="card stat-card">
                        <div class="card-header">
                            <h3>Tarefas em Progresso</h3>
                        </div>
                        <div class="card-content">
                            <div class="stat-number">0</div>
                        </div>
                    </div>

                    <div class="card stat-card"> // Exibe o número de tarefas prontas
                        <div class="card-header">
                            <h3>Tarefas Prontas</h3>
                        </div>
                        <div class="card-content">
                            <div class="card-content">
                                <div class="stat-number"><?php echo $numTarefas; ?></div>
                            </div>

                        </div>
                    </div>

                    <div class="card stat-card">
                        <div class="card-header">
                            <h3>Tarefas Totais</h3>
                        </div>
                        <div class="card-content">
                            <div class="stat-number">2</div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {

            // Navegação do menu
            const navItems = document.querySelectorAll(".nav-item");
            navItems.forEach(item => {
                item.addEventListener("click", function () {
                    navItems.forEach(nav => nav.classList.remove("active"));
                    this.classList.add("active");
                });
            });

            // Interação com projetos
            const projectItems = document.querySelectorAll(".project-item");
            projectItems.forEach(item => {
                item.addEventListener("click", function () {
                    const projectName = this.querySelector(".project-name").textContent;
                    console.log(`Projeto selecionado: ${projectName}`);
                });
            });

            // Menu do usuário
            const userMenu = document.querySelector(".user-menu");
            if (userMenu) {
                userMenu.addEventListener("click", function () {
                    console.log("Menu do usuário clicado");
                });
            }

            const cards = document.querySelectorAll(".card");
            cards.forEach((card, index) => {
                card.style.opacity = "0";
                card.style.transform = "translateY(20px)";

                setTimeout(() => {
                    card.style.transition = "opacity 0.5s ease, transform 0.5s ease";
                    card.style.opacity = "1";
                    card.style.transform = "translateY(0)";
                }, index * 100);
            });

            // Atualização de estatísticas (simulação)
            function updateStats() {
                const statNumbers = document.querySelectorAll(".stat-number");
                statNumbers.forEach(stat => {
                    const currentValue = parseInt(stat.textContent);
                    // Animação de contagem
                    animateNumber(stat, 0, currentValue, 1000);
                });
            }

            // Função para animar números
            function animateNumber(element, start, end, duration) {
                const range = end - start;
                const increment = range / (duration / 16);
                let current = start;

                const timer = setInterval(() => {
                    current += increment;
                    if (current >= end) {
                        current = end;
                        clearInterval(timer);
                    }
                    element.textContent = Math.floor(current);
                }, 16);
            }

            // Inicializar animações
            setTimeout(updateStats, 500);

            // Responsividade - ajustar sidebar em telas pequenas
            function handleResize() {
                const sidebar = document.querySelector(".sidebar");
                const mainContent = document.querySelector(".main-content");

                if (window.innerWidth <= 768) {
                    sidebar.style.transform = "translateX(-100%)";
                    mainContent.style.marginLeft = "0";
                } else {
                    sidebar.style.transform = "translateX(0)";
                    mainContent.style.marginLeft = "280px";
                }
            }

            // Listener para redimensionamento
            window.addEventListener("resize", handleResize);

            // Verificar tamanho inicial
            handleResize();
        });

        // Função para toggle da sidebar em mobile
        function toggleSidebar() {
            const sidebar = document.querySelector(".sidebar");
            const isHidden = sidebar.style.transform === "translateX(-100%)";

            if (isHidden) {
                sidebar.style.transform = "translateX(0)";
            } else {
                sidebar.style.transform = "translateX(-100%)";
            }
        }
        const projectItems = document.querySelectorAll(".project-item");
        projectItems.forEach(item => {
            item.addEventListener("click", function () {
                const projectName = this.querySelector(".project-name").textContent;
                console.log(`Projeto selecionado: ${projectName}`);
            });
        });

        // Função para atualizar o dashboard para o projeto selecionado
        function atualizarDashboardProjeto(nomeProjeto) {
            const breadcrumb = document.querySelector(".breadcrumb");
            if (breadcrumb) {
                const spans = breadcrumb.querySelectorAll("span");
                if (spans.length > 0) {
                    spans[0].textContent = nomeProjeto;
                }
            }
        }

        // Adiciona event listener para todos os projetos
        function adicionarEventoProjeto(item) {
            item.addEventListener("click", function () {
                const projectName = this.querySelector(".project-name").textContent;
                atualizarDashboardProjeto(projectName);
            });
        }

        // Inicializa listeners para projetos existentes
        document.querySelectorAll(".project-item").forEach(adicionarEventoProjeto);




        const addProjectButton = document.querySelector(".Add-Projeto");
        const formNovoProjeto = document.getElementById("formNovoProjeto");
        const btnCancelarProjeto = document.getElementById("btnCancelarProjeto");

        addProjectButton.addEventListener("click", function () {
            formNovoProjeto.style.display = "block";
            addProjectButton.style.display = "none";
        });

        btnCancelarProjeto.addEventListener("click", function () {
            formNovoProjeto.style.display = "none";
            addProjectButton.style.display = "block";
        });
    </script>
</body>

</html>