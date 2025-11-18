<?php
session_start();

// Segurança: Apenas clientes logados podem avaliar
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'cliente') {
    header('Location: ../auth/login.php?erro=login_necessario');
    exit;
}

require_once '../includes/db.php';
require_once '../classes/AvaliacaoDAO.php';
require_once '../classes/AtendimentoDAO.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $clienteId = $_SESSION['usuario_id'];
    $tecnicoId = $_POST['tecnico_id'] ?? null;
    $atendimentoId = $_POST['atendimento_id'] ?? null;
    $nota = $_POST['nota'] ?? null;
    $comentario = $_POST['comentario'] ?? null;

    // Validação básica
    if (empty($tecnicoId) || empty($atendimentoId) || empty($nota)) {
        $origem = $_POST['origem'] ?? 'perfil';
        if ($origem === 'meusPedidos') {
            header('Location: ../painel/meusPedidos.php?erro=campos_vazios');
        } else {
            header('Location: ../perfil.php?id=' . $tecnicoId . '&erro=campos_vazios');
        }
        exit;
    }

    // Valida nota (deve ser entre 1 e 5)
    $nota = (int)$nota;
    if ($nota < 1 || $nota > 5) {
        $origem = $_POST['origem'] ?? 'perfil';
        if ($origem === 'meusPedidos') {
            header('Location: ../painel/meusPedidos.php?erro=nota_invalida');
        } else {
            header('Location: ../perfil.php?id=' . $tecnicoId . '&erro=nota_invalida');
        }
        exit;
    }

    // Verifica se o atendimento realmente pertence ao cliente e está concluído
    $atendimentoDAO = new AtendimentoDAO($pdo);
    $sql = "SELECT id FROM atendimentos WHERE id = ? AND cliente_id = ? AND tecnico_id = ? AND status = 'concluido'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$atendimentoId, $clienteId, $tecnicoId]);
    if (!$stmt->fetch()) {
        $origem = $_POST['origem'] ?? 'perfil';
        if ($origem === 'meusPedidos') {
            header('Location: ../painel/meusPedidos.php?erro=atendimento_invalido');
        } else {
            header('Location: ../perfil.php?id=' . $tecnicoId . '&erro=atendimento_invalido');
        }
        exit;
    }

    // Verifica se já existe avaliação para este atendimento específico
    $avaliacaoDAO = new AvaliacaoDAO($pdo);
    if ($avaliacaoDAO->verificarSeJaAvaliou($clienteId, $tecnicoId, $atendimentoId)) {
        $origem = $_POST['origem'] ?? 'perfil';
        if ($origem === 'meusPedidos') {
            header('Location: ../painel/meusPedidos.php?erro=ja_avaliou');
        } else {
            header('Location: ../perfil.php?id=' . $tecnicoId . '&erro=ja_avaliou');
        }
        exit;
    }

    // Cria a avaliação
    $dados = [
        'cliente_id' => $clienteId,
        'tecnico_id' => $tecnicoId,
        'atendimento_id' => $atendimentoId,
        'nota' => $nota,
        'comentario' => $comentario
    ];

    $sucesso = $avaliacaoDAO->criar($dados);

    // Redireciona baseado na origem
    $origem = $_POST['origem'] ?? 'perfil';
    
    if ($sucesso) {
        if ($origem === 'meusPedidos') {
            header('Location: ../painel/meusPedidos.php?sucesso=avaliacao_enviada');
        } else {
            header('Location: ../perfil.php?id=' . $tecnicoId . '&sucesso=avaliacao_enviada');
        }
    } else {
        if ($origem === 'meusPedidos') {
            header('Location: ../painel/meusPedidos.php?erro=avaliacao_falhou');
        } else {
            header('Location: ../perfil.php?id=' . $tecnicoId . '&erro=avaliacao_falhou');
        }
    }
    exit;

} else {
    header('Location: ../index.php');
    exit;
}
?>

