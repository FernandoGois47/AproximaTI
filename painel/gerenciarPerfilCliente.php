<?php
session_start();

// Segurança: Apenas clientes logados podem acessar.
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'cliente') {
    header('Location: ../auth/login.php?erro=acesso_negado');
    exit;
}

require_once '../includes/db.php';
require_once '../classes/UsuarioDAO.php';

// Busca os dados atuais do cliente para preencher o formulário
$usuarioDAO = new UsuarioDAO($pdo);
$cliente = $usuarioDAO->buscarPorId($_SESSION['usuario_id']);

// Se por algum motivo não encontrar o cliente, redireciona para o painel
if (!$cliente) {
    header('Location: painelCliente.php?erro=usuario_nao_encontrado');
    exit;
}

$avatarUrl = '../assets/img/' . ($_SESSION['usuario_foto'] ?? 'avatar_default.png');

include '../includes/header.php';
?>

<main class="container my-5">
    <div class="row">
        <div class="col-md-3">
            <?php 
                $paginaAtiva = 'perfil';
                include '../includes/menuCliente.php'; 
            ?>
        </div>

        <div class="col-md-9">
            <h1>Editar Perfil</h1>
            <hr>
            <p>Atualize suas informações pessoais e de contato.</p>

            <div class="card">
                <div class="card-body">
                    <form action="../processa/processaEdicaoPerfilCliente.php" method="post" enctype="multipart/form-data">
                        
                        <div class="row align-items-center mb-4">
                            <div class="col-md-3 text-center">
                                <img src="<?php echo $avatarUrl; ?>" 
                                    alt="Avatar" class="rounded-circle img-fluid" style="width: 120px; height: 120px; object-fit: cover;">
                            </div>
                            <div class="col-md-9">
                                <label for="foto" class="form-label">Alterar Foto de Perfil</label>
                                <input class="form-control" type="file" id="foto" name="foto" accept="image/*">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome Completo</label>
                            <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($cliente['nome']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($cliente['email']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="telefone" class="form-label">Telefone</label>
                            <input type="text" class="form-control" id="telefone" name="telefone" value="<?php echo htmlspecialchars($cliente['telefone'] ?? ''); ?>">
                        </div>
                        <div class="row">
                            <div class="col-md-9 mb-3">
                                <label for="cidade" class="form-label">Cidade</label>
                                <input type="text" class="form-control" id="cidade" name="cidade" value="<?php echo htmlspecialchars($cliente['cidade'] ?? ''); ?>">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="estado" class="form-label">Estado (UF)</label>
                                <input type="text" class="form-control" id="estado" name="estado" value="<?php echo htmlspecialchars($cliente['estado'] ?? ''); ?>" maxlength="2">
                            </div>
                        </div>
                        
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

