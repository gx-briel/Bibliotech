<?php
session_start();
$mensagem = $_SESSION['mensagem'] ?? null;
$tipo    = $_SESSION['tipo']    ?? null;
unset($_SESSION['mensagem'], $_SESSION['tipo']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Bibliotech</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
      background-color: rgb(216, 107, 107);
      margin: 0;
      overflow-x: hidden;
      padding-bottom: 80px;
    }

    .wrapper {
      display: flex;
    }

    .sidebar {
      width: 250px;
      background-color: #1c0e3f;
      color: white;
      min-height: 100vh;
      transition: transform 0.3s ease;
      position: fixed;
      z-index: 999;
    }

    .sidebar.hidden {
      transform: translateX(-100%);
    }

    .sidebar .sidebar-header {
      padding: 1rem;
      font-size: 1.5rem;
      font-weight: bold;
      background-color: #150a2c;
      text-align: center;
    }

    .toggle-btn {
      background: none;
      border: none;
      color: white;
      font-size: 1.1rem;
      padding: 0.5rem 1rem;
      text-align: left;
      width: 100%;
      cursor: pointer;
    }

    .nav-links {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .nav-links li {
      padding: 0.75rem 1rem;
    }

    .nav-links li a {
      color: white;
      font-weight: bold;
      text-decoration: none;
      display: block;
    }

    .nav-links li a:hover {
      color: #ffcc00;
    }

    .logout-btn {
      /* não usado nesta página */
    }

    .show-sidebar-btn {
      position: fixed;
      top: 15px;
      left: 15px;
      z-index: 1000;
      background-color: #1c0e3f;
      color: white;
      border: none;
      border-radius: 4px;
      padding: 8px 12px;
      font-size: 1.2rem;
      display: none;
    }

    .sidebar.hidden ~ .show-sidebar-btn {
      display: block;
    }

    .content {
      margin-left: 250px;
      padding: 2rem;
      flex: 1;
      transition: margin-left 0.3s;
    }

    .sidebar.hidden ~ .content {
      margin-left: 0;
    }

    .mission, .services {
      background: rgba(255,255,255,0.85);
      border-radius: 8px;
      padding: 1.5rem;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    h2 {
      color: #1c0e3f;
    }

    p, li {
      color: #333;
    }

    footer {
      position: fixed;
      bottom: 0;
      width: 100%;
      background-color: #f8f9fa;
      padding: 10px 0;
      text-align: center;
    }

    @media (max-width: 768px) {
      .content {
        margin-left: 0 !important;
      }
    }
  </style>
</head>
<body>

<div class="wrapper">
  <!-- Sidebar -->
  <nav id="sidebar" class="sidebar">
    <div class="sidebar-header">Bibliotech</div>
    <button class="toggle-btn btn btn-sm btn-warning w-100 mb-2" onclick="hideSidebar()">Recolher ☰</button>
    <ul class="nav-links">
      <li><a href="login.php">Realizar Login</a></li>
      <li><a href="cadastroUsuario.php">Cadastrar Usuário</a></li>
    </ul>
  </nav>

  <!-- Botão para mostrar sidebar -->
  <button id="showSidebarBtn" class="show-sidebar-btn" onclick="showSidebar()">☰</button>

  <!-- Conteúdo principal -->
  <div class="content">
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

    <!-- Logo Central -->
    <div class="container mt-5 text-center">
      <img src="fxd.jpg" alt="Logo da Bibliotech" class="img-fluid mb-4" style="max-width: 400px;">
    </div>

    <!-- Missão e Serviços -->
    <div class="container mb-5">
      <div class="row">
        <div class="col-md-6 mb-4">
          <div class="mission">
            <h2>Nossa Missão</h2>
            <p style="text-align: justify;">
              A nossa missão é fornecer um gerenciamento claro e preciso de livros à disposição
              para empréstimos, assim como mostrar clientes inadimplentes com a maior eficiência
              possível para melhor atendê-los.
            </p>
          </div>
        </div>
        <div class="col-md-6 mb-4">
          <div class="services">
            <h2>Nossos Serviços</h2>
            <ul>
              <li>Cadastro de Livros</li>
              <li>Cadastro de Clientes</li>
              <li>Controle de Disponibilidade</li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <footer>
      <span class="text-muted">© 2024 Bibliotech. Todos os direitos reservados.</span>
    </footer>
  </div>
</div>

<!-- Scripts -->
<script>
  function hideSidebar() {
    document.getElementById('sidebar').classList.add('hidden');
    document.getElementById('showSidebarBtn').style.display = 'block';
  }
  function showSidebar() {
    document.getElementById('sidebar').classList.remove('hidden');
    document.getElementById('showSidebarBtn').style.display = 'none';
  }
</script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
