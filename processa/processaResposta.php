<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'tecnico') {
    header('Location: ../auth/login.php?erro=acesso_negado');
    exit;
}

require_once '../includes/db.php';
require_once '../classes/AvaliacaoDAO.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $avaliacaoId = $_POST['avaliacao_id'] ?? null;
    $resposta = $_POST['resposta'] ?? null;
    $tecnicoId = $_SESSION['usuario_id'];

    if (empty($avaliacaoId) || empty($resposta)) {
        header('Location: ../painel/gerenciarAvaliacoes.php?erro=campos_vazios');
        exit;
    }

    $avaliacaoDAO = new AvaliacaoDAO($pdo);
    $sucesso = $avaliacaoDAO->responder($avaliacaoId, $tecnicoId, $resposta);

    if ($sucesso) {
        header('Location: ../painel/gerenciarAvaliacoes.php?sucesso=resposta_enviada');
        exit;
    }
}

// Se algo der errado
header('Location: ../painel/gerenciarAvaliacoes.php?erro=resposta');
exit;
?>

