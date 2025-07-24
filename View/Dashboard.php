<?php
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
                    <i class= img src="/ToDo/Templates/Assets/Images/logo_gerenciador_tarefas_menos_3d-removebg-preview.png" alt=""></i>
<!-- <img src="/ToDo/Templates/Assets/Images/logo_gerenciador_tarefas_menos_3d-removebg-preview.png" alt="">                 -->
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
                    <i class="fas fa-ellipsis-vertical"></i>
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
    <script src="script.js"></script>
</body>
</html>

