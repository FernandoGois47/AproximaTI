<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'tecnico') {
    header('Location: ../auth/login.php?erro=acesso_negado');
    exit;
}

require_once '../includes/db.php';
require_once '../classes/ServicoDAO.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Sanitiza e converte o preço para um formato adequado para o banco.
    $preco = $_POST['preco'] ? str_replace(',', '.', $_POST['preco']) : null;

    $dados = [
        'tecnico_id' => $_SESSION['usuario_id'],
        'titulo' => $_POST['titulo'] ?? '',
        'descricao' => $_POST['descricao'] ?? null,
        'preco' => $preco
    ];

    if (empty($dados['titulo'])) {
        header('Location: ../painel/gerenciarServicos.php?erro=campos_vazios');
        exit;
    }

    $servicoDAO = new ServicoDAO($pdo);
    $sucesso = $servicoDAO->adicionar($dados);

    if ($sucesso) {
        header('Location: ../painel/gerenciarServicos.php?sucesso=adicionar');
        exit;
    } else {
        header('Location: ../painel/gerenciarServicos.php?erro=adicionar');
        exit;
    }

} else {
    header('Location: ../painel/painelTecnico.php');
    exit;
}
?>

