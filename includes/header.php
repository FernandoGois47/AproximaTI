<?php
// Se a sessão ainda não foi iniciada em uma página, iniciamos aqui.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Função para detectar o nível de profundidade e retornar o caminho base correto
function getBasePath() {
    $scriptPath = $_SERVER['PHP_SELF'];
    // Remove o nome do arquivo e pega o diretório
    $path = dirname($scriptPath);
    
    // Normaliza o caminho
    $path = str_replace('\\', '/', $path);
    $path = trim($path, '/');
    
    // Remove o nome do projeto do caminho para contar apenas subpastas
    // Exemplo: /AproximaTI/auth -> auth (1 nível)
    // Exemplo: /AproximaTI/painel -> painel (1 nível)  
    // Exemplo: /AproximaTI -> '' (raiz)
    $path = preg_replace('#^/?AproximaTI/?#', '', $path);
    $path = trim($path, '/');
    
    // Se está na raiz
    if (empty($path)) {
        return '';
    }
    
    // Conta quantos níveis de profundidade temos
    $parts = array_filter(explode('/', $path));
    $depth = count($parts);
    
    // Retorna o caminho relativo para voltar à raiz
    return str_repeat('../', $depth);
}

$basePath = getBasePath();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AproximaTI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/css/style.css"> 
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg bg-body-tertiary shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="<?php echo $basePath; ?>index.php">
                    <img src="<?php echo $basePath; ?>assets/img/logo_aproximati_comprido.png" alt="Logo AproximaTI" style="height: 40px;">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto align-items-center">
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $basePath; ?>index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $basePath; ?>sobre.php">Sobre</a>
                        </li>
                        
                        <li class="nav-item mx-lg-2">
                            <a class="btn btn-primary" href="<?php echo $basePath; ?>servico1.php">Encontrar Técnicos</a>
                        </li>

                        <?php if (isset($_SESSION['usuario_id'])): ?>
                            <?php
                                $isInPainel = (strpos($_SERVER['PHP_SELF'], '/painel/') !== false);
                                $avatarPath = $basePath . 'assets/img/';
                                
                                $painelUrl = ($_SESSION['usuario_tipo'] === 'tecnico') 
                                    ? ($isInPainel ? 'painelTecnico.php' : $basePath . 'painel/painelTecnico.php')
                                    : ($isInPainel ? 'painelCliente.php' : $basePath . 'painel/painelCliente.php');
                                $avatarUrl = $avatarPath . ($_SESSION['usuario_foto'] ?? 'avatar_default.png');
                                $logoutUrl = $basePath . 'auth/logout.php';
                            ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <img src="<?php echo $avatarUrl; ?>" alt="Avatar" class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <li><a class="dropdown-item" href="<?php echo $painelUrl; ?>">Meu Painel</a></li>
                                    <li><span class="dropdown-item-text"><small>Olá, <?php echo strtok($_SESSION['usuario_nome'], ' '); ?></small></span></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="<?php echo $logoutUrl; ?>">Sair (Logout)</a></li>
                                </ul>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="btn btn-warning" href="<?php echo $basePath; ?>auth/cadastro.php">Seja um Técnico</a>
                            </li>
                            <li class="nav-item ms-lg-2">
                                <a class="nav-link" href="<?php echo $basePath; ?>auth/login.php">Login</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>