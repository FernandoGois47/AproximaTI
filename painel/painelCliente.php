<?php
// Inicia a sessão para verificar o login
session_start();

// Lógica de Segurança
// 1. Verifica se o usuário NÃO está logado
// 2. Verifica se o usuário logado NÃO é do tipo 'cliente'
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'cliente') {
    // Se uma das condições for verdadeira, redireciona para a página de login
    header('Location: ../auth/login.php?erro=acesso_negado');
    exit;
}

// Se o script continuar, significa que o usuário é um cliente logado.
$nomeCliente = $_SESSION['usuario_nome'];

include '../includes/header.php';
?>

<main class="container my-5">
    <div class="row">
        <div class="col-md-3">
            <?php 
                $paginaAtiva = 'painel';
                include '../includes/menuCliente.php'; 
            ?>
        </div>

        <div class="col-md-9">
            <h1>Painel do Cliente</h1>
            <hr>
            <h2 class="h4">Bem-vindo, <?php echo htmlspecialchars($nomeCliente); ?>!</h2>
            <p>Aqui você poderá gerenciar seus pedidos de serviço e acompanhar o andamento dos seus atendimentos.</p>

            <div class="card mt-4">
                <div class="card-header">
                    Resumo Rápido
                </div>
                <div class="card-body">
                    <p>Você tem <strong>0</strong> atendimentos em andamento.</p>
                    <a href="../servico1.php" class="btn btn-primary">Encontrar um Novo Técnico</a>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
include '../includes/footer.php';
?>

