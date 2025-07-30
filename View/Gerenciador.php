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
    <title>ToDo - Lista de Tarefas</title>
    <link rel="stylesheet" href="../Templates/Assets/CSS/Gerenciador.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
                    <span>Adicionar Projeto</span>
                </div>
                <div class="project-list">
                    <div class="project-item">
                        <span class="project-name">Projeto 1</span>
                        <div class="project-indicator red"></div>
                    </div>
                    <div class="project-item">
                        <span class="project-name">Projeto 2</span>
                        <div class="project-indicator purple"></div>
                    </div>
                </div>
            </div>


            <div class="user-profile">
                <div class="user-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="user-info">
                    <div class="user-name">Nicollas</div>
                    <div class="user-email">nicollasrio2270@gmail.com</div>
                </div>
                <div class="user-menu">
                    <i class="fas fa-ellipsis-vertical"></i>
                </div>
            </div>
        </aside>


        <main class="main-content">

        <header class="header">
                <div class="breadcrumb">
                    <span>Projeto 1</span>
                    <i class="fas fa-chevron-right"></i>
                    <span>Lista de tarefas</span>
                </div>
                <button class="add-task-button">
                    <i class="fas fa-plus"></i> Adicionar tarefa
                </button>
            </header>


            <section class="task-list-section">
                <div class="task-item-row">
                    <div class="task-details">
                        <i class="far fa-circle task-checkbox"></i>
                        <span class="task-name">Tarefa 1</span>
                    </div>
                    <button class="remove-task-button">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
                <div class="task-item-row">
                    <div class="task-details">
                        <i class="far fa-circle task-checkbox"></i>
                        <span class="task-name">Tarefa 2</span>
                    </div>
                    <button class="remove-task-button">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>

            </section>
        </main>
    </div>
    <script>
        // Funcionalidades específicas para a página de lista de tarefas
document.addEventListener("DOMContentLoaded", function() {
    
    // Navegação do menu (comum a ambas as páginas)
    const navItems = document.querySelectorAll(".nav-item");
    navItems.forEach(item => {
        item.addEventListener("click", function() {
            navItems.forEach(nav => nav.classList.remove("active"));
            this.classList.add("active");
        });
    });

    // Interação com projetos (comum a ambas as páginas)
    const projectItems = document.querySelectorAll(".project-item");
    projectItems.forEach(item => {
        item.addEventListener("click", function() {
            const projectName = this.querySelector(".project-name").textContent;
            console.log(`Projeto selecionado: ${projectName}`);
        });
    });

    // Menu do usuário (comum a ambas as páginas)
    const userMenu = document.querySelector(".user-menu");
    if (userMenu) {
        userMenu.addEventListener("click", function() {
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
        addTaskButton.addEventListener("click", function() {
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

        checkbox.addEventListener("click", function() {
            toggleTaskCompletion(taskRow);
        });

        removeButton.addEventListener("click", function() {
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
            checkbox.addEventListener("click", function() {
                toggleTaskCompletion(taskRow);
            });
        }

        if (removeButton) {
            removeButton.addEventListener("click", function() {
                removeTask(taskRow);
            });
        }
    });

    // Drag and drop functionality
    let draggedElement = null;

    document.querySelectorAll(".task-item-row").forEach(taskRow => {
        taskRow.draggable = true;
        
        taskRow.addEventListener("dragstart", function(e) {
            draggedElement = this;
            this.classList.add("dragging");
            e.dataTransfer.effectAllowed = "move";
        });

        taskRow.addEventListener("dragend", function() {
            this.classList.remove("dragging");
            draggedElement = null;
        });

        taskRow.addEventListener("dragover", function(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = "move";
            this.classList.add("drag-over");
        });

        taskRow.addEventListener("dragleave", function() {
            this.classList.remove("drag-over");
        });

        taskRow.addEventListener("drop", function(e) {
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
    document.addEventListener("keydown", function(e) {
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
    </script>
    
</body>
</html>

