<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'tecnico') {
    header('Location: ../auth/login.php?erro=acesso_negado');
    exit;
}

require_once '../includes/db.php';
require_once '../classes/ServicoDAO.php';

$servicoDAO = new ServicoDAO($pdo);

// Pega o ID do serviço pela URL e busca os dados
$servico = false;
if (isset($_GET['id'])) {
    $servico = $servicoDAO->buscarPorId($_GET['id'], $_SESSION['usuario_id']);
}

// Se não encontrar o serviço (ou se não for do técnico logado), volta para a lista.
if (!$servico) {
    header('Location: gerenciarServicos.php?erro=servico_invalido');
    exit;
}

include '../includes/header.php';
?>

<main class="container my-5">
    <div class="row">
        <div class="col-md-3">
            <?php 
                $paginaAtiva = 'servicos';
                include '../includes/menuTecnico.php'; 
            ?>
        </div>

        <div class="col-md-9">
            <h1>Editar Serviço</h1>
            <hr>

            <div class="card">
                <div class="card-body">
                    <form action="../processa/processaEdicaoServico.php" method="post">
                        <input type="hidden" name="id" value="<?php echo $servico['id']; ?>">

                        <div class="mb-3">
                            <label for="titulo" class="form-label">Título do Serviço</label>
                            <input type="text" class="form-control" id="titulo" name="titulo" value="<?php echo htmlspecialchars($servico['titulo']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição (opcional)</label>
                            <textarea class="form-control" id="descricao" name="descricao" rows="3"><?php echo htmlspecialchars($servico['descricao'] ?? ''); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="preco" class="form-label">Preço (R$)</label>
                            <input type="number" step="0.01" class="form-control" id="preco" name="preco" value="<?php echo $servico['preco']; ?>">
                        </div>
                        <a href="gerenciarServicos.php" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
include '../includes/footer.php';
?>

