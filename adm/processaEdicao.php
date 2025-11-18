<?php
require_once 'includes/auth.php';
require_once '../includes/db.php';
require_once '../classes/UsuarioDAO.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: listarUsuarios.php');
    exit;
}

$usuarioDAO = new UsuarioDAO($pdo);

$id = $_POST['id'] ?? null;
$nome = $_POST['nome'] ?? '';
$email = $_POST['email'] ?? '';
$tipo = $_POST['tipo'] ?? '';
$telefone = $_POST['telefone'] ?? null;
$cidade = $_POST['cidade'] ?? null;
$estado = $_POST['estado'] ?? null;
$especialidade = $_POST['especialidade'] ?? null;
$senha = $_POST['senha'] ?? null;

// Validação básica
if (empty($id) || empty($nome) || empty($email) || empty($tipo)) {
    header('Location: editarUsuario.php?id=' . $id . '&erro=campos_vazios');
    exit;
}

// Valida tipo
if (!in_array($tipo, ['cliente', 'tecnico', 'admin'])) {
    header('Location: editarUsuario.php?id=' . $id . '&erro=tipo_invalido');
    exit;
}

// Verifica se o usuário está tentando alterar seu próprio tipo
if ($id == $_SESSION['usuario_id'] && $tipo !== 'admin') {
    header('Location: editarUsuario.php?id=' . $id . '&erro=nao_pode_alterar_proprio_tipo');
    exit;
}

// Busca o usuário atual
$usuarioAtual = $usuarioDAO->buscarPorIdCompleto($id);
if (!$usuarioAtual) {
    header('Location: listarUsuarios.php?erro=usuario_nao_encontrado');
    exit;
}

// Verifica se o email já existe em outro usuário
$sql = "SELECT id FROM usuarios WHERE email = ? AND id != ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$email, $id]);
if ($stmt->fetch()) {
    header('Location: editarUsuario.php?id=' . $id . '&erro=email_existente');
    exit;
}

// Prepara dados para atualização
$dados = [
    'nome' => $nome,
    'email' => $email,
    'telefone' => $telefone ?: null,
    'cidade' => $cidade ?: null,
    'estado' => $estado ?: null,
    'especialidade' => $especialidade ?: null
];

// Se uma nova senha foi fornecida, atualiza
if (!empty($senha)) {
    $senhaHash = md5($senha);
    $sql = "UPDATE usuarios SET nome = ?, email = ?, tipo = ?, telefone = ?, cidade = ?, estado = ?, especialidade = ?, senha = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $sucesso = $stmt->execute([
        $dados['nome'],
        $dados['email'],
        $tipo,
        $dados['telefone'],
        $dados['cidade'],
        $dados['estado'],
        $dados['especialidade'],
        $senhaHash,
        $id
    ]);
} else {
    // Atualiza sem alterar a senha
    $sql = "UPDATE usuarios SET nome = ?, email = ?, tipo = ?, telefone = ?, cidade = ?, estado = ?, especialidade = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $sucesso = $stmt->execute([
        $dados['nome'],
        $dados['email'],
        $tipo,
        $dados['telefone'],
        $dados['cidade'],
        $dados['estado'],
        $dados['especialidade'],
        $id
    ]);
}

if ($sucesso) {
    header('Location: listarUsuarios.php?sucesso=edicao');
} else {
    header('Location: editarUsuario.php?id=' . $id . '&erro=edicao');
}
exit;
?>

