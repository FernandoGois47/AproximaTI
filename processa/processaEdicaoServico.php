<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'tecnico') {
    header('Location: ../auth/login.php?erro=acesso_negado');
    exit;
}

require_once '../includes/db.php';
require_once '../classes/ServicoDAO.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $preco = $_POST['preco'] ? str_replace(',', '.', $_POST['preco']) : null;

    $dados = [
        'id' => $_POST['id'],
        'tecnico_id' => $_SESSION['usuario_id'],
        'titulo' => $_POST['titulo'] ?? '',
        'descricao' => $_POST['descricao'] ?? null,
        'preco' => $preco
    ];

    if (empty($dados['titulo']) || empty($dados['id'])) {
        header('Location: ../painel/gerenciarServicos.php?erro=campos_vazios');
        exit;
    }

    $servicoDAO = new ServicoDAO($pdo);
    $sucesso = $servicoDAO->atualizar($dados);

    if ($sucesso) {
        header('Location: ../painel/gerenciarServicos.php?sucesso=edicao');
        exit;
    } else {
        header('Location: ../painel/gerenciarServicos.php?erro=edicao');
        exit;
    }

} else {
    header('Location: ../painel/painelTecnico.php');
    exit;
}
?>

