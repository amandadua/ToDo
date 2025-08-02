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

$sql = "SELECT id, user_fullname, user_email, foto FROM user WHERE user_email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $usuario = $result->fetch_assoc();
    $usuario_id = $usuario['id'];
    $_SESSION['user_id'] = $usuario_id;
    $nome = $usuario['user_fullname'];
    $email = $usuario['user_email'];
    $foto = !empty($usuario['foto']) ? $usuario['foto'] : '../Images/user.jpg';
} else {
    header('Location: login.php');
    exit();
}

$projeto_id = $_GET['projeto_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nome_projeto'])) {
    $nomeProjeto = trim($_POST['nome_projeto']);
    if (!empty($nomeProjeto)) {
        $cores = ['red', 'purple', 'blue', 'green', 'orange'];
        $cor = $cores[array_rand($cores)];

        $stmt = $conn->prepare("INSERT INTO project (user_id, name, cor) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $usuario_id, $nomeProjeto, $cor);
        $stmt->execute();

        header("Location: dashboard.php");
        exit();
    }
}

$stmt = $conn->prepare("SELECT id, name, cor FROM project WHERE user_id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$projetos_result = $stmt->get_result();
$hasProjects = ($projetos_result->num_rows > 0);

$tarefasBacklog = 0;
$tarefasProgresso = 0;
$tarefasProntas = 0;
$tarefasTotais = 0;
$tarefas_result = null;

if ($projeto_id) {
    $query_base = "SELECT COUNT(*) FROM task WHERE projeto_id = ?";
    $params = "i";
    $values = [&$projeto_id];

    $stmt = $conn->prepare($query_base);
    if ($stmt) {
        $stmt->bind_param($params, ...$values);
        $stmt->execute();
        $stmt->bind_result($tarefasTotais);
        $stmt->fetch();
        $stmt->close();
    }

    $query_backlog = $query_base . " AND status = 'Pendente'";
    $stmt = $conn->prepare($query_backlog);
    if ($stmt) {
        $stmt->bind_param($params, ...$values);
        $stmt->execute();
        $stmt->bind_result($tarefasBacklog);
        $stmt->fetch();
        $stmt->close();
    }

    $query_prontas = $query_base . " AND status = 'Concluída'";
    $stmt = $conn->prepare($query_prontas);
    if ($stmt) {
        $stmt->bind_param($params, ...$values);
        $stmt->execute();
        $stmt->bind_result($tarefasProntas);
        $stmt->fetch();
        $stmt->close();
    }

    $stmt = $conn->prepare("SELECT * FROM task WHERE projeto_id = ?");
    $stmt->bind_param("i", $projeto_id);
    $stmt->execute();
    $tarefas_result = $stmt->get_result();
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
    <link rel="shortcut icon" href="../Images/WhatsApp_Image_2025-07-22_at_08.35.46-removebg-preview.png" type="image/x-icon">
    <style>
        .placeholder-message {
            text-align: center;
            margin-top: 50px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e2e2e2;
            color: #555;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        .placeholder-message h1 {
            font-size: 24px;
            color: #333;
        }
        .placeholder-message p {
            font-size: 16px;
            line-height: 1.5;
        }
    </style>
</head>

<body>

    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="logo">
                <div class="logo-icon">
                    <img src="../Images/logo_gerenciador_tarefas_menos_3d-removebg-preview.png" alt="">
                </div>
                <span class="logo-text">ToDo</span>
            </div>

            <nav class="nav-menu">
                <div class="nav-item active">
                    <i class="fas fa-chart-pie"></i>
                    <span>Dashboard</span>
                </div>
                <div class="nav-item">
                    <i class="fas fa-list-check"></i>
                    <a href="Gerenciador.php?projeto_id=<?php echo htmlspecialchars($projeto_id ?? ''); ?>" class="nav-item">
                        <span>Lista de tarefas</span>
                    </a>
                </div>
            </nav>

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
                                    <div class="project-indicator <?php echo htmlspecialchars($projeto['cor'] ?? 'blue'); ?>"></div>
                                </a>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>

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

        <main class="main-content">
            <header class="header">
                <div class="breadcrumb">
                    <span><?php echo htmlspecialchars($nomeProjetoAtual ?: 'Nenhum projeto'); ?></span>
                    <i class="fas fa-chevron-right"></i>
                    <span>Dashboard</span>
                </div>
            </header>

            <section class="welcome-section">
                <h1 class="welcome-title">Olá, <?php echo htmlspecialchars($nome); ?></h1>
            </section>

            <?php if (!$hasProjects): ?>
                <div class="placeholder-message">
                    <h1>Nenhum projeto existente.</h1>
                    <p>Use o botão "Adicionar Projeto" na barra lateral para criar seu primeiro projeto e começar a organizar suas tarefas!</p>
                </div>
            <?php elseif (!$projeto_id): ?>
                <div class="placeholder-message">
                    <h1>Selecione um projeto.</h1>
                    <p>Escolha um dos projetos na barra lateral para visualizar as informações do dashboard.</p>
                </div>
            <?php else: ?>
                <section class="dashboard-content">
                    <div class="cards-row top-row">
                        <div class="card large-card">
                            <div class="card-header">
                                <h3>Tarefas do Projeto</h3>
                            </div>
                            <div class="card-content task-list-content">
                                <?php if ($tarefas_result && $tarefas_result->num_rows > 0): ?>
                                    <?php while ($tarefa = $tarefas_result->fetch_assoc()): ?>
                                        <div class="task-item">
                                            <i class="fas fa-circle task-icon status-<?php echo htmlspecialchars($tarefa['status']); ?>"></i>
                                            <div class="task-info">
                                                <span class="task-name"><?php echo htmlspecialchars($tarefa['titulo']); ?></span>
                                                <span class="task-time">
                                                    <?php echo date('d/m/Y H:i', strtotime($tarefa['data_criacao'])); ?>
                                                </span>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <p class="no-tasks-message">Nenhuma tarefa para este projeto.</p>
                                <?php endif; ?>
                            </div>
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
                                <div class="stat-number"><?php echo $tarefasBacklog; ?></div>
                            </div>
                        </div>

                        <div class="card stat-card">
                            <div class="card-header">
                                <h3>Tarefas em Progresso</h3>
                            </div>
                            <div class="card-content">
                                <div class="stat-number"><?php echo $tarefasProgresso; ?></div>
                            </div>
                        </div>

                        <div class="card stat-card"> 
                            <div class="card-header">
                                <h3>Tarefas Prontas</h3>
                            </div>
                            <div class="card-content">
                                <div class="stat-number"><?php echo $tarefasProntas; ?></div>
                            </div>
                        </div>

                        <div class="card stat-card">
                            <div class="card-header">
                                <h3>Tarefas Totais</h3>
                            </div>
                            <div class="card-content">
                                <div class="stat-number"><?php echo $tarefasTotais; ?></div>
                            </div>
                        </div>
                    </div>
                </section>
            <?php endif; ?>
        </main>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
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
            
            setTimeout(() => {
                const statNumbers = document.querySelectorAll(".stat-number");
                statNumbers.forEach(stat => {
                    const currentValue = parseInt(stat.textContent);
                    animateNumber(stat, 0, currentValue, 1000);
                });
            }, 500);

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
        });
    </script>
</body>
</html>