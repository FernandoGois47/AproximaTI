<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'tecnico') {
    header('Location: ../auth/login.php?erro=acesso_negado');
    exit;
}

require_once '../includes/db.php';
require_once '../classes/UsuarioDAO.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $usuarioDAO = new UsuarioDAO($pdo);
    $usuarioId = $_SESSION['usuario_id'];

    // Pega os dados atuais para saber o nome da foto antiga
    $usuarioAtual = $usuarioDAO->buscarPorId($usuarioId);

    $dados = [
        'nome' => $_POST['nome'] ?? '',
        'email' => $_POST['email'] ?? '',
        'telefone' => $_POST['telefone'] ?? null,
        'cidade' => $_POST['cidade'] ?? null,
        'estado' => $_POST['estado'] ?? null,
        'especialidade' => $_POST['especialidade'] ?? ''
    ];

    // Lógica de Upload da Nova Foto
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
        $foto = $_FILES['foto'];
        $uploadDir = '../assets/img/'; // Salvaremos as fotos de perfil na pasta principal de imagens

        $extensao = pathinfo($foto['name'], PATHINFO_EXTENSION);
        $nomeArquivo = 'avatar_' . $usuarioId . '_' . uniqid() . '.' . $extensao;
        $uploadPath = $uploadDir . $nomeArquivo;

        if (move_uploaded_file($foto['tmp_name'], $uploadPath)) {
            // Se o upload deu certo, adiciona o nome do novo arquivo aos dados
            $dados['foto_perfil'] = $nomeArquivo;

            // Apaga a foto antiga, se ela existir e não for a padrão
            if (!empty($usuarioAtual['foto_perfil']) && $usuarioAtual['foto_perfil'] !== 'avatar_default.png') {
                unlink($uploadDir . $usuarioAtual['foto_perfil']);
            }
        }
    }

    $sucesso = $usuarioDAO->atualizar($usuarioId, $dados);

    if ($sucesso) {
        // Atualiza as informações na sessão para refletir as mudanças imediatamente
        $_SESSION['usuario_nome'] = $dados['nome'];
        if (isset($dados['foto_perfil'])) {
            $_SESSION['usuario_foto'] = $dados['foto_perfil'];
        }

        header('Location: ../painel/gerenciarPerfil.php?sucesso=edicao');
        exit;
    } else {
        header('Location: ../painel/gerenciarPerfil.php?erro=edicao');
        exit;
    }

} else {
    header('Location: ../painel/painelTecnico.php');
    exit;
}
?>

