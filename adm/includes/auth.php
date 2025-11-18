<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: ../auth/login.php?erro=login_necessario');
    exit;
}

// Verifica se o usuário é um administrador
if ($_SESSION['usuario_tipo'] !== 'admin') {
    header('Location: ../index.php?erro=acesso_negado');
    exit;
}
?>

