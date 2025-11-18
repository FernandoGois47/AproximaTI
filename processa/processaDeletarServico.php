<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'tecnico') {
    header('Location: ../auth/login.php?erro=acesso_negado');
    exit;
}

require_once '../includes/db.php';
require_once '../classes/ServicoDAO.php';

if (isset($_GET['id'])) {
    $servicoId = $_GET['id'];
    $tecnicoId = $_SESSION['usuario_id']; // Garante que o técnico só pode deletar seus próprios serviços.

    $servicoDAO = new ServicoDAO($pdo);
    $sucesso = $servicoDAO->deletar($servicoId, $tecnicoId);

    if ($sucesso) {
        header('Location: ../painel/gerenciarServicos.php?sucesso=deletar');
        exit;
    }
}

// Se o ID não for fornecido ou se a exclusão falhar.
header('Location: ../painel/gerenciarServicos.php?erro=deletar');
exit;
?>

