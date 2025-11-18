<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'tecnico') {
    header('Location: ../auth/login.php?erro=acesso_negado');
    exit;
}

require_once '../includes/db.php';
require_once '../classes/PortfolioDAO.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Verifica se o arquivo de imagem foi enviado sem erros.
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === 0) {
        $titulo = $_POST['titulo'];
        $descricao = $_POST['descricao'];
        $tecnicoId = $_SESSION['usuario_id'];
        
        $imagem = $_FILES['imagem'];
        $uploadDir = '../assets/img/portfolio/';
        
        // Gera um nome de arquivo único para evitar sobreposições.
        $extensao = pathinfo($imagem['name'], PATHINFO_EXTENSION);
        $nomeArquivo = uniqid() . '.' . $extensao;
        $uploadPath = $uploadDir . $nomeArquivo;

        // Tenta mover o arquivo para a pasta de destino.
        if (move_uploaded_file($imagem['tmp_name'], $uploadPath)) {
            $dados = [
                'tecnico_id' => $tecnicoId,
                'titulo' => $titulo,
                'descricao' => $descricao,
                'imagem_url' => $nomeArquivo // Salva apenas o nome do arquivo no banco.
            ];

            $portfolioDAO = new PortfolioDAO($pdo);
            $sucesso = $portfolioDAO->adicionar($dados);

            if ($sucesso) {
                header('Location: ../painel/gerenciarPortfolio.php?sucesso=adicionar');
                exit;
            }
        }
    }
    
    // Se chegar aqui, algo deu errado.
    header('Location: ../painel/gerenciarPortfolio.php?erro=adicionar');
    exit;

} else {
    header('Location: ../painel/painelTecnico.php');
    exit;
}
?>

