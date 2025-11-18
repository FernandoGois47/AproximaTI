<?php 
include 'includes/header.php';
require_once 'includes/db.php';
require_once 'classes/TecnicoDAO.php';
require_once 'classes/PortfolioDAO.php';

// Pega o termo de busca da URL (se existir)
$termoBusca = $_GET['busca'] ?? '';

$tecnicoDAO = new TecnicoDAO($pdo);
$portfolioDAO = new PortfolioDAO($pdo);

// Se houver um termo de busca, usa o método de filtro.
// Senão, busca todos os técnicos.
if (!empty($termoBusca)) {
    $tecnicos = $tecnicoDAO->buscarPorTermo($termoBusca);
} else {
    $tecnicos = $tecnicoDAO->buscarTodos();
}

// Busca portfólio para cada técnico
foreach ($tecnicos as &$tecnico) {
    $tecnico['portfolio'] = $portfolioDAO->buscarPorTecnicoId($tecnico['id']);
    // Limita a 3 itens do portfólio para exibição
    $tecnico['portfolio'] = array_slice($tecnico['portfolio'], 0, 3);
}
unset($tecnico); // Remove referência
?>

<main class="container my-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="display-6 fw-bold">Encontre o Profissional Ideal</h1>
            <p class="text-muted">Navegue pelos perfis e portfólios ou busque por nome do técnico ou cidade.</p>
        </div>
        <div class="col-md-4 align-self-center">
            <form action="servico1.php" method="get">
                <div class="input-group">
                    <input type="text" class="form-control" name="busca" placeholder="Buscar por nome ou cidade..." value="<?php echo htmlspecialchars($termoBusca); ?>">
                    <button class="btn btn-primary" type="submit">Buscar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4">
        <?php if (!empty($tecnicos)): ?>
            <?php foreach ($tecnicos as $tecnico): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body d-flex flex-column">
                            <!-- Foto e informações básicas -->
                            <div class="text-center mb-3">
                                <img src="assets/img/<?php echo htmlspecialchars($tecnico['foto_perfil'] ?? 'avatar_default.png'); ?>" alt="Foto de <?php echo htmlspecialchars($tecnico['nome']); ?>" class="rounded-circle mb-2" style="width: 100px; height: 100px; object-fit: cover;">
                                <h5 class="card-title mb-1"><?php echo htmlspecialchars($tecnico['nome']); ?></h5>
                                <h6 class="card-subtitle mb-2 text-muted small">
                                    <i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($tecnico['cidade']); ?> - <?php echo htmlspecialchars($tecnico['estado']); ?>
                                </h6>
                                <div class="text-warning mb-2">
                                    <?php if ($tecnico['media_avaliacoes']): 
                                        $media = round($tecnico['media_avaliacoes']);
                                        for($i = 0; $i < $media; $i++) { echo '<i class="bi bi-star-fill"></i>'; }
                                        for($i = $media; $i < 5; $i++) { echo '<i class="bi bi-star"></i>'; }
                                    ?>
                                        <span class="text-muted small">(<?php echo number_format($tecnico['media_avaliacoes'], 1, ','); ?>)</span>
                                    <?php else: ?>
                                        <small class="text-muted">Sem avaliações</small>
                                    <?php endif; ?>
                                </div>
                                <p class="card-text small">
                                    <strong>Especialidade:</strong> <?php echo htmlspecialchars($tecnico['especialidade']); ?>
                                </p>
                            </div>

                            <!-- Portfólio -->
                            <?php if (!empty($tecnico['portfolio'])): ?>
                                <div class="mb-3">
                                    <h6 class="small text-muted mb-2"><i class="bi bi-images"></i> Portfólio</h6>
                                    <div class="row g-2">
                                        <?php foreach ($tecnico['portfolio'] as $item): ?>
                                            <div class="col-4">
                                                <img src="assets/img/portfolio/<?php echo htmlspecialchars($item['imagem_url']); ?>" 
                                                     class="img-fluid rounded" 
                                                     alt="<?php echo htmlspecialchars($item['titulo']); ?>"
                                                     style="width: 100%; height: 80px; object-fit: cover; cursor: pointer;"
                                                     onclick="window.location.href='perfil.php?id=<?php echo $tecnico['id']; ?>'"
                                                     title="<?php echo htmlspecialchars($item['titulo']); ?>">
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="mb-3">
                                    <p class="text-muted small mb-0"><i class="bi bi-images"></i> Sem portfólio ainda</p>
                                </div>
                            <?php endif; ?>

                            <div class="mt-auto">
                                <a href="perfil.php?id=<?php echo $tecnico['id']; ?>" class="btn btn-primary w-100">Ver Perfil Completo</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info text-center" role="alert">
                    Nenhum técnico encontrado para a sua busca.
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php 
include 'includes/footer.php';
?>