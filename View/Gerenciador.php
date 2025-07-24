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
        <!-- Sidebar -->
        <aside class="sidebar">
            <!-- Logo -->
            <div class="logo">
                <div class="logo-icon">
                    <i class="fas fa-check-double"></i>
                </div>
                <span class="logo-text">ToDo</span>
            </div>

            <!-- Navigation Menu -->
            <nav class="nav-menu">
                <a href="Dashboard.php" class="nav-item">
                    <i class="fas fa-chart-pie"></i>
                    <span>Dashboard</span>
                </a>
                <a href="tasks.html" class="nav-item active">
                    <i class="fas fa-list-check"></i>
                    <span>Lista de tarefas</span>
                </a>
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
                    <div class="user-email">nicollasrio2270,
                        
                    @gmail.com</div>
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
                    <span>Lista de tarefas</span>
                </div>
                <button class="add-task-button">
                    <i class="fas fa-plus"></i> Adicionar tarefa
                </button>
            </header>

            <!-- Task List Section -->
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
                <!-- Mais tarefas podem ser adicionadas aqui -->
            </section>
        </main>
    </div>
    <script src="script.js">
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


    </script>
    
</body>
</html>

