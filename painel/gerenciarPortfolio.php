<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'tecnico') {
    header('Location: ../auth/login.php?erro=acesso_negado');
    exit;
}

require_once '../includes/db.php';
require_once '../classes/PortfolioDAO.php';

$portfolioDAO = new PortfolioDAO($pdo);
$portfolioItens = $portfolioDAO->buscarPorTecnicoId($_SESSION['usuario_id']);

include '../includes/header.php';
?>

<main class="container my-5">
    <div class="row">
        <div class="col-md-3">
            <?php 
                $paginaAtiva = 'portfolio';
                include '../includes/menuTecnico.php'; 
            ?>
        </div>

        <div class="col-md-9">
            <h1>Gerenciar Portfólio</h1>
            <hr>

            <div class="card mb-4">
                <div class="card-header">Adicionar Novo Trabalho</div>
                <div class="card-body">
                    <form action="../processa/processaAdicionarPortfolio.php" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="titulo" class="form-label">Título do Trabalho</label>
                            <input type="text" class="form-control" id="titulo" name="titulo" required>
                        </div>
                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição</label>
                            <textarea class="form-control" id="descricao" name="descricao" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="imagem" class="form-label">Imagem do Trabalho</label>
                            <input class="form-control" type="file" id="imagem" name="imagem" accept="image/*" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Adicionar ao Portfólio</button>
                    </form>
                </div>
            </div>

            <h2>Seus Trabalhos</h2>
            <div class="row g-3">
                <?php if (empty($portfolioItens)): ?>
                    <p class="text-muted">Você ainda não adicionou nenhum item ao seu portfólio.</p>
                <?php else: ?>
                    <?php foreach ($portfolioItens as $item): ?>
                        <div class="col-md-4">
                            <div class="card">
                                <img src="../assets/img/portfolio/<?php echo htmlspecialchars($item['imagem_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($item['titulo']); ?>" style="height: 180px; object-fit: cover;">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($item['titulo']); ?></h5>
                                    <a href="../processa/processaDeletarPortfolio.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este item?');">Excluir</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php
include '../includes/footer.php';
?>

