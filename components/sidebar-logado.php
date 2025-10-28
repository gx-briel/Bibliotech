<?php
// Componente sidebar para páginas logadas
?>
<div class="wrapper">
  <!-- Sidebar -->
  <nav id="sidebar" class="sidebar">

    <div class="sidebar-header mb-0" style="border-bottom:0;">
      <a href="indexlogado.php">
        <i class="fa-solid fa-book-open-reader"></i>
        <span>Bibliotech</span>
      </a>
    </div>

    <button class="toggle-btn btn btn-sm btn-warning w-100 mb-2 mt-0" onclick="hideSidebar()" style="font-weight: bold; font-size: 1rem;">
      <i class="fa-solid fa-angles-left mr-2"></i> Recolher Menu
    </button>

    <ul class="nav-links">
      <!-- Menu Clientes -->
      <li class="nav-item">
        <a href="#" class="nav-link" onclick="toggleSubmenu('clientes')">
          <i class="fa-solid fa-users"></i> Clientes
          <i class="fa-solid fa-chevron-down submenu-arrow"></i>
        </a>
        <ul class="submenu" id="clientes">
          <li><a href="cadastroCliente.php"><i class="fa-solid fa-user-plus"></i> Cadastrar Clientes</a></li>
          <li><a href="listaCliente.php"><i class="fa-solid fa-users"></i> Listar Clientes</a></li>
        </ul>
      </li>

      <!-- Menu Livros -->
      <li class="nav-item">
        <a href="#" class="nav-link" onclick="toggleSubmenu('livros')">
          <i class="fa-solid fa-book"></i> Livros
          <i class="fa-solid fa-chevron-down submenu-arrow"></i>
        </a>
        <ul class="submenu" id="livros">
          <li><a href="cadastroLivro.php"><i class="fa-solid fa-book-medical"></i> Cadastrar Livros</a></li>
          <li><a href="acervo.php"><i class="fa-solid fa-book"></i> Acervo de Livros</a></li>
        </ul>
      </li>

      <!-- Menu Empréstimos -->
      <li class="nav-item">

        <a href="#" class="nav-link" onclick="toggleSubmenu('emprestimos')">
          <i class="fa-solid fa-arrow-right-arrow-left"></i> Empréstimos
          <i class="fa-solid fa-chevron-down submenu-arrow"></i>
        </a>

        <ul class="submenu" id="emprestimos">
          <li><a href="criaEmprestimo.php"><i class="fa-solid fa-arrow-right-arrow-left"></i> Criar Empréstimo</a></li>
          <li>

            <a href="#" class="nav-link" onclick="toggleSubmenu('relatorios')">
              <i class="fa-solid fa-chart-bar"></i> Relatórios
              <i class="fa-solid fa-chevron-down submenu-arrow"></i>
            </a>
            
            <ul class="submenu" id="relatorios">
              <li><a href="relatorios.php"><i class="fa-solid fa-chart-simple"></i> Dashboard</a></li>
              <li><a href="todosEmprestimos.php"><i class="fa-solid fa-list"></i> Todos os Empréstimos</a></li>
              <li><a href="listaEmprestimoAtivo.php"><i class="fa-solid fa-circle-check"></i> Empréstimos Ativos</a></li>
              <li><a href="emprestimoVence.php"><i class="fa-solid fa-clock"></i> Empréstimos a Vencer</a></li>
              <li><a href="emprestimoVencido.php"><i class="fa-solid fa-exclamation-triangle"></i> Empréstimos Vencidos</a></li>
            </ul>
          </li>
        </ul>

      </li>

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

  <!-- JavaScript para controlar submenus -->
  <script>
    function toggleSubmenu(submenuId) {
      const submenu = document.getElementById(submenuId);
      const arrow = submenu.previousElementSibling.querySelector('.submenu-arrow');
      
      if (submenu.classList.contains('show')) {
        // Fecha o submenu
        submenu.classList.remove('show');
        arrow.classList.remove('fa-chevron-up');
        arrow.classList.add('fa-chevron-down');
        
        // Fecha todos os submenus filhos
        const childSubmenus = submenu.querySelectorAll('.submenu');
        const childArrows = submenu.querySelectorAll('.submenu-arrow');
        
        childSubmenus.forEach(child => child.classList.remove('show'));
        childArrows.forEach(childArrow => {
          childArrow.classList.remove('fa-chevron-up');
          childArrow.classList.add('fa-chevron-down');
        });
      } else {
        // Para menus principais, fecha outros menus do mesmo nível
        if (['clientes', 'livros', 'emprestimos'].includes(submenuId)) {
          const allMainSubmenus = document.querySelectorAll('.nav-links > .nav-item > .submenu');
          const allMainArrows = document.querySelectorAll('.nav-links > .nav-item > .nav-link > .submenu-arrow');
          
          allMainSubmenus.forEach(menu => {
            if (menu.id !== submenuId) {
              menu.classList.remove('show');
              // Fecha submenus filhos
              const childSubmenus = menu.querySelectorAll('.submenu');
              const childArrows = menu.querySelectorAll('.submenu-arrow');
              childSubmenus.forEach(child => child.classList.remove('show'));
              childArrows.forEach(childArrow => {
                childArrow.classList.remove('fa-chevron-up');
                childArrow.classList.add('fa-chevron-down');
              });
            }
          });
          
          allMainArrows.forEach(arr => {
            if (arr.parentElement.getAttribute('onclick') !== `toggleSubmenu('${submenuId}')`) {
              arr.classList.remove('fa-chevron-up');
              arr.classList.add('fa-chevron-down');
            }
          });
        }
        
        // Abre o submenu clicado
        submenu.classList.add('show');
        arrow.classList.remove('fa-chevron-down');
        arrow.classList.add('fa-chevron-up');
      }
    }

    // Impede que o clique no link do menu principal redirecione
    document.addEventListener('DOMContentLoaded', function() {
      const navLinks = document.querySelectorAll('a[onclick^="toggleSubmenu"]');
      navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
          e.preventDefault();
        });
      });
    });
  </script>
