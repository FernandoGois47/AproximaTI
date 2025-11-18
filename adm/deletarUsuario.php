<?php
require_once 'includes/auth.php';
require_once '../includes/db.php';
require_once '../classes/UsuarioDAO.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: listarUsuarios.php?erro=usuario_nao_encontrado');
    exit;
}

// Não permite deletar o próprio usuário
if ($id == $_SESSION['usuario_id']) {
    header('Location: listarUsuarios.php?erro=nao_pode_deletar_proprio_usuario');
    exit;
}

$usuarioDAO = new UsuarioDAO($pdo);

// Busca o usuário para verificar se é admin
$usuario = $usuarioDAO->buscarPorIdCompleto($id);

if (!$usuario) {
    header('Location: listarUsuarios.php?erro=usuario_nao_encontrado');
    exit;
}

// Não permite deletar outros admins
if ($usuario['tipo'] === 'admin') {
    header('Location: listarUsuarios.php?erro=nao_pode_deletar_admin');
    exit;
}

// Deleta o usuário
$sucesso = $usuarioDAO->deletar($id);

if ($sucesso) {
    header('Location: listarUsuarios.php?sucesso=deletar');
} else {
    header('Location: listarUsuarios.php?erro=deletar');
}
exit;
?>

