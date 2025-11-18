<?php
// Inicia a sessão para verificar o login
session_start();

// Lógica de Segurança
// 1. Verifica se o usuário NÃO está logado
// 2. Verifica se o usuário logado NÃO é do tipo 'tecnico'
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'tecnico') {
    // Se uma das condições for verdadeira, redireciona para a página de login
    header('Location: ../auth/login.php?erro=acesso_negado');
    exit;
}

// Se o script continuar, significa que o usuário é um técnico logado.
$nomeTecnico = $_SESSION['usuario_nome'];

include '../includes/header.php';
?>

<main class="container my-5">
    <div class="row">
        <div class="col-md-3">
            <?php 
                $paginaAtiva = 'dashboard';
                include '../includes/menuTecnico.php'; 
            ?>
        </div>

        <div class="col-md-9">
            <h1>Dashboard do Técnico</h1>
            <hr>
            <h2 class="h4">Bem-vindo, <?php echo htmlspecialchars($nomeTecnico); ?>!</h2>
            <p>Este é o seu painel de controle. Use o menu ao lado para gerenciar suas informações e serviços.</p>

            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Seu Perfil Público</h5>
                            <p class="card-text">Veja como os clientes estão vendo o seu perfil na plataforma.</p>
                            <a href="#" class="btn btn-primary">Ver Meu Perfil</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Novas Solicitações</h5>
                            <p class="card-text">Você tem <strong>0</strong> novos pedidos de atendimento.</p>
                            <a href="#" class="btn btn-outline-primary">Ver Solicitações</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
include '../includes/footer.php';
?>

