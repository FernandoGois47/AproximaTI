<?php
require_once 'includes/header.php';
require_once '../includes/db.php';
require_once '../classes/UsuarioDAO.php';

$usuarioDAO = new UsuarioDAO($pdo);

$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: listarUsuarios.php?erro=usuario_nao_encontrado');
    exit;
}

$usuario = $usuarioDAO->buscarPorIdCompleto($id);

if (!$usuario) {
    header('Location: listarUsuarios.php?erro=usuario_nao_encontrado');
    exit;
}
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h2 mb-2">
                    <i class="bi bi-pencil-square"></i> Editar Usuário
                </h1>
                <p class="text-muted mb-0">Altere as informações do usuário.</p>
            </div>
            <div>
                <a href="listarUsuarios.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Voltar
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="processaEdicao.php">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($usuario['id']); ?>">

                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome Completo</label>
                        <input type="text" class="form-control" id="nome" name="nome" 
                               value="<?php echo htmlspecialchars($usuario['nome']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="tipo" class="form-label">Tipo de Usuário</label>
                        <select class="form-select" id="tipo" name="tipo" required>
                            <option value="cliente" <?php echo $usuario['tipo'] === 'cliente' ? 'selected' : ''; ?>>Cliente</option>
                            <option value="tecnico" <?php echo $usuario['tipo'] === 'tecnico' ? 'selected' : ''; ?>>Técnico</option>
                            <option value="admin" <?php echo $usuario['tipo'] === 'admin' ? 'selected' : ''; ?>>Administrador</option>
                        </select>
                        <small class="form-text text-muted">
                            <?php if ($usuario['id'] == $_SESSION['usuario_id']): ?>
                                <span class="text-warning"><i class="bi bi-exclamation-triangle"></i> Você não pode alterar seu próprio tipo de usuário.</span>
                            <?php endif; ?>
                        </small>
                    </div>

                    <div class="mb-3">
                        <label for="telefone" class="form-label">Telefone</label>
                        <input type="text" class="form-control" id="telefone" name="telefone" 
                               value="<?php echo htmlspecialchars($usuario['telefone'] ?? ''); ?>" 
                               placeholder="(00) 00000-0000">
                    </div>

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="cidade" class="form-label">Cidade</label>
                            <input type="text" class="form-control" id="cidade" name="cidade" 
                                   value="<?php echo htmlspecialchars($usuario['cidade'] ?? ''); ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="estado" class="form-label">Estado (UF)</label>
                            <input type="text" class="form-control" id="estado" name="estado" 
                                   value="<?php echo htmlspecialchars($usuario['estado'] ?? ''); ?>" 
                                   maxlength="2" placeholder="PR">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="especialidade" class="form-label">Especialidade</label>
                        <input type="text" class="form-control" id="especialidade" name="especialidade" 
                               value="<?php echo htmlspecialchars($usuario['especialidade'] ?? ''); ?>" 
                               placeholder="Ex: Desenvolvimento Web, Infraestrutura, etc.">
                        <small class="form-text text-muted">Principalmente para técnicos.</small>
                    </div>

                    <div class="mb-3">
                        <label for="senha" class="form-label">Nova Senha (opcional)</label>
                        <input type="password" class="form-control" id="senha" name="senha" 
                               placeholder="Deixe em branco para manter a senha atual">
                        <small class="form-text text-muted">Preencha apenas se desejar alterar a senha.</small>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="listarUsuarios.php" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

