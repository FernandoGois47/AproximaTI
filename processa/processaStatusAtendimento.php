<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'tecnico') {
    header('Location: ../auth/login.php?erro=acesso_negado');
    exit;
}

require_once '../includes/db.php';
require_once '../classes/AtendimentoDAO.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $atendimentoId = $_POST['id'] ?? null;
    $novoStatus = $_POST['novo_status'] ?? null;
    $tecnicoId = $_SESSION['usuario_id'];
    
    if (empty($atendimentoId) || empty($novoStatus)) {
        header('Location: ../painel/gerenciarAtendimentos.php?erro=campos_vazios');
        exit;
    }

    $atendimentoDAO = new AtendimentoDAO($pdo);
    $sucesso = $atendimentoDAO->atualizarStatus($atendimentoId, $tecnicoId, $novoStatus);

    if ($sucesso) {
        header('Location: ../painel/gerenciarAtendimentos.php?sucesso=status_atualizado');
        exit;
    } else {
        header('Location: ../painel/gerenciarAtendimentos.php?erro=status_falhou');
        exit;
    }
}

header('Location: ../painel/gerenciarAtendimentos.php');
exit;
?>

