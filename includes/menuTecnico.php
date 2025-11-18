<?php
// Detecta se estamos em painel/ ou na raiz
$isInPainel = (strpos($_SERVER['PHP_SELF'], '/painel/') !== false);
$basePath = $isInPainel ? '' : '../';
$painelPrefix = $isInPainel ? '' : 'painel/';
?>
<div class="list-group">
   <a href="<?php echo $basePath . $painelPrefix; ?>painelTecnico.php" 
       class="list-group-item list-group-item-action <?php echo ($paginaAtiva === 'dashboard') ? 'active' : ''; ?>">
        Dashboard
   </a>
   <a href="<?php echo $basePath . $painelPrefix; ?>gerenciarPerfil.php" 
       class="list-group-item list-group-item-action <?php echo ($paginaAtiva === 'perfil') ? 'active' : ''; ?>">
        Gerenciar Perfil
   </a>
   <a href="<?php echo $basePath . $painelPrefix; ?>gerenciarPortfolio.php" 
       class="list-group-item list-group-item-action <?php echo ($paginaAtiva === 'portfolio') ? 'active' : ''; ?>">
        Gerenciar Portfólio
   </a>
   <a href="<?php echo $basePath . $painelPrefix; ?>gerenciarServicos.php" 
      class="list-group-item list-group-item-action <?php echo ($paginaAtiva === 'servicos') ? 'active' : ''; ?>">
      Gerenciar Serviços
   </a>
   <a href="<?php echo $basePath . $painelPrefix; ?>gerenciarAtendimentos.php" 
      class="list-group-item list-group-item-action <?php echo ($paginaAtiva === 'atendimentos') ? 'active' : ''; ?>">
      Solicitações
   </a>
   <a href="<?php echo $basePath . $painelPrefix; ?>gerenciarAvaliacoes.php" 
      class="list-group-item list-group-item-action <?php echo ($paginaAtiva === 'avaliacoes') ? 'active' : ''; ?>">
      Avaliações
   </a>
    <a href="<?php echo $isInPainel ? '../auth/logout.php' : 'auth/logout.php'; ?>" 
       class="list-group-item list-group-item-action text-danger">
       Sair (Logout)
    </a>
</div>