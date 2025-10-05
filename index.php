<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Bibliotech</title>

  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <?php include 'components/sidebar-style.php'; ?>
  <link rel="stylesheet" href="assets/css/theme.css">
  <style>
    .container-hero{ max-width:1100px; margin:2.5rem auto; padding:1rem; }
    .hero-visual{ text-align:center; }
    .visual-card{ background: linear-gradient(135deg, rgba(58,110,165,0.12), rgba(110,193,228,0.08)); padding:1.25rem; border-radius:12px; height:100%; display:flex; align-items:center; justify-content:center; box-shadow: inset 0 1px 0 rgba(255,255,255,0.6); }
    @media (max-width:900px){ .container-hero{ margin:1rem; } }
  </style>
</head>
<body>
  <?php include 'components/sidebar-logoff.php'; ?>
  <main class="container-hero content">
    <script>
      const mensagem = <?php echo json_encode($mensagem); ?>;
      const tipo     = <?php echo json_encode($tipo); ?>;
      if (mensagem) {
        Swal.fire({
          title: mensagem,
          icon: tipo === 'sucesso' ? 'success' : 'error',
          showConfirmButton: false,
          timer: 3000
        });
      }
    </script>

    <section class="hero">
      <div class="hero-card">
        <h1> <i class="fa-solid fa-book-open-reader"></i> Bibliotech</h1>
        <p class="lead">Área destinada à equipe da biblioteca.</p>

        <!-- Botões funcionais: apenas cadastro e login -->
        <div style="display:flex; gap:0.75rem; margin-top:1rem;">
          <a class="btn btn-outline-primary btn-lg btn-cadastrar" href="cadastroUsuario.php" style="flex:1;">Cadastrar usuário</a>
          <a class="btn btn-primary btn-lg btn-entrar" href="login.php" style="flex:1;">Entrar</a>
        </div>

        <!-- Conteúdo puramente decorativo para deixar a landing agradável -->
        <div class="features" style="margin-top:1.25rem;">
          <div class="feature" style="text-align:center;">
            <div style="font-size:1.6rem;color:var(--accent-1)"><i class="fa-solid fa-user-tie"></i></div>
            <div style="font-weight:600;margin-top:.5rem;">Acesso restrito</div>
            <div style="color:var(--muted);font-size:.85rem;margin-top:.25rem;">Somente pessoal autorizado.</div>
          </div>
          <div class="feature" style="text-align:center;">
            <div style="font-size:1.6rem;color:var(--accent-1)"><i class="fa-solid fa-shield-halved"></i></div>
            <div style="font-weight:600;margin-top:.5rem;">Segurança</div>
            <div style="color:var(--muted);font-size:.85rem;margin-top:.25rem;">Controle de acessos e permissões.</div>
          </div>
          <div class="feature" style="text-align:center;">
            <div style="font-size:1.6rem;color:var(--accent-1)"><i class="fa-solid fa-gears"></i></div>
            <div style="font-weight:600;margin-top:.5rem;">Operacional</div>
            <div style="color:var(--muted);font-size:.85rem;margin-top:.25rem;">Ferramentas administrativas para gestão.</div>
          </div>
        </div>
      </div>

      <div class="hero-visual">
        <div class="visual-card">
          <!-- Ilustração decorativa para equipe -->
          <svg width="300" height="200" viewBox="0 0 300 200" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <rect x="6" y="18" width="200" height="140" rx="12" fill="#ffffff" stroke="#d1e8fb" />
            <circle cx="250" cy="60" r="30" fill="#3a6ea5" />
            <path d="M230 140c10-30 40-30 52-20" stroke="#6ec1e4" stroke-width="6" stroke-linecap="round"/>
            <text x="34" y="70" fill="#3a6ea5" font-size="18" font-family="Segoe UI, Tahoma, Geneva, Verdana, sans-serif">Seja bem vindo(a)!</text>
          </svg>
        </div>
      </div>
    </section>

    <footer class="site-footer">&copy; <?php echo date('Y'); ?> Bibliotech — Todos os direitos reservados.</footer>
  </main>
</div>

<script>
  // Salva o estado da sidebar no localStorage
  function hideSidebar() {
    document.getElementById('sidebar').classList.add('hidden');
    document.getElementById('showSidebarBtn').style.display = 'block';
    document.querySelector('.content').classList.add('sidebar-hidden');
    localStorage.setItem('sidebarState', 'hidden');
  }
  
  function showSidebar() {
    document.getElementById('sidebar').classList.remove('hidden');
    document.getElementById('showSidebarBtn').style.display = 'none';
    document.querySelector('.content').classList.remove('sidebar-hidden');
    localStorage.setItem('sidebarState', 'visible');
  }

  // Ao carregar a página, restaura o estado salvo
  window.addEventListener('DOMContentLoaded', function() {
    var sidebarState = localStorage.getItem('sidebarState');
    if (sidebarState === 'hidden') {
      document.getElementById('sidebar').classList.add('hidden');
      document.getElementById('showSidebarBtn').style.display = 'block';
      document.querySelector('.content').classList.add('sidebar-hidden');
    } else {
      document.getElementById('sidebar').classList.remove('hidden');
      document.getElementById('showSidebarBtn').style.display = 'none';
      document.querySelector('.content').classList.remove('sidebar-hidden');
    }
    
    // Clique para abrir a sidebar
    document.getElementById('showSidebarBtn').addEventListener('click', showSidebar);
  });

<?php include 'components/sidebar-script.php'; ?>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


</body>
</html>