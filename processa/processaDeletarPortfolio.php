<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'tecnico') {
    header('Location: ../auth/login.php?erro=acesso_negado');
    exit;
}

require_once '../includes/db.php';
require_once '../classes/PortfolioDAO.php';

if (isset($_GET['id'])) {
    $portfolioId = $_GET['id'];
    $tecnicoId = $_SESSION['usuario_id'];
    
    $portfolioDAO = new PortfolioDAO($pdo);
    
    // Primeiro, busca o item para pegar o nome do arquivo da imagem.
    $item = $portfolioDAO->buscarPorId($portfolioId, $tecnicoId);
    
    if ($item) {
        // Tenta deletar o arquivo de imagem do servidor.
        $caminhoArquivo = '../assets/img/portfolio/' . $item['imagem_url'];
        if (file_exists($caminhoArquivo)) {
            unlink($caminhoArquivo);
        }
        
        // Agora, deleta o registro do banco de dados.
        $sucesso = $portfolioDAO->deletar($portfolioId, $tecnicoId);

        if ($sucesso) {
            header('Location: ../painel/gerenciarPortfolio.php?sucesso=deletar');
            exit;
        }
    }
}

// Se chegar aqui, algo deu errado.
header('Location: ../painel/gerenciarPortfolio.php?erro=deletar');
exit;
?>

