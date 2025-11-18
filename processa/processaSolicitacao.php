<?php
session_start();

// Segurança: Verifica se o usuário logado é um cliente.
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'cliente') {
    // Se não for, redireciona para o login com uma mensagem
    header('Location: ../auth/login.php?erro=login_necessario');
    exit;
}

require_once '../includes/db.php';
require_once '../classes/AtendimentoDAO.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $dados = [
        'cliente_id' => $_SESSION['usuario_id'],
        'tecnico_id' => isset($_POST['tecnico_id']) ? (int)$_POST['tecnico_id'] : null,
        'servico_id' => isset($_POST['servico_id']) ? (int)$_POST['servico_id'] : null
    ];

    // Validação básica
    if (empty($dados['tecnico_id']) || empty($dados['servico_id'])) {
        $tecnicoId = $dados['tecnico_id'] ?? 0;
        header('Location: ../perfil.php?id=' . $tecnicoId . '&erro=servico_invalido');
        exit;
    }

    $atendimentoDAO = new AtendimentoDAO($pdo);
    
    // Verifica se já existe uma solicitação pendente ou em andamento para este serviço
    if ($atendimentoDAO->verificarSolicitacaoExistente($dados['cliente_id'], $dados['servico_id'])) {
        header('Location: ../perfil.php?id=' . $dados['tecnico_id'] . '&erro=solicitacao_existente');
        exit;
    }
    
    // Tenta criar a solicitação
    try {
        $sucesso = $atendimentoDAO->solicitar($dados);

        if ($sucesso) {
            header('Location: ../perfil.php?id=' . $dados['tecnico_id'] . '&sucesso=solicitacao_enviada');
            exit;
        } else {
            header('Location: ../perfil.php?id=' . $dados['tecnico_id'] . '&erro=erro_ao_solicitar');
            exit;
        }
    } catch (Exception $e) {
        // Log do erro (em produção, você pode usar um sistema de logs)
        error_log('Erro ao processar solicitação: ' . $e->getMessage());
        header('Location: ../perfil.php?id=' . $dados['tecnico_id'] . '&erro=erro_ao_solicitar');
        exit;
    }
}

// Se não for POST, redireciona para a página inicial
header('Location: ../index.php');
exit;
?>

