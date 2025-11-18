<?php
// Inicia a sessão para que possamos usar a superglobal $_SESSION.
session_start();

require_once '../includes/db.php';
require_once '../classes/UsuarioDAO.php';

if (!isset($pdo)) {
    die("Falha crítica: A conexão com o banco de dados não foi estabelecida.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = $_POST['email'] ?? null;
    $senha = $_POST['senha'] ?? null;

    if (empty($email) || empty($senha)) {
        header('Location: ../auth/login.php?erro=campos_vazios');
        exit;
    }

    $usuarioDAO = new UsuarioDAO($pdo);
    $usuario = $usuarioDAO->verificarLogin($email, $senha);

    if ($usuario) {
        // Se o login for válido, armazena os dados do usuário na sessão.
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['usuario_tipo'] = $usuario['tipo'];
        $_SESSION['usuario_foto'] = $usuario['foto_perfil'];

        // Lógica de redirecionamento inteligente
        if ($usuario['tipo'] === 'tecnico') {
            header('Location: ../painel/painelTecnico.php');
        } elseif ($usuario['tipo'] === 'cliente') {
            header('Location: ../painel/painelCliente.php');
        } elseif ($usuario['tipo'] === 'admin') {
            // Admin redireciona para área administrativa
            header('Location: ../adm/index.php');
        } else {
            // Para outros tipos, vai para a home
            header('Location: ../index.php?login=sucesso');
        }
        exit;

    } else {
        // Se o login falhar, redireciona de volta com um erro.
        header('Location: ../auth/login.php?erro=login_invalido');
        exit;
    }

} else {
    // Redireciona se o acesso ao script for direto.
    header('Location: ../index.php');
    exit;
}
?>

