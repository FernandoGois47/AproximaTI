<?php
session_start();
require_once 'includes/db.php';
require_once 'classes/UsuarioDAO.php';
require_once 'classes/PortfolioDAO.php';
require_once 'classes/ServicoDAO.php';
require_once 'classes/AvaliacaoDAO.php'; // Inclui a nova classe

$tecnicoId = $_GET['id'] ?? null;
if (!$tecnicoId) { die("Técnico não encontrado."); }

$usuarioDAO = new UsuarioDAO($pdo);
$tecnico = $usuarioDAO->buscarPorId($tecnicoId);
if (!$tecnico) { die("Técnico não encontrado."); }

$portfolioDAO = new PortfolioDAO($pdo);
$portfolioItens = $portfolioDAO->buscarPorTecnicoId($tecnicoId);

$servicoDAO = new ServicoDAO($pdo);
$servicos = $servicoDAO->buscarPorTecnicoId($tecnicoId);

// Busca os dados de avaliação
$avaliacaoDAO = new AvaliacaoDAO($pdo);
$estatisticas = $avaliacaoDAO->obterEstatisticasPorTecnicoId($tecnicoId);
$avaliacoes = $avaliacaoDAO->buscarPorTecnicoId($tecnicoId);

include 'includes/header.php';
?>

