<?php
// Componente sidebar para páginas não logadas
?>
<div class="wrapper">
  <!-- Sidebar -->
  <nav id="sidebar" class="sidebar">
    <div class="sidebar-header">
      <a href="index.php">
        <i class="fa-solid fa-book-open-reader"></i>
        <span>Bibliotech</span>
      </a>
    </div>
    <button class="toggle-btn btn btn-sm btn-warning w-100 mb-2" onclick="hideSidebar()" style="font-weight: bold; font-size: 1rem;">
      <i class="fa-solid fa-angles-left mr-2"></i> Recolher Menu
    </button>
    <ul class="nav-links">
      <li><a href="login.php"><i class="fa-solid fa-right-to-bracket"></i> Login</a></li>
      <li><a href="cadastroUsuario.php"><i class="fa-solid fa-user-plus"></i> Cadastrar Usuário</a></li>
    </ul>
  </nav>

  <!-- Botão para mostrar sidebar -->
  <button id="showSidebarBtn" class="show-sidebar-btn" onclick="showSidebar()">☰</button>
