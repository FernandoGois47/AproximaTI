<?php
require_once 'includes/header.php';
require_once '../includes/db.php';
require_once '../classes/UsuarioDAO.php';

$usuarioDAO = new UsuarioDAO($pdo);

// Estatísticas
$totalUsuarios = count($usuarioDAO->listarTodos());
$usuariosTecnicos = 0;
$usuariosClientes = 0;
$usuariosAdmin = 0;

$todosUsuarios = $usuarioDAO->listarTodos();
foreach ($todosUsuarios as $usuario) {
    if ($usuario['tipo'] === 'tecnico') {
        $usuariosTecnicos++;
    } elseif ($usuario['tipo'] === 'cliente') {
        $usuariosClientes++;
    } elseif ($usuario['tipo'] === 'admin') {
        $usuariosAdmin++;
    }
}
?>

<div class="row mb-4">
    <div class="col-12">
        <h1 class="h2 mb-4">
            <i class="bi bi-speedometer2"></i> Dashboard Administrativo
        </h1>
        <p class="text-muted">Bem-vindo, <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>! Gerencie todos os aspectos da plataforma AproximaTI.</p>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Total de Usuários</h6>
                        <h2 class="mb-0"><?php echo $totalUsuarios; ?></h2>
                    </div>
                    <div class="text-primary" style="font-size: 3rem;">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent">
                <a href="listarUsuarios.php" class="btn btn-sm btn-primary">Ver Todos</a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Técnicos</h6>
                        <h2 class="mb-0"><?php echo $usuariosTecnicos; ?></h2>
                    </div>
                    <div class="text-success" style="font-size: 3rem;">
                        <i class="bi bi-tools"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent">
                <a href="listarUsuarios.php?tipo=tecnico" class="btn btn-sm btn-success">Ver Técnicos</a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-2">Clientes</h6>
                        <h2 class="mb-0"><?php echo $usuariosClientes; ?></h2>
                    </div>
                    <div class="text-info" style="font-size: 3rem;">
                        <i class="bi bi-person-check"></i>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent">
                <a href="listarUsuarios.php?tipo=cliente" class="btn btn-sm btn-info">Ver Clientes</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-list-ul"></i> Ações Rápidas</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <a href="listarUsuarios.php" class="btn btn-outline-primary w-100 text-start">
                            <i class="bi bi-people me-2"></i>
                            Gerenciar Usuários
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="../index.php" class="btn btn-outline-secondary w-100 text-start">
                            <i class="bi bi-house me-2"></i>
                            Voltar ao Site
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

