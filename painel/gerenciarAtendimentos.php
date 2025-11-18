<?php
session_start();

// Segurança: Apenas técnicos logados podem acessar.
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'tecnico') {
    header('Location: ../auth/login.php?erro=acesso_negado');
    exit;
}

require_once '../includes/db.php';
require_once '../classes/AtendimentoDAO.php'; // Inclui o DAO de Atendimentos

$atendimentoDAO = new AtendimentoDAO($pdo);
$atendimentos = $atendimentoDAO->buscarPorTecnicoId($_SESSION['usuario_id']);

include '../includes/header.php';
?>

<main class="container my-5">
    <div class="row">
        <div class="col-md-3">
            <?php 
                $paginaAtiva = 'atendimentos'; // Define a página ativa
                include '../includes/menuTecnico.php'; 
            ?>
        </div>

        <div class="col-md-9">
            <h1>Solicitações Recebidas</h1>
            <hr>
            <p>Gerencie todos os pedidos de serviço feitos por clientes. Você pode aceitar, iniciar ou concluir o serviço.</p>

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th scope="col">Data</th>
                            <th scope="col">Cliente</th>
                            <th scope="col">Serviço Solicitado</th>
                            <th scope="col">Status Atual</th>
                            <th scope="col">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($atendimentos)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted">Você ainda não recebeu nenhuma solicitação de serviço.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($atendimentos as $atendimento): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y H:i', strtotime($atendimento['data_atendimento'])); ?></td>
                                    <td><?php echo htmlspecialchars($atendimento['cliente_nome']); ?></td>
                                    <td><?php echo htmlspecialchars($atendimento['servico_titulo']); ?></td>
                                    <td>
                                        <span class="badge 
                                            <?php 
                                                switch($atendimento['status']) {
                                                    case 'pendente': echo 'bg-warning text-dark'; break;
                                                    case 'em_andamento': echo 'bg-info text-dark'; break;
                                                    case 'concluido': echo 'bg-success'; break;
                                                    case 'cancelado': echo 'bg-danger'; break;
                                                    default: echo 'bg-secondary';
                                                }
                                            ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $atendimento['status'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="chat.php?atendimento_id=<?php echo $atendimento['id']; ?>" class="btn btn-sm btn-info mb-1" title="Abrir Chat">
                                            <i class="bi bi-chat-dots"></i> Chat
                                        </a>
                                        <?php if ($atendimento['status'] === 'pendente'): ?>
                                            <form action="../processa/processaStatusAtendimento.php" method="post" class="d-inline">
                                                <input type="hidden" name="id" value="<?php echo $atendimento['id']; ?>">
                                                <input type="hidden" name="novo_status" value="em_andamento">
                                                <button type="submit" class="btn btn-sm btn-success">Aceitar</button>
                                            </form>
                                            <form action="../processa/processaStatusAtendimento.php" method="post" class="d-inline">
                                                <input type="hidden" name="id" value="<?php echo $atendimento['id']; ?>">
                                                <input type="hidden" name="novo_status" value="cancelado">
                                                <button type="submit" class="btn btn-sm btn-danger">Recusar</button>
                                            </form>
                                        <?php elseif ($atendimento['status'] === 'em_andamento'): ?>
                                            <form action="../processa/processaStatusAtendimento.php" method="post" class="d-inline">
                                                <input type="hidden" name="id" value="<?php echo $atendimento['id']; ?>">
                                                <input type="hidden" name="novo_status" value="concluido">
                                                <button type="submit" class="btn btn-sm btn-primary">Concluir</button>
                                            </form>
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

