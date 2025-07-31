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

$action = $_POST['action'] ?? null;
$projeto_id = $_POST['projeto_id'] ?? null;

// AQUI: A variável de redirecionamento está corrigida para apontar para a sua pasta 'View'
$redirect = '../View/Gerenciador.php' . ($projeto_id ? '?projeto_id=' . $projeto_id : '');

if (!$action) {
    header("Location: $redirect");
    exit();
}

if ($action === 'create') {
    $titulo = trim($_POST['titulo'] ?? '');
    $usuario_id = $_SESSION['user_id'] ?? null; // Adicione o user_id na sessão para ser seguro
    
    if (!empty($titulo) && $projeto_id && $usuario_id) {
        $stmt = $conn->prepare("INSERT INTO task (titulo, projeto_id, user_id, status) VALUES (?, ?, ?, 'Pendente')");
        $stmt->bind_param("sii", $titulo, $projeto_id, $usuario_id);
        $stmt->execute();
    }
    
    header("Location: $redirect");
    exit();
}

if ($action === 'toggle') {
    $task_id = $_POST['task_id'] ?? null;
    $status = $_POST['status'] ?? 'Pendente';
    
    if ($task_id !== null) {
        $stmt = $conn->prepare("UPDATE task SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $task_id);
        $stmt->execute();
    }
    
    header("Location: $redirect");
    exit();
}

if ($action === 'delete') {
    $task_id = $_POST['task_id'] ?? null;
    
    if ($task_id !== null) {
        $stmt = $conn->prepare("DELETE FROM task WHERE id = ?");
        $stmt->bind_param("i", $task_id);
        $stmt->execute();
    }
    
    header("Location: $redirect");
    exit();
}

header("Location: $redirect");
exit();
?>