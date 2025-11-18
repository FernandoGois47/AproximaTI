<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'tecnico') {
    header('Location: ../auth/login.php?erro=acesso_negado');
    exit;
}

require_once '../includes/db.php';
require_once '../classes/AvaliacaoDAO.php';

$avaliacaoDAO = new AvaliacaoDAO($pdo);
$avaliacoes = $avaliacaoDAO->buscarPorTecnicoId($_SESSION['usuario_id']);

include '../includes/header.php';
?>

<main class="container my-5">
    <div class="row">
        <div class="col-md-3">
            <?php 
                $paginaAtiva = 'avaliacoes';
                include '../includes/menuTecnico.php'; 
            ?>
        </div>

        <div class="col-md-9">
            <h1>Minhas Avaliações</h1>
            <hr>
            <p>Aqui você pode ver o que os clientes estão dizendo sobre o seu trabalho e responder aos comentários.</p>

            <?php if (empty($avaliacoes)): ?>
                <div class="alert alert-info">Você ainda não recebeu nenhuma avaliação.</div>
            <?php else: ?>
                <?php foreach ($avaliacoes as $avaliacao): ?>
                    <div class="card mb-3">
                        <div class="card-header d-flex justify-content-between">
                            <strong>Cliente: <?php echo htmlspecialchars($avaliacao['cliente_nome']); ?></strong>
                            <div class="text-warning">
                                <?php for($i = 0; $i < $avaliacao['nota']; $i++) { echo '<i class="bi bi-star-fill"></i>'; } ?>
                                <?php for($i = $avaliacao['nota']; $i < 5; $i++) { echo '<i class="bi bi-star"></i>'; } ?>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="fst-italic">"<?php echo htmlspecialchars($avaliacao['comentario']); ?>"</p>

                            <?php if (!empty($avaliacao['resposta_tecnico'])): ?>
                                <hr>
                                <p class="ms-3">
                                    <strong>Sua Resposta:</strong><br>
                                    <span class="text-muted"><?php echo htmlspecialchars($avaliacao['resposta_tecnico']); ?></span>
                                </p>
                            <?php else: ?>
                                <hr>
                                <form action="../processa/processaResposta.php" method="post">
                                    <input type="hidden" name="avaliacao_id" value="<?php echo $avaliacao['id']; ?>">
                                    <div class="mb-2">
                                        <textarea name="resposta" class="form-control" rows="2" placeholder="Agradeça o cliente ou esclareça algum ponto..." required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-outline-primary">Responder</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php
include '../includes/footer.php';
?>

