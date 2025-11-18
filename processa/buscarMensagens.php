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

$atendimentoId = isset($_GET['atendimento_id']) ? (int)$_GET['atendimento_id'] : 0;
$usuarioId = $_SESSION['usuario_id'];

if (empty($atendimentoId)) {
    echo json_encode(['sucesso' => false, 'erro' => 'ID do atendimento não fornecido']);
    exit;
}

$mensagemDAO = new MensagemDAO($pdo);

// Verifica permissão
if (!$mensagemDAO->verificarPermissaoChat($atendimentoId, $usuarioId)) {
    echo json_encode(['sucesso' => false, 'erro' => 'Acesso negado']);
    exit;
}

// Busca mensagens
$mensagens = $mensagemDAO->buscarMensagensPorAtendimento($atendimentoId);

// Marca como lidas
$mensagemDAO->marcarComoLidas($atendimentoId, $usuarioId);

// Formata as mensagens para JSON
$mensagensFormatadas = [];
foreach ($mensagens as $msg) {
    $mensagensFormatadas[] = [
        'id' => $msg['id'],
        'mensagem' => htmlspecialchars($msg['mensagem']),
        'remetente_id' => $msg['remetente_id'],
        'remetente_nome' => htmlspecialchars($msg['remetente_nome']),
        'remetente_foto' => $msg['remetente_foto'],
        'data_envio' => $msg['data_envio'],
        'lida' => $msg['lida']
    ];
}

echo json_encode(['sucesso' => true, 'mensagens' => $mensagensFormatadas]);
?>

