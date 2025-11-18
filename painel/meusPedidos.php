<?php
session_start();

// Segurança: Apenas clientes logados podem acessar.
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'cliente') {
    header('Location: ../auth/login.php?erro=acesso_negado');
    exit;
}

require_once '../includes/db.php';
require_once '../classes/AtendimentoDAO.php'; // Inclui o DAO de Atendimentos
require_once '../classes/AvaliacaoDAO.php';

$atendimentoDAO = new AtendimentoDAO($pdo);
$avaliacaoDAO = new AvaliacaoDAO($pdo);
$pedidos = $atendimentoDAO->buscarPorClienteId($_SESSION['usuario_id']);

// Busca tecnico_id para cada pedido e verifica se já foi avaliado
foreach ($pedidos as &$pedido) {
    // Busca o atendimento completo para pegar o tecnico_id
    $atendimentoCompleto = $atendimentoDAO->buscarPorId($pedido['id']);
    if ($atendimentoCompleto) {
        $pedido['tecnico_id'] = $atendimentoCompleto['tecnico_id'];
        $pedido['ja_avaliado'] = $avaliacaoDAO->verificarSeJaAvaliou(
            $_SESSION['usuario_id'], 
            $atendimentoCompleto['tecnico_id'], 
            $pedido['id']
        );
    } else {
        $pedido['ja_avaliado'] = false;
    }
}
unset($pedido); // Remove a referência

include '../includes/header.php';
?>

<main class="container my-5">
    <div class="row">
        <div class="col-md-3">
            <?php 
                $paginaAtiva = 'pedidos'; // Define a página ativa para o menu
                include '../includes/menuCliente.php'; 
            ?>
        </div>

        <div class="col-md-9">
            <h1>Meus Pedidos</h1>
            <hr>
            <p>Acompanhe aqui o status de todos os serviços que você solicitou.</p>

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th scope="col">Data Solicitação</th>
                            <th scope="col">Técnico</th>
                            <th scope="col">Serviço</th>
                            <th scope="col">Status</th>
                            <th scope="col">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pedidos)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted">Você ainda não fez nenhum pedido.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($pedidos as $pedido): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y H:i', strtotime($pedido['data_atendimento'])); ?></td>
                                    <td><?php echo htmlspecialchars($pedido['tecnico_nome']); ?></td>
                                    <td><?php echo htmlspecialchars($pedido['servico_titulo']); ?></td>
                                    <td>
                                        <span class="badge 
                                            <?php 
                                                switch($pedido['status']) {
                                                    case 'pendente': echo 'bg-warning text-dark'; break;
                                                    case 'em_andamento': echo 'bg-info text-dark'; break;
                                                    case 'concluido': echo 'bg-success'; break;
                                                    case 'cancelado': echo 'bg-danger'; break;
                                                    default: echo 'bg-secondary';
                                                }
                                            ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $pedido['status'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (in_array($pedido['status'], ['pendente', 'em_andamento'])): ?>
                                            <a href="chat.php?atendimento_id=<?php echo $pedido['id']; ?>" class="btn btn-sm btn-info" title="Abrir Chat">
                                                <i class="bi bi-chat-dots"></i> Chat
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($pedido['status'] === 'concluido'): ?>
                                            <?php if (isset($pedido['ja_avaliado']) && $pedido['ja_avaliado']): ?>
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle"></i> Avaliado
                                                </span>
                                            <?php else: ?>
                                                <a href="avaliarAtendimento.php?atendimento_id=<?php echo $pedido['id']; ?>" class="btn btn-sm btn-success">
                                                    <i class="bi bi-star"></i> Avaliar
                                                </a>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php
include '../includes/footer.php';
?>

