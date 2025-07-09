<?php
// Componente JavaScript para controle da sidebar
?>
<script>
  // Salva o estado da sidebar no localStorage
  function hideSidebar() {
    const sidebar = document.getElementById('sidebar');
    const showBtn = document.getElementById('showSidebarBtn');
    
    if (sidebar && showBtn) {
      sidebar.classList.add('hidden');
      showBtn.style.display = 'block';
      localStorage.setItem('sidebarState', 'hidden');
    }
  }

  function showSidebar() {
    const sidebar = document.getElementById('sidebar');
    const showBtn = document.getElementById('showSidebarBtn');
    
    if (sidebar && showBtn) {
      sidebar.classList.remove('hidden');
      showBtn.style.display = 'none';
      localStorage.setItem('sidebarState', 'visible');
    }
  }

  // Ao carregar a página, restaura o estado salvo
  window.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const showBtn = document.getElementById('showSidebarBtn');
    
    if (sidebar && showBtn) {
      var sidebarState = localStorage.getItem('sidebarState');
      if (sidebarState === 'hidden') {
        sidebar.classList.add('hidden');
        showBtn.style.display = 'block';
      } else {
        sidebar.classList.remove('hidden');
        showBtn.style.display = 'none';
      }
      
      // Adiciona evento ao botão de mostrar sidebar
      showBtn.addEventListener('click', showSidebar);
    }
  });
</script>
