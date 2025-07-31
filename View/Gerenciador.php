<?php
session_start();

if (!isset($_SESSION['user_email'])) {
    header('Location: login.php');
    exit();
}

// Conecta ao banco
$conn = new mysqli('localhost', 'root', '', 'todo');
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Pega o email da sessão
$email = $_SESSION['user_email'];

// Busca os dados do usuário (com id e foto)
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

// Buscar projetos do usuário
$stmt = $conn->prepare("SELECT id, name FROM project WHERE user_id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$projetos_result = $stmt->get_result();

// Buscar tarefas do projeto selecionado
$tarefas_result = null;
$projeto_id = null;
if (isset($_GET['projeto_id'])) {
    $projeto_id = (int) $_GET['projeto_id'];
    $stmt = $conn->prepare("SELECT * FROM task WHERE projeto_id = ?");
    $stmt->bind_param("i", $projeto_id);
    $stmt->execute();
    $tarefas_result = $stmt->get_result();
}

// Buscar ID, nome e email do usuário
$stmt = $conn->prepare("SELECT id, user_fullname, user_email FROM user WHERE user_email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $usuario = $result->fetch_assoc();
    $usuario_id = $usuario['id'];
    $nome = $usuario['user_fullname'];
    $email = $usuario['user_email'];
} else {
    // Caso não encontre, redireciona para o login
    header('Location: login.php');
    exit();
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
    <title>ToDo | Lista de Tarefas</title>
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
                <a href="Dashboard.php" class="nav-item">
                    <i class="fas fa-chart-pie"></i>
                    <span>Dashboard</span>
                </a>
                <a href="" class="nav-item active">
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


                    <div class="project-item">
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
                <button class="add-task-button" style="margin-bottom: 16px;">
                    <i class="fas fa-plus"></i> Adicionar Tarefa
                </button>
                <?php if ($tarefas_result && $tarefas_result->num_rows > 0): ?>
                    <?php while ($tarefa = $tarefas_result->fetch_assoc()): ?>
                        <div class="task-item-row<?php echo $tarefa['completed'] ? ' completed' : ''; ?>">
                            <div class="task-details">
                                <i
                                    class="<?php echo $tarefa['completed'] ? 'fas fa-check-circle' : 'far fa-circle'; ?> task-checkbox"></i>
                                <span class="task-name"><?php echo htmlspecialchars($tarefa['name']); ?></span>
                            </div>
                            <button class="remove-task-button" data-task-id="<?php echo $tarefa['id']; ?>">
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
        // Funcionalidades específicas para a página de lista de tarefas
        document.addEventListener("DOMContentLoaded", function () {

            // Navegação do menu (comum a ambas as páginas)
            const navItems = document.querySelectorAll(".nav-item");
            navItems.forEach(item => {
                item.addEventListener("click", function () {
                    navItems.forEach(nav => nav.classList.remove("active"));
                    this.classList.add("active");
                });
            });

            // Interação com projetos (comum a ambas as páginas)
            const projectItems = document.querySelectorAll(".project-item");
            projectItems.forEach(item => {
                item.addEventListener("click", function () {
                    const projectName = this.querySelector(".project-name").textContent;
                    console.log(`Projeto selecionado: ${projectName}`);
                });
            });

            // Menu do usuário (comum a ambas as páginas)
            const userMenu = document.querySelector(".user-menu");
            if (userMenu) {
                userMenu.addEventListener("click", function () {
                    console.log("Menu do usuário clicado");
                });
            }


            // Responsividade - ajustar sidebar em telas pequenas (comum a ambas as páginas)
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

            // Listener para redimensionamento (comum a ambas as páginas)
            window.addEventListener("resize", handleResize);

            // Verificar tamanho inicial (comum a ambas as páginas)
            handleResize();

            // Funcionalidades específicas da lista de tarefas
            // Adicionar nova tarefa
            const addTaskButton = document.querySelector(".add-task-button");
            if (addTaskButton) {
                addTaskButton.addEventListener("click", function () {
                    addNewTask();
                });
            }

            // Função para adicionar nova tarefa
            function addNewTask() {
                const taskName = prompt("Digite o nome da nova tarefa:");
                if (taskName && taskName.trim()) {
                    const taskListSection = document.querySelector(".task-list-section");
                    const newTaskRow = createTaskElement(taskName.trim());
                    taskListSection.appendChild(newTaskRow);

                    // Animar entrada da nova tarefa
                    setTimeout(() => {
                        newTaskRow.style.opacity = "1";
                        newTaskRow.style.transform = "translateX(0)";
                    }, 10);
                }
            }

            // Função para criar elemento de tarefa
            function createTaskElement(taskName) {
                const taskRow = document.createElement("div");
                taskRow.className = "task-item-row";
                taskRow.style.opacity = "0";
                taskRow.style.transform = "translateX(-20px)";
                taskRow.style.transition = "all 0.3s ease";

                taskRow.innerHTML = `
            <div class="task-details">
                <i class="far fa-circle task-checkbox"></i>
                <span class="task-name">${taskName}</span>
            </div>
            <button class="remove-task-button">
                <i class="fas fa-minus"></i>
            </button>
        `;

                // Adicionar event listeners
                const checkbox = taskRow.querySelector(".task-checkbox");
                const removeButton = taskRow.querySelector(".remove-task-button");

                checkbox.addEventListener("click", function () {
                    toggleTaskCompletion(taskRow);
                });

                removeButton.addEventListener("click", function () {
                    removeTask(taskRow);
                });

                return taskRow;
            }

            // Função para alternar conclusão da tarefa
            function toggleTaskCompletion(taskRow) {
                const checkbox = taskRow.querySelector(".task-checkbox");
                const isCompleted = taskRow.classList.contains("completed");

                if (isCompleted) {
                    taskRow.classList.remove("completed");
                    checkbox.classList.remove("fas", "fa-check-circle");
                    checkbox.classList.add("far", "fa-circle");
                } else {
                    taskRow.classList.add("completed");
                    checkbox.classList.remove("far", "fa-circle");
                    checkbox.classList.add("fas", "fa-check-circle");
                }
            }

            // Função para remover tarefa
            function removeTask(taskRow) {
                if (confirm("Tem certeza que deseja remover esta tarefa?")) {
                    taskRow.classList.add("removing");
                    setTimeout(() => {
                        taskRow.remove();
                    }, 300);
                }
            }

            // Adicionar event listeners para tarefas existentes
            document.querySelectorAll(".task-item-row").forEach(taskRow => {
                const checkbox = taskRow.querySelector(".task-checkbox");
                const removeButton = taskRow.querySelector(".remove-task-button");

                if (checkbox) {
                    checkbox.addEventListener("click", function () {
                        toggleTaskCompletion(taskRow);
                    });
                }

                if (removeButton) {
                    removeButton.addEventListener("click", function () {
                        removeTask(taskRow);
                    });
                }
            });

            // Drag and drop functionality
            let draggedElement = null;

            document.querySelectorAll(".task-item-row").forEach(taskRow => {
                taskRow.draggable = true;

                taskRow.addEventListener("dragstart", function (e) {
                    draggedElement = this;
                    this.classList.add("dragging");
                    e.dataTransfer.effectAllowed = "move";
                });

                taskRow.addEventListener("dragend", function () {
                    this.classList.remove("dragging");
                    draggedElement = null;
                });

                taskRow.addEventListener("dragover", function (e) {
                    e.preventDefault();
                    e.dataTransfer.dropEffect = "move";
                    this.classList.add("drag-over");
                });

                taskRow.addEventListener("dragleave", function () {
                    this.classList.remove("drag-over");
                });

                taskRow.addEventListener("drop", function (e) {
                    e.preventDefault();
                    this.classList.remove("drag-over");

                    if (draggedElement && draggedElement !== this) {
                        const taskListSection = document.querySelector(".task-list-section");
                        const allTasks = Array.from(taskListSection.children);
                        const draggedIndex = allTasks.indexOf(draggedElement);
                        const targetIndex = allTasks.indexOf(this);

                        if (draggedIndex < targetIndex) {
                            this.parentNode.insertBefore(draggedElement, this.nextSibling);
                        } else {
                            this.parentNode.insertBefore(draggedElement, this);
                        }
                    }
                });
            });

            // Atalhos de teclado
            document.addEventListener("keydown", function (e) {
                // Ctrl/Cmd + N para nova tarefa
                if ((e.ctrlKey || e.metaKey) && e.key === "n") {
                    e.preventDefault();
                    addNewTask();
                }

                // Escape para cancelar ações
                if (e.key === "Escape") {
                    document.querySelectorAll(".drag-over").forEach(el => {
                        el.classList.remove("drag-over");
                    });
                }
            });
        });

        // Função para toggle da sidebar em mobile (comum a ambas as páginas)
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
                // Aqui você pode adicionar lógica para trocar de projeto
            });
        });

        // Função para atualizar o breadcrumb com o projeto selecionado
        function atualizarBreadcrumbProjeto(nomeProjeto) {
            const breadcrumb = document.querySelector(".breadcrumb");
            if (breadcrumb) {
                const spans = breadcrumb.querySelectorAll("span");
                if (spans.length > 0) {
                    spans[0].textContent = nomeProjeto;
                }
            }
        }

        // Função para adicionar o event listener de seleção de projeto
        function adicionarEventoProjeto(item) {
            item.addEventListener("click", function () {
                const projectName = this.querySelector(".project-name").textContent;
                atualizarBreadcrumbProjeto(projectName);
            });
        }

        // Inicializa listeners para projetos existentes
        document.querySelectorAll(".project-item").forEach(adicionarEventoProjeto);

        // Adicionar novo projeto ao clicar no botão
        const addProjectButton = document.querySelector(".Add-Projeto");
        if (addProjectButton) {
            addProjectButton.addEventListener("click", function (e) {
                e.stopPropagation();
                const projectName = prompt("Digite o nome do novo projeto:");
                if (projectName && projectName.trim()) {
                    const projectList = document.querySelector(".project-list");
                    const newProject = document.createElement("div");
                    newProject.className = "project-item";
                    newProject.innerHTML = `
                <span class="project-name">${projectName.trim()}</span>
                <div class="project-indicator"></div>
            `;
                    // Cor aleatória para o indicador
                    const colors = ["red", "purple", "blue", "green", "orange"];
                    newProject.querySelector(".project-indicator").classList.add(colors[Math.floor(Math.random() * colors.length)]);
                    adicionarEventoProjeto(newProject);
                    projectList.appendChild(newProject);
                }
            });
        }
    </script>

</body>

</html>