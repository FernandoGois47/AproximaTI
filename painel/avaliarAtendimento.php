<?php
session_start();

// Segurança: Apenas clientes logados podem acessar
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'cliente') {
    header('Location: ../auth/login.php?erro=acesso_negado');
    exit;
}

require_once '../includes/db.php';
require_once '../classes/AtendimentoDAO.php';
require_once '../classes/AvaliacaoDAO.php';
require_once '../classes/UsuarioDAO.php';

$atendimentoDAO = new AtendimentoDAO($pdo);
$avaliacaoDAO = new AvaliacaoDAO($pdo);
$usuarioDAO = new UsuarioDAO($pdo);

$clienteId = $_SESSION['usuario_id'];
$atendimentoId = $_GET['atendimento_id'] ?? null;

// Validação do atendimento_id
if (empty($atendimentoId)) {
    header('Location: meusPedidos.php?erro=atendimento_invalido');
    exit;
}

// Busca o atendimento e verifica se pertence ao cliente e está concluído
$atendimento = $atendimentoDAO->buscarPorId($atendimentoId);
if (!$atendimento || $atendimento['cliente_id'] != $clienteId || $atendimento['status'] !== 'concluido') {
    header('Location: meusPedidos.php?erro=atendimento_invalido');
    exit;
}

// Busca dados do técnico
$tecnico = $usuarioDAO->buscarPorId($atendimento['tecnico_id']);
if (!$tecnico) {
    header('Location: meusPedidos.php?erro=tecnico_nao_encontrado');
    exit;
}

// Verifica se já foi avaliado este atendimento específico
$jaAvaliou = $avaliacaoDAO->verificarSeJaAvaliou($clienteId, $atendimento['tecnico_id'], $atendimentoId);

include '../includes/header.php';
?>

<main class="container my-5">
    <div class="row">
        <div class="col-md-3">
            <?php 
                $paginaAtiva = 'pedidos';
                include '../includes/menuCliente.php'; 
            ?>
        </div>

        <div class="col-md-9">
            <h1>Avaliar Serviço</h1>
            <hr>

            <?php if ($jaAvaliou): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Você já avaliou este atendimento.
                </div>
                <a href="meusPedidos.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Voltar para Meus Pedidos
                </a>
            <?php else: ?>
                <!-- Informações do Atendimento -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="bi bi-info-circle"></i> Informações do Serviço</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Técnico:</strong> <?php echo htmlspecialchars($tecnico['nome']); ?></p>
                        <p><strong>Serviço:</strong> <?php echo htmlspecialchars($atendimento['servico_titulo'] ?? 'N/A'); ?></p>
                        <p><strong>Data de Conclusão:</strong> <?php echo date('d/m/Y H:i', strtotime($atendimento['data_atendimento'])); ?></p>
                    </div>
                </div>

                <!-- Formulário de Avaliação -->
                <div class="card shadow-sm border-primary">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-star"></i> Avaliar Serviço</h5>
                    </div>
                    <div class="card-body">
                        <form action="../processa/processaAvaliacao.php" method="post">
                            <input type="hidden" name="tecnico_id" value="<?php echo $atendimento['tecnico_id']; ?>">
                            <input type="hidden" name="atendimento_id" value="<?php echo $atendimentoId; ?>">
                            <input type="hidden" name="origem" value="meusPedidos">
                            
                            <div class="mb-3">
                                <label class="form-label">Nota</label>
                                <div class="d-flex gap-2 align-items-center" id="starRating">
                                    <?php for($i = 1; $i <= 5; $i++): ?>
                                        <div class="form-check">
                                            <input class="form-check-input d-none" type="radio" name="nota" id="nota<?php echo $i; ?>" value="<?php echo $i; ?>" required>
                                            <label class="form-check-label text-warning" for="nota<?php echo $i; ?>" style="font-size: 1.8rem; cursor: pointer; user-select: none;" data-rating="<?php echo $i; ?>">
                                                <i class="bi bi-star" id="star<?php echo $i; ?>"></i>
                                            </label>
                                        </div>
                                    <?php endfor; ?>
                                    <span class="ms-2 text-muted small" id="ratingText">Clique para avaliar</span>
                                </div>
                            </div>

                            <script>
                            // Interatividade das estrelas
                            document.querySelectorAll('[data-rating]').forEach(star => {
                                star.addEventListener('click', function() {
                                    const rating = parseInt(this.getAttribute('data-rating'));
                                    updateStars(rating);
                                });
                                
                                star.addEventListener('mouseenter', function() {
                                    const rating = parseInt(this.getAttribute('data-rating'));
                                    highlightStars(rating);
                                });
                            });
                            
                            document.getElementById('starRating').addEventListener('mouseleave', function() {
                                const selected = document.querySelector('input[name="nota"]:checked');
                                if (selected) {
                                    updateStars(parseInt(selected.value));
                                } else {
                                    resetStars();
                                }
                            });
                            
                            function updateStars(rating) {
                                for(let i = 1; i <= 5; i++) {
                                    const star = document.getElementById('star' + i);
                                    const input = document.getElementById('nota' + i);
                                    if (i <= rating) {
                                        star.className = 'bi bi-star-fill';
                                        star.style.color = '#ffc107';
                                    } else {
                                        star.className = 'bi bi-star';
                                        star.style.color = '#6c757d';
                                    }
                                }
                                const texts = ['', 'Ruim', 'Regular', 'Bom', 'Muito Bom', 'Excelente'];
                                document.getElementById('ratingText').textContent = texts[rating] || 'Clique para avaliar';
                            }
                            
                            function highlightStars(rating) {
                                const selected = document.querySelector('input[name="nota"]:checked');
                                if (!selected) {
                                    for(let i = 1; i <= 5; i++) {
                                        const star = document.getElementById('star' + i);
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
                            
                            function resetStars() {
                                for(let i = 1; i <= 5; i++) {
                                    const star = document.getElementById('star' + i);
                                    star.className = 'bi bi-star';
                                    star.style.color = '#6c757d';
                                }
                                document.getElementById('ratingText').textContent = 'Clique para avaliar';
                            }
                            
                            // Atualiza quando um radio é selecionado
                            document.querySelectorAll('input[name="nota"]').forEach(input => {
                                input.addEventListener('change', function() {
                                    updateStars(parseInt(this.value));
                                });
                            });
                            </script>
                            
                            <div class="mb-3">
                                <label for="comentario" class="form-label">Comentário</label>
                                <textarea class="form-control" id="comentario" name="comentario" rows="4" placeholder="Deixe seu comentário sobre o serviço recebido..." required></textarea>
                                <small class="form-text text-muted">Compartilhe sua experiência com este serviço.</small>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-send"></i> Enviar Avaliação
                                </button>
                                <a href="meusPedidos.php" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php
include '../includes/footer.php';
?>

