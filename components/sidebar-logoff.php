<?php
// Componente sidebar para páginas não logadas
?>
<div class="wrapper">
  <!-- Sidebar moderno -->
  <nav id="sidebar" class="sidebar" aria-label="Menu lateral">
    <div class="brand">
      <a href="index.php" class="brand-link">
        <div class="brand-logo" aria-hidden="true">
          <i class="fa-solid fa-book-open-reader"></i>
        </div>
        <div class="brand-text">
          <strong>Bibliotech</strong>
          <small>Página Inicial</small>
        </div>
      </a>
    </div>

    <ul class="nav-links" role="menu">
      <li role="none">Recolher Menu<button class="collapse-btn" aria-label="Recolher menu" onclick="hideSidebar()"> <i class="fa-solid fa-chevron-left"></i></button></li>
      <li role="none"><a role="menuitem" href="login.php"><i class="fa-solid fa-right-to-bracket"></i><span>Entrar</span></a></li>
      <li role="none"><a role="menuitem" href="cadastroUsuario.php"><i class="fa-solid fa-user-plus"></i><span>Cadastrar Usuário</span></a></li>
    </ul>

    <div class="sidebar-footer">
      <a href="index.php" class="home-link"><i class="fa-solid fa-house"></i><span>Início</span></a>
      <button class="logout-btn" onclick="window.location.href='login.php'" title="Login"><i class="fa-solid fa-right-to-bracket"></i></button>
    </div>
  </nav>

  <!-- Botão para mostrar sidebar (aparece quando sidebar escondida) -->
  <button id="showSidebarBtn" class="show-sidebar-btn" aria-label="Mostrar menu" onclick="showSidebar()">☰</button>
