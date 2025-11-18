<?php
require_once 'includes/header.php';
require_once '../includes/db.php';
require_once '../classes/UsuarioDAO.php';

$usuarioDAO = new UsuarioDAO($pdo);

// Filtro por tipo
$tipoFiltro = $_GET['tipo'] ?? null;
$usuarios = $usuarioDAO->listarTodos();

// Aplica filtro se necessário
if ($tipoFiltro && in_array($tipoFiltro, ['tecnico', 'cliente', 'admin'])) {
    $usuarios = array_filter($usuarios, function($usuario) use ($tipoFiltro) {
        return $usuario['tipo'] === $tipoFiltro;
    });
    $usuarios = array_values($usuarios); // Reindexa o array
}

/** 
 * Retorna uma etiqueta conforme o tipo informado (ou etiqueta padrão) 
*/
function etiquetaPorTipo($tipo) {
    $etiqueta = [
        'admin' => '<span class="badge bg-danger">Admin</span>',
        'tecnico' => '<span class="badge bg-success">Técnico</span>',
        'cliente' => '<span class="badge bg-info">Cliente</span>'
    ];
    return $etiqueta[$tipo] ?? '<span class="badge bg-secondary">' . htmlspecialchars($tipo) . '</span>';
}
?>

<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h2 mb-2">
                    <i class="bi bi-people"></i> Gerenciar Usuários
                </h1>
                <p class="text-muted mb-0">Visualize, edite e exclua usuários do sistema.</p>
            </div>
            <div>
                <a href="index.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Voltar
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <div class="btn-group" role="group">
            <a href="listarUsuarios.php" class="btn btn-outline-primary <?php echo !$tipoFiltro ? 'active' : ''; ?>">
                Todos
            </a>
            <a href="listarUsuarios.php?tipo=tecnico" class="btn btn-outline-success <?php echo $tipoFiltro === 'tecnico' ? 'active' : ''; ?>">
                Técnicos
            </a>
            <a href="listarUsuarios.php?tipo=cliente" class="btn btn-outline-info <?php echo $tipoFiltro === 'cliente' ? 'active' : ''; ?>">
                Clientes
            </a>
            <a href="listarUsuarios.php?tipo=admin" class="btn btn-outline-danger <?php echo $tipoFiltro === 'admin' ? 'active' : ''; ?>">
                Administradores
            </a>
        </div>
    </div>
</div>

<!-- Tabela de Usuários -->
<div class="card">
    <div class="card-body">
        <?php if (empty($usuarios)): ?>
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle"></i> Nenhum usuário encontrado.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Tipo</th>
                            <th>Telefone</th>
                            <th>Cidade/Estado</th>
                            <th>Data Cadastro</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($usuario['id']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['nome']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                <td><?php echo etiquetaPorTipo($usuario['tipo']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['telefone'] ?? '-'); ?></td>
                                <td>
                                    <?php 
                                    $cidadeEstado = [];
                                    if (!empty($usuario['cidade'])) $cidadeEstado[] = $usuario['cidade'];
                                    if (!empty($usuario['estado'])) $cidadeEstado[] = $usuario['estado'];
                                    echo !empty($cidadeEstado) ? htmlspecialchars(implode('/', $cidadeEstado)) : '-';
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                    if (!empty($usuario['data_cadastro'])) {
                                        $data = new DateTime($usuario['data_cadastro']);
                                        echo $data->format('d/m/Y H:i');
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="editarUsuario.php?id=<?php echo $usuario['id']; ?>" 
                                           class="btn btn-outline-primary" 
                                           title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <?php if ($usuario['id'] != $_SESSION['usuario_id'] && $usuario['tipo'] !== 'admin'): ?>
                                            <a href="deletarUsuario.php?id=<?php echo $usuario['id']; ?>" 
                                               class="btn btn-outline-danger" 
                                               title="Excluir"
                                               onclick="return confirm('Tem certeza que deseja excluir o usuário <?php echo htmlspecialchars($usuario['nome']); ?>? Esta ação não pode ser desfeita.');">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        <?php else: ?>
                                            <button class="btn btn-outline-secondary" disabled title="Não é possível excluir este usuário">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

