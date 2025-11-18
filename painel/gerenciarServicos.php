<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'tecnico') {
    header('Location: ../auth/login.php?erro=acesso_negado');
    exit;
}

require_once '../includes/db.php';
require_once '../classes/ServicoDAO.php';

$servicoDAO = new ServicoDAO($pdo);
$servicos = $servicoDAO->buscarPorTecnicoId($_SESSION['usuario_id']);

include '../includes/header.php';
?>

<main class="container my-5">
    <div class="row">
        <div class="col-md-3">
            <?php 
                $paginaAtiva = 'servicos'; // Define a página ativa para o menu
                include '../includes/menuTecnico.php'; 
            ?>
        </div>

        <div class="col-md-9">
            <h1>Gerenciar Serviços</h1>
            <hr>

            <div class="card mb-4">
                <div class="card-header">Adicionar Novo Serviço</div>
                <div class="card-body">
                    <form action="../processa/processaAdicionarServico.php" method="post">
                        <div class="mb-3">
                            <label for="titulo" class="form-label">Título do Serviço</label>
                            <input type="text" class="form-control" id="titulo" name="titulo" placeholder="Ex: Formatação com Backup" required>
                        </div>
                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição (opcional)</label>
                            <textarea class="form-control" id="descricao" name="descricao" rows="3" placeholder="Detalhe o que está incluído no serviço..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="preco" class="form-label">Preço (R$)</label>
                            <input type="number" step="0.01" class="form-control" id="preco" name="preco" placeholder="150.00">
                        </div>
                        <button type="submit" class="btn btn-primary">Adicionar Serviço</button>
                    </form>
                </div>
            </div>

            <h2>Seus Serviços Cadastrados</h2>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th scope="col">Título</th>
                            <th scope="col">Preço (R$)</th>
                            <th scope="col">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($servicos)): ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted">Você ainda não cadastrou nenhum serviço.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($servicos as $servico): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($servico['titulo']); ?></td>
                                    <td><?php echo number_format($servico['preco'], 2, ',', '.'); ?></td>
                                    <td>
                                        <a href="editarServico.php?id=<?php echo $servico['id']; ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                                        <a href="../processa/processaDeletarServico.php?id=<?php echo $servico['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Tem certeza que deseja excluir este serviço?');">Excluir</a>
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

