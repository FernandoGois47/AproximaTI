<?php
// Detecta se estamos em painel/ ou na raiz
$isInPainel = (strpos($_SERVER['PHP_SELF'], '/painel/') !== false);
$basePath = $isInPainel ? '' : '../';
$painelPrefix = $isInPainel ? '' : 'painel/';
?>
<div class="list-group">
    <a href="<?php echo $basePath . $painelPrefix; ?>painelCliente.php" 
       class="list-group-item list-group-item-action <?php echo ($paginaAtiva === 'painel') ? 'active' : ''; ?>">
        Meu Painel
    </a>
   <a href="<?php echo $basePath . $painelPrefix; ?>meusPedidos.php" 
      class="list-group-item list-group-item-action <?php echo ($paginaAtiva === 'pedidos') ? 'active' : ''; ?>">
      Meus Pedidos
   </a>
   <a href="<?php echo $basePath . $painelPrefix; ?>gerenciarPerfilCliente.php" 
      class="list-group-item list-group-item-action <?php echo ($paginaAtiva === 'perfil') ? 'active' : ''; ?>">
      Editar Perfil
   </a>
    <a href="<?php echo $isInPainel ? '../auth/logout.php' : 'auth/logout.php'; ?>" 
       class="list-group-item list-group-item-action text-danger">
       Sair (Logout)
    </a>
</div>