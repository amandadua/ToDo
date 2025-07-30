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
    <title>ToDo | Dashboard</title>
    <link rel="stylesheet" href="../Templates/Assets/CSS/Dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="shortcut icon" href="/Images/logo_gerenciador_tarefas_menos_3d.webp" type="image/x-icon">
</head>
<body>
    <!-- <style>
        body {
            background-color: red;
        }
    </style> -->
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <!-- Logo -->
            <div class="logo">
                
                <div class="logo-icon">
                    <!-- <i class= img src="/ToDo/Templates/Assets/Images/logo_gerenciador_tarefas_menos_3d-removebg-preview.png" alt=""></i> -->
<img src="../Images/logo_gerenciador_tarefas_menos_3d-removebg-preview.png" alt="">                
            </div>
                <span class="logo-text">ToDo</span>
            </div>

            <!-- Navigation Menu -->
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

            <!-- Projects Section -->
            <div class="projects-section">
                <div class="section-header">
                    <i class="fas fa-folder-plus"></i>
                    <button class="Add-Projeto">Adicionar Projeto</button>
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

            <!-- User Profile -->
            <div class="user-profile">
                <div class="user-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="user-info">
                    <div class="user-name">Nicollas</div>
                    <div class="user-email">nicollasrio227@gmail.com</div>
                </div>
                <div class="user-menu">
                    <a href="userpage.php" class="user-menu">
                    <i class="fas fa-ellipsis-vertical"></i>
                    </a>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="header">
                <div class="breadcrumb">
                    <span>Projeto 1</span>
                    <i class="fas fa-chevron-right"></i>
                    <span>Dashboard</span>
                </div>
            </header>

            <!-- Welcome Section -->
            <section class="welcome-section">
                <h1 class="welcome-title">Olá, Nicollas!</h1>
            </section>

            <!-- Dashboard Cards -->
            <section class="dashboard-content">
                <!-- Top Row Cards -->
                <div class="cards-row top-row">
                    <!-- Recent Tasks Card -->
                    <div class="card large-card">
                        <div class="card-header">
                            <h3>Tarefas Recentes</h3>
                        </div>
                        <div class="card-content">
                            <div class="task-item">
                                <i class="fas fa-cog task-icon"></i>
                                <div class="task-info">
                                    <span class="task-name">Tarefa 1</span>
                                    <span class="task-time">Criado a 10 minutos</span>
                                </div>
                            </div>
                            <div class="task-item">
                                <i class="fas fa-cog task-icon"></i>
                                <div class="task-info">
                                    <span class="task-name">Tarefa 2</span>
                                    <span class="task-time">Criado a 10 minutos</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Notifications Card -->
                    <div class="card large-card">
                        <div class="card-header">
                            <h3>Notificações Recentes</h3>
                        </div>
                        <div class="card-content notification-content">
                            <div class="notification-number">0</div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Row Cards -->
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

                    <div class="card stat-card">
                        <div class="card-header">
                            <h3>Tarefas Prontas</h3>
                        </div>
                        <div class="card-content">
                            <div class="stat-number">0</div>
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
        // Dashboard ToDo - Interatividade
document.addEventListener("DOMContentLoaded", function() {
    
    // Navegação do menu
    const navItems = document.querySelectorAll(".nav-item");
    navItems.forEach(item => {
        item.addEventListener("click", function() {
            // Remove active de todos os itens
            navItems.forEach(nav => nav.classList.remove("active"));
            // Adiciona active ao item clicado
            this.classList.add("active");
        });
    });

    // Interação com projetos
    const projectItems = document.querySelectorAll(".project-item");
    projectItems.forEach(item => {
        item.addEventListener("click", function() {
            const projectName = this.querySelector(".project-name").textContent;
            console.log(`Projeto selecionado: ${projectName}`);
            // Aqui você pode adicionar lógica para trocar de projeto
        });
    });

    // Menu do usuário
    const userMenu = document.querySelector(".user-menu");
    if (userMenu) {
        userMenu.addEventListener("click", function() {
            console.log("Menu do usuário clicado");
            // Aqui você pode adicionar um dropdown menu
        });
    }

    // Animação dos cards ao carregar
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
    item.addEventListener("click", function() {
        const projectName = this.querySelector(".project-name").textContent;
        console.log(`Projeto selecionado: ${projectName}`);
        // Aqui você pode adicionar lógica para trocar de projeto
    });
});

// Função para atualizar o dashboard para o projeto selecionado
function atualizarDashboardProjeto(nomeProjeto) {
    // Atualiza breadcrumb
    const breadcrumb = document.querySelector(".breadcrumb");
    if (breadcrumb) {
        const spans = breadcrumb.querySelectorAll("span");
        if (spans.length > 0) {
            spans[0].textContent = nomeProjeto;
        }
    }
    // Atualiza título de boas-vindas
    const welcomeTitle = document.querySelector(".welcome-title");
    if (welcomeTitle) {
        welcomeTitle.textContent = `Olá, Nicollas!`;
    }
    // Aqui você pode adicionar outras atualizações específicas do projeto,
    // como tarefas, notificações, etc.
}

// Adiciona event listener para todos os projetos (inclusive os novos)
function adicionarEventoProjeto(item) {
    item.addEventListener("click", function() {
        const projectName = this.querySelector(".project-name").textContent;
        atualizarDashboardProjeto(projectName);
    });
}

// Inicializa listeners para projetos existentes
document.querySelectorAll(".project-item").forEach(adicionarEventoProjeto);

// Adicionar novo projeto ao clicar no botão
const addProjectButton = document.querySelector(".Add-Projeto");
if (addProjectButton) {
    addProjectButton.addEventListener("click", function(e) {
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
            adicionarEventoProjeto(newProject); // <-- Aqui está o segredo!
            projectList.appendChild(newProject);
        }
    });
}
        </script>
</body>
</html>