<main class="container my-5">
    <div class="row">
        <div class="col-lg-8">
            <div class="d-flex align-items-center mb-4">
                <img src="assets/img/<?php echo htmlspecialchars($tecnico['foto_perfil'] ?? 'tecnico_default.jpg'); ?>" alt="Foto de <?php echo htmlspecialchars($tecnico['nome']); ?>" class="rounded-circle me-3" style="width: 120px; height: 120px; object-fit: cover;">
                <div>
                    <h1 class="mb-0"><?php echo htmlspecialchars($tecnico['nome']); ?></h1>
                    <p class="fs-5 text-muted"><?php echo htmlspecialchars($tecnico['especialidade']); ?></p>
                    <p class="text-muted"><i class="bi bi-geo-alt-fill"></i> <?php echo htmlspecialchars($tecnico['cidade']); ?> - <?php echo htmlspecialchars($tecnico['estado']); ?></p>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h4 class="card-title">Portfólio de Trabalhos</h4>
                    <?php if (empty($portfolioItens)): ?>
                        <p class="text-muted">Este técnico ainda não adicionou trabalhos ao seu portfólio.</p>
                    <?php else: ?>
                        <div class="row g-3">
                            <?php foreach ($portfolioItens as $item): ?>
                                <div class="col-md-4"><img src="assets/img/portfolio/<?php echo htmlspecialchars($item['imagem_url']); ?>" class="img-fluid rounded" alt="<?php echo htmlspecialchars($item['titulo']); ?>" style="width: 100%; height: 200px; object-fit: cover;"></div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Formulário de Avaliação (apenas para clientes logados) -->
            <?php if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_tipo'] === 'cliente'): ?>
                <?php
                $clienteId = $_SESSION['usuario_id'];
                
                // Busca atendimentos concluídos do cliente com este técnico
                require_once 'classes/AtendimentoDAO.php';
                $atendimentoDAO = new AtendimentoDAO($pdo);
                $sqlAtendimentos = "SELECT a.id, a.servico_id, s.titulo AS servico_titulo, a.data_atendimento 
                                    FROM atendimentos a
                                    JOIN servicos s ON a.servico_id = s.id
                                    WHERE a.cliente_id = ? AND a.tecnico_id = ? AND a.status = 'concluido' 
                                    ORDER BY a.data_atendimento DESC";
                $stmtAtendimentos = $pdo->prepare($sqlAtendimentos);
                $stmtAtendimentos->execute([$clienteId, $tecnicoId]);
                $atendimentosConcluidos = $stmtAtendimentos->fetchAll();
                
                // Para cada atendimento, verifica se já foi avaliado
                $atendimentosParaAvaliar = [];
                foreach ($atendimentosConcluidos as $atendimento) {
                    $jaAvaliou = $avaliacaoDAO->verificarSeJaAvaliou($clienteId, $tecnicoId, $atendimento['id']);
                    if (!$jaAvaliou) {
                        $atendimentosParaAvaliar[] = $atendimento;
                    }
                }
                ?>
                
                <?php if (!empty($atendimentosParaAvaliar)): ?>
                    <?php foreach ($atendimentosParaAvaliar as $idx => $atendimento): ?>
                    <div class="card shadow-sm mb-4 border-primary">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="bi bi-star"></i> Avaliar Serviço</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-3">
                                <strong>Serviço:</strong> <?php echo htmlspecialchars($atendimento['servico_titulo']); ?><br>
                                <small>Concluído em: <?php echo date('d/m/Y H:i', strtotime($atendimento['data_atendimento'])); ?></small>
                            </p>
                            <form action="processa/processaAvaliacao.php" method="post">
                                <input type="hidden" name="tecnico_id" value="<?php echo $tecnicoId; ?>">
                                <input type="hidden" name="atendimento_id" value="<?php echo $atendimento['id']; ?>">
                                <input type="hidden" name="origem" value="perfil">
                                
                                <div class="mb-3">
                                    <label class="form-label">Nota</label>
                                    <div class="d-flex gap-2 align-items-center" id="starRating<?php echo $idx; ?>">
                                        <?php for($i = 1; $i <= 5; $i++): ?>
                                            <div class="form-check">
                                                <input class="form-check-input d-none" type="radio" name="nota" id="nota<?php echo $idx . '_' . $i; ?>" value="<?php echo $i; ?>" required>
                                                <label class="form-check-label text-warning" for="nota<?php echo $idx . '_' . $i; ?>" style="font-size: 1.8rem; cursor: pointer; user-select: none;" data-rating="<?php echo $i; ?>" data-form="<?php echo $idx; ?>">
                                                    <i class="bi bi-star" id="star<?php echo $idx . '_' . $i; ?>"></i>
                                                </label>
                                            </div>
                                        <?php endfor; ?>
                                        <span class="ms-2 text-muted small" id="ratingText<?php echo $idx; ?>">Clique para avaliar</span>
                                    </div>
                                </div>
                                <script>
                                (function() {
                                    const formIdx = <?php echo $idx; ?>;
                                    const starRating = document.getElementById('starRating' + formIdx);
                                    const ratingText = document.getElementById('ratingText' + formIdx);
                                    
                                    // Interatividade das estrelas
                                    starRating.querySelectorAll('[data-rating]').forEach(star => {
                                        star.addEventListener('click', function() {
                                            const rating = parseInt(this.getAttribute('data-rating'));
                                            updateStars(rating, formIdx);
                                        });
                                        
                                        star.addEventListener('mouseenter', function() {
                                            const rating = parseInt(this.getAttribute('data-rating'));
                                            highlightStars(rating, formIdx);
                                        });
                                    });
                                    
                                    starRating.addEventListener('mouseleave', function() {
                                        const selected = starRating.querySelector('input[name="nota"]:checked');
                                        if (selected) {
                                            updateStars(parseInt(selected.value), formIdx);
                                        } else {
                                            resetStars(formIdx);
                                        }
                                    });
                                    
                                    function updateStars(rating, idx) {
                                        for(let i = 1; i <= 5; i++) {
                                            const star = document.getElementById('star' + idx + '_' + i);
                                            const input = document.getElementById('nota' + idx + '_' + i);
                                            if (i <= rating) {
                                                star.className = 'bi bi-star-fill';
                                                star.style.color = '#ffc107';
                                            } else {
                                                star.className = 'bi bi-star';
                                                star.style.color = '#6c757d';
                                            }
                                        }
                                        const texts = ['', 'Ruim', 'Regular', 'Bom', 'Muito Bom', 'Excelente'];
                                        document.getElementById('ratingText' + idx).textContent = texts[rating] || 'Clique para avaliar';
                                    }
                                    
                                    function highlightStars(rating, idx) {
                                        const selected = starRating.querySelector('input[name="nota"]:checked');
                                        if (!selected) {
                                            for(let i = 1; i <= 5; i++) {
                                                const star = document.getElementById('star' + idx + '_' + i);
                                                if (i <= rating) {
                                                    star.className = 'bi bi-star-fill';
                                                    star.style.color = '#ffc107';
                                                } else {
                                                    star.className = 'bi bi-star';
                                                    star.style.color = '#6c757d';
                                                }
                                            }
                                        }
                                    }
                                    
                                    function resetStars(idx) {
                                        for(let i = 1; i <= 5; i++) {
                                            const star = document.getElementById('star' + idx + '_' + i);
                                            star.className = 'bi bi-star';
                                            star.style.color = '#6c757d';
                                        }
                                        document.getElementById('ratingText' + idx).textContent = 'Clique para avaliar';
                                    }
                                    
                                    // Atualiza quando um radio é selecionado
                                    starRating.querySelectorAll('input[name="nota"]').forEach(input => {
                                        input.addEventListener('change', function() {
                                            updateStars(parseInt(this.value), formIdx);
                                        });
                                    });
                                })();
                                </script>
                                
                                <div class="mb-3">
                                    <label for="comentario" class="form-label">Comentário</label>
                                    <textarea class="form-control" id="comentario" name="comentario" rows="3" placeholder="Deixe seu comentário sobre o serviço recebido..." required></textarea>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-send"></i> Enviar Avaliação
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php elseif (empty($atendimentosConcluidos)): ?>
                    <div class="alert alert-warning mb-4">
                        <i class="bi bi-exclamation-triangle"></i> Você precisa ter um atendimento concluído com este técnico para poder avaliá-lo.
                    </div>
                <?php else: ?>
                    <div class="alert alert-info mb-4">
                        <i class="bi bi-info-circle"></i> Você já avaliou todos os serviços concluídos com este técnico.
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h4 class="card-title">O que os clientes dizem</h4>
                    <?php if (empty($avaliacoes)): ?>
                        <p class="text-muted">Este técnico ainda não recebeu avaliações.</p>
                    <?php else: ?>
                        <?php foreach($avaliacoes as $avaliacao): ?>
                            <div class="mb-3 border-bottom pb-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <strong><?php echo htmlspecialchars($avaliacao['cliente_nome']); ?></strong>
                                    <div class="text-warning">
                                        <?php for($i = 0; $i < $avaliacao['nota']; $i++) { echo '<i class="bi bi-star-fill"></i>'; } ?>
                                        <?php for($i = $avaliacao['nota']; $i < 5; $i++) { echo '<i class="bi bi-star"></i>'; } ?>
                                    </div>
                                </div>
                                <?php if (!empty($avaliacao['comentario'])): ?>
                                    <p class="mb-2 fst-italic">"<?php echo htmlspecialchars($avaliacao['comentario']); ?>"</p>
                                <?php endif; ?>
                                <?php if (!empty($avaliacao['resposta_tecnico'])): ?>
                                    <div class="ms-3 mt-2 p-2 bg-light rounded">
                                        <small class="text-muted"><strong>Resposta do técnico:</strong></small>
                                        <p class="mb-0 small"><?php echo htmlspecialchars($avaliacao['resposta_tecnico']); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    <h4 class="card-title">Avaliação Geral</h4>
                    <?php if ($estatisticas['total'] > 0): ?>
                        <h1 class="display-4 fw-bold"><?php echo number_format($estatisticas['media'], 1, ','); ?></h1>
                        <div class="text-warning mb-2">
                            <?php 
                                $fullStars = floor($estatisticas['media']);
                                $halfStar = ($estatisticas['media'] - $fullStars) >= 0.5;
                                for($i = 0; $i < $fullStars; $i++) { echo '<i class="bi bi-star-fill"></i>'; }
                                if ($halfStar) { echo '<i class="bi bi-star-half"></i>'; }
                                for($i = 0; $i < (5 - $fullStars - $halfStar); $i++) { echo '<i class="bi bi-star"></i>'; }
                            ?>
                        </div>
                        <p class="text-muted">(Baseado em <?php echo $estatisticas['total']; ?> avaliações)</p>
                    <?php else: ?>
                        <p class="text-muted mt-3">Ainda não há avaliações.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header"><h4 class="my-0 fw-normal">Serviços Oferecidos</h4></div>
                <div class="card-body">
                    <?php if (empty($servicos)): ?>
                        <p class="text-muted">Nenhum serviço cadastrado.</p>
                    <?php else: ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($servicos as $servico): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?php echo htmlspecialchars($servico['titulo']); ?></strong>
                                        <?php if (!empty($servico['descricao'])): ?>
                                            <br><small class="text-muted"><?php echo htmlspecialchars($servico['descricao']); ?></small>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($servico['preco']): ?>
                                        <span class="badge bg-primary rounded-pill">R$ <?php echo number_format($servico['preco'], 2, ',', '.'); ?></span>
                                    <?php endif; ?>
                                </li>
                                <?php if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_tipo'] === 'cliente'): ?>
                                    <li class="list-group-item">
                                        <form action="processa/processaSolicitacao.php" method="post" class="d-inline">
                                            <input type="hidden" name="tecnico_id" value="<?php echo $tecnicoId; ?>">
                                            <input type="hidden" name="servico_id" value="<?php echo $servico['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-primary w-100">
                                                <i class="bi bi-check-circle"></i> Solicitar Serviço
                                            </button>
                                        </form>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
include 'includes/footer.php';
?>