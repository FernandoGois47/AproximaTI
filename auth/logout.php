<?php
session_start();
session_destroy(); // Destrói todas as informações da sessão

// Detecta de onde veio o logout para redirecionar corretamente
$referer = $_SERVER['HTTP_REFERER'] ?? '';

// Sempre redireciona para login (o caminho será ajustado automaticamente)
if (strpos($_SERVER['PHP_SELF'], '/auth/') !== false) {
    // Se o logout.php está em auth/, redireciona para login na mesma pasta
    header('Location: login.php?logout=sucesso');
} else {
    // Se está em outro lugar, redireciona para auth/login
    header('Location: auth/login.php?logout=sucesso');
}
exit;
?>

