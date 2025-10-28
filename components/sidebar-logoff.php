<?php
// Componente sidebar para páginas não logadas
?>
<style>
  /* Texto colorido apenas ao passar o mouse — mantêm o fundo do item inalterado */
  .nav-links li a.login-link{color:rgba(255,255,255,0.95);transition:color .14s ease, text-shadow .14s}
  .nav-links li a.signup-link{color:rgba(255,255,255,0.95);transition:color .14s ease, text-shadow .14s}

  .nav-links li a.login-link:hover{background:transparent !important}
  .nav-links li a.login-link:hover span.text{color:#0bec61ff !important; text-decoration-thickness:2px}
  .nav-links li a.login-link:hover i{color:#0bec61ff !important}
  .nav-links li a.login-link:hover{box-shadow: 0 6px 18px rgba(0,0,0,0.12); transform: translateY(-2px); border-radius:8px}

  .nav-links li a.signup-link:hover{background:transparent !important}
  .nav-links li a.signup-link:hover span.text{color:#00e5ff !important; text-decoration-thickness:2px}
  .nav-links li a.signup-link:hover i{color:#00e5ff !important}
  .nav-links li a.signup-link:hover{box-shadow: 0 6px 18px rgba(0,0,0,0.12); transform: translateY(-2px); border-radius:8px}

  /* foco por teclado — visível e altera texto e ícone */
  .nav-links li a.login-link:focus span.text,
  .nav-links li a.signup-link:focus span.text{outline: none}
  .nav-links li a.login-link:focus i{color:#0bec61ff}
  .nav-links li a.signup-link:focus i{color:#00e5ff}
</style>
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
      <li><a class="login-link" href="login.php"><i class="fa-solid fa-right-to-bracket"></i> <span class="text">Login</span></a></li>
      <li><a class="signup-link" href="cadastroUsuario.php"><i class="fa-solid fa-user-plus"></i> <span class="text">Cadastrar Usuário</span></a></li>
    </ul>
  </nav>

  <!-- Botão para mostrar sidebar -->
  <button id="showSidebarBtn" class="show-sidebar-btn" onclick="showSidebar()">☰</button>
