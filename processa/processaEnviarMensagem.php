<?php
session_start();
header('Content-Type: application/json');

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['sucesso' => false, 'erro' => 'Usuário não autenticado']);
    exit;
}

require_once '../includes/db.php';
require_once '../classes/MensagemDAO.php';
require_once '../classes/AtendimentoDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['sucesso' => false, 'erro' => 'Método não permitido']);
    exit;
}

$atendimentoId = isset($_POST['atendimento_id']) ? (int)$_POST['atendimento_id'] : 0;
$mensagem = isset($_POST['mensagem']) ? trim($_POST['mensagem']) : '';
$usuarioId = $_SESSION['usuario_id'];

// Validações
if (empty($atendimentoId) || empty($mensagem)) {
    echo json_encode(['sucesso' => false, 'erro' => 'Dados incompletos']);
    exit;
}

$mensagemDAO = new MensagemDAO($pdo);
$atendimentoDAO = new AtendimentoDAO($pdo);

// Verifica permissão para enviar mensagem neste atendimento
if (!$mensagemDAO->verificarPermissaoChat($atendimentoId, $usuarioId)) {
    echo json_encode(['sucesso' => false, 'erro' => 'Você não tem permissão para enviar mensagens neste atendimento']);
    exit;
}

// Envia a mensagem
$sucesso = $mensagemDAO->enviarMensagem($atendimentoId, $usuarioId, $mensagem);

if ($sucesso) {
    echo json_encode(['sucesso' => true]);
} else {
    echo json_encode(['sucesso' => false, 'erro' => 'Erro ao enviar mensagem']);
}
?>

