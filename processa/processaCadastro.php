<?php
// Incluo todos os arquivos que vou precisar
require_once '../includes/db.php';
require_once '../classes/UsuarioDAO.php';
require_once '../classes/UserFactory.php'; // A UserFactory é importante aqui

// Verifico se o formulário foi realmente enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Pego os dados do formulário. Os campos obrigatórios primeiro.
    $dados = [
        'nome' => $_POST['nome'] ?? '',
        'email' => $_POST['email'] ?? '',
        'senha' => $_POST['senha'] ?? '',
        'tipo' => $_POST['tipo'] ?? ''
    ];

    // Validação simples: verifico se os campos essenciais não estão vazios
    if (empty($dados['nome']) || empty($dados['email']) || empty($dados['senha']) || empty($dados['tipo'])) {
        // Se algo estiver faltando, mando de volta com um erro.
        // Futuramente, posso criar uma mensagem mais específica.
        header('Location: ../auth/cadastro.php?erro=campos_obrigatorios');
        exit;
    }

    // Adiciono os dados opcionais ao array de dados
    $dados['telefone'] = $_POST['telefone'] ?? null;
    $dados['cidade'] = $_POST['cidade'] ?? null;
    $dados['estado'] = $_POST['estado'] ?? null;
    $dados['especialidade'] = $_POST['especialidade'] ?? null; // Importante para o técnico

    // Crio uma instância do DAO, entregando a conexão do banco
    $usuarioDAO = new UsuarioDAO($pdo);

    // Tento criar o usuário no banco de dados
    $sucesso = $usuarioDAO->criar($dados);

    if ($sucesso) {
        // Se o cadastro deu certo, redireciono para a tela de login com uma mensagem de sucesso
        header('Location: ../auth/login.php?cadastro=sucesso');
        exit;
    } else {
        // Se deu erro (ex: e-mail duplicado), volto para a tela de cadastro com um erro
        header('Location: ../auth/cadastro.php?erro=email_existente');
        exit;
    }

} else {
    // Se alguém tentar acessar o arquivo diretamente, redireciono para o início
    header('Location: ../index.php');
    exit;
}
?>

