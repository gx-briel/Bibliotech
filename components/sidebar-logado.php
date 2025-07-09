<?php
// Componente sidebar para páginas logadas
?>
<div class="wrapper">
  <!-- Sidebar -->
  <nav id="sidebar" class="sidebar">
    <div class="sidebar-header">
      <a href="indexlogado.php">
        <i class="fa-solid fa-book-open-reader"></i>
        <span>Bibliotech</span>
      </a>
    </div>
    <button class="toggle-btn btn btn-sm btn-warning w-100 mb-2" onclick="hideSidebar()" style="font-weight: bold; font-size: 1rem;">
      <i class="fa-solid fa-angles-left mr-2"></i> Recolher Menu
    </button>
    <ul class="nav-links">
      <li><a href="cadastroCliente.php"><i class="fa-solid fa-user-plus"></i> Cadastrar Clientes</a></li>
      <li><a href="listaCliente.php"><i class="fa-solid fa-users"></i> Listar Clientes</a></li>
      <li><a href="cadastroLivro.php"><i class="fa-solid fa-book-medical"></i> Cadastrar Livro</a></li>
      <li><a href="acervo.php"><i class="fa-solid fa-book"></i> Acervo de Livros</a></li>
      <li><a href="criaEmprestimo.php"><i class="fa-solid fa-arrow-right-arrow-left"></i> Criar Empréstimo</a></li>
      <li><a href="relatorios.php"><i class="fa-solid fa-chart-bar"></i> Relatórios</a></li>
    </ul>

    <!-- Botão de logout no rodapé -->
    <div class="logout-btn">
      <a href="logout.php" class="btn btn-danger w-100" style="font-size: 1rem;">
        <i class="fa-solid fa-right-from-bracket mr-2"></i> Sair
      </a>
    </div>
  </nav>

  <!-- Botão para mostrar sidebar -->
  <button id="showSidebarBtn" class="show-sidebar-btn" onclick="showSidebar()">☰</button>
