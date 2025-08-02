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
    $nome = $usuario['user_fullname'];
    $email = $usuario['user_email'];
    $foto = !empty($usuario['foto']) ? $usuario['foto'] : '../Images/user.jpg';
} else {
    header('Location: login.php');
    exit();
}

$tarefas_result = null;
$projeto_id = $_GET['projeto_id'] ?? null;

if ($projeto_id) {
    $stmt = $conn->prepare("SELECT id, titulo, status FROM task WHERE projeto_id = ?");
    $stmt->bind_param("i", $projeto_id);
    $stmt->execute();
    $tarefas_result = $stmt->get_result();
}

$stmt = $conn->prepare("SELECT id, name FROM project WHERE user_id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$projetos_result = $stmt->get_result();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['nome_projeto'])) {
        $nomeProjeto = trim($_POST['nome_projeto']);
        if (!empty($nomeProjeto)) {
            $stmt = $conn->prepare("INSERT INTO project (user_id, name) VALUES (?, ?)");
            $stmt->bind_param("is", $usuario_id, $nomeProjeto);
            $stmt->execute();
            header("Location: ../View/Gerenciador.php");
            exit();
        }
    }

    if (isset($_POST['nova_tarefa']) && isset($_POST['projeto_id'])) {
        $novaTarefa = trim($_POST['nova_tarefa']);
        $projeto_id = (int) $_POST['projeto_id'];
        if (!empty($novaTarefa)) {
            $stmt = $conn->prepare("INSERT INTO task (titulo, projeto_id, status) VALUES (?, ?, 'Pendente')");
            $stmt->bind_param("si", $novaTarefa, $projeto_id);
            $stmt->execute();
            header("Location: ../View/Gerenciador.php" . ($projeto_id ? '?projeto_id=' . $projeto_id : ''));
            exit();
        }
    }

    if (isset($_POST['remover_tarefa'])) {
        $tarefa_id = (int) $_POST['remover_tarefa'];
        $stmt = $conn->prepare("DELETE FROM task WHERE id = ?");
        $stmt->bind_param("i", $tarefa_id);
        $stmt->execute();
        header("Location: ../View/Gerenciador.php" . ($projeto_id ? '?projeto_id=' . $projeto_id : ''));
        exit();
    }
}

$nomeProjetoAtual = '';
if ($projeto_id) {
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
    <title>ToDo | Lista de Tarefas</title>
    <link rel="stylesheet" href="../Templates/Assets/CSS/Dashboard.css">
    <link rel="stylesheet" href="../Templates/Assets/CSS/Gerenciador.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="shortcut icon" href="../Images/WhatsApp_Image_2025-07-22_at_08.35.46-removebg-preview.png"
        type="image/x-icon">
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
                <a href="Dashboard.php?projeto_id=<?php echo htmlspecialchars($projeto_id ?? ''); ?>" class="nav-item">
                    <i class="fas fa-chart-pie"></i>
                    <span>Dashboard</span>
                </a>
                <a href="?projeto_id=<?php echo htmlspecialchars($projeto_id ?? ''); ?>" class="nav-item active">
                    <i class="fas fa-list-check"></i>
                    <span>Lista de tarefas</span>
                </a>
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
                                    <div
                                        class="project-indicator <?php echo htmlspecialchars($projeto['cor'] ?? 'blue'); ?>">
                                    </div>
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
                    <span>Tarefas</span>
                </div>
            </header>
            <section class="task-list-section">
                <button type="button" class="add-task-button" id="mostrarFormularioTarefa" style="margin-bottom: 16px;">
                    <i class="fas fa-plus"></i> Adicionar Tarefa
                </button>
                <?php if ($projeto_id): ?>
                    <form method="POST" action="Gerenciador.php?projeto_id=<?php echo $projeto_id; ?>" id="formNovaTarefa"
                        style="display: none; margin-bottom: 20px;">
                        <input type="hidden" name="projeto_id" value="<?php echo $projeto_id; ?>">
                        <input type="text" name="nova_tarefa" placeholder="Nome da nova tarefa" required>
                        <button type="submit">Adicionar</button>
                        <button type="button"
                            onclick="document.getElementById('formNovaTarefa').style.display='none'">Cancelar</button>
                    </form>
                <?php endif; ?>
                <?php if ($tarefas_result && $tarefas_result->num_rows > 0): ?>
                    <?php while ($task = $tarefas_result->fetch_assoc()): ?>
                        <?php $isCompleted = ($task['status'] === 'Concluido'); ?>
                        <div class="task-item-row<?php echo $isCompleted ? ' Concluido' : ''; ?>">
                            <div class="task-details">
                                <i class="<?= $isCompleted ? 'fas fa-check-circle' : 'far fa-circle' ?> task-checkbox"
                                    data-task-id="<?php echo $task['id']; ?>"
                                    data-task-status="<?php echo htmlspecialchars($task['status']); ?>"></i>
                                <span class="task-name"><?php echo htmlspecialchars($task['titulo']); ?></span>
                            </div>
                            <button class="remove-task-button" data-task-id="<?php echo $task['id']; ?>">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="task-item-row">
                        <div class="task-details">
                            <span class="task-name">Nenhuma tarefa encontrada.</span>
                        </div>
                    </div>
                <?php endif; ?>
            </section>
        </main>
    </div>
    <script>
        document.getElementById('mostrarFormularioTarefa').addEventListener('click', function () {
            const form = document.getElementById('formNovaTarefa');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        });

        document.querySelector('.task-list-section').addEventListener('click', function (event) {
            const projectId = new URLSearchParams(window.location.search).get('projeto_id');
            const target = event.target.closest('.remove-task-button');
            const toggleTarget = event.target.closest('.task-checkbox');

            if (target) {
                const taskId = target.getAttribute('data-task-id');
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '../Controller/TaskController.php';

                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'delete';
                form.appendChild(actionInput);

                const taskIdInput = document.createElement('input');
                taskIdInput.type = 'hidden';
                taskIdInput.name = 'task_id';
                taskIdInput.value = taskId;
                form.appendChild(taskIdInput);

                const projectIdInput = document.createElement('input');
                projectIdInput.type = 'hidden';
                projectIdInput.name = 'projeto_id';
                projectIdInput.value = projectId;
                form.appendChild(projectIdInput);

                document.body.appendChild(form);
                form.submit();
            } else if (toggleTarget) {
                const taskId = toggleTarget.getAttribute('data-task-id');
                const currentStatus = toggleTarget.getAttribute('data-task-status');
                const newStatus = currentStatus === 'Concluido' ? 'Pendente' : 'Concluido';

                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '../Controller/TaskController.php';

                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'toggle';
                form.appendChild(actionInput);

                const taskIdInput = document.createElement('input');
                taskIdInput.type = 'hidden';
                taskIdInput.name = 'task_id';
                taskIdInput.value = taskId;
                form.appendChild(taskIdInput);

                const statusInput = document.createElement('input');
                statusInput.type = 'hidden';
                statusInput.name = 'status';
                statusInput.value = newStatus;
                form.appendChild(statusInput);

                const projectIdInput = document.createElement('input');
                projectIdInput.type = 'hidden';
                projectIdInput.name = 'projeto_id';
                projectIdInput.value = projectId;
                form.appendChild(projectIdInput);

                document.body.appendChild(form);
                form.submit();
            }
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