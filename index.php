<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Bibliotech</title>

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <!-- FontAwesome Free for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-pzw1ZT3dW2v9Vfkz0fP1+gQJr+eQh7Q9IuT1dE+VtoEuK5+W9eDtYI1+6r6G6YrQlXBiDJxbPxW1YhaxP+j1yw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    body {
      background-color: rgb(216, 107, 107);
      margin: 0;
      overflow-x: hidden;
      padding-bottom: 80px;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
      display: flex;
      align-items: center;
    }

    .nav-links li a {
      color: white;
      font-weight: bold;
      text-decoration: none;
      display: flex;
      align-items: center;
    }

    .nav-links li a i {
      margin-right: 8px;
      font-size: 1.2rem;
    }

    .nav-links li a:hover {
      color: #ffcc00;
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
      text-align: center;
    }

    .sidebar.hidden ~ .content {
      margin-left: 0;
    }

    .content img {
      width: 60%;
      max-width: 600px;
      filter: drop-shadow(0 8px 16px rgba(0,0,0,0.3)) grayscale(20%);
      border-radius: 8px;
      transition: transform 0.3s ease;
    }

    .content img:hover {
      transform: scale(1.05);
    }

    .tagline {
      margin-top: 1rem;
      font-size: 1.25rem;
      color: #fff;
      text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
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
      .content img {
        width: 80%;
      }
    }
  </style>
</head>
<body>

<?php
session_start();
$mensagem = $_SESSION['mensagem'] ?? null;
$tipo    = $_SESSION['tipo']    ?? null;
unset($_SESSION['mensagem'], $_SESSION['tipo']);
?>

<div class="wrapper">
  <!-- Sidebar -->
  <nav id="sidebar" class="sidebar">
    <div class="sidebar-header"><a href="indexlogado.php" style="color: #fff; text-decoration: none;">Bibliotech</a></div>
    <button class="toggle-btn btn btn-sm btn-warning w-100 mb-2" onclick="hideSidebar()">Recolher ☰</button>
    <ul class="nav-links">
      <li><a href="login.php"><i class="fa-solid fa-right-to-bracket"></i> Realizar Login</a></li>
      <li><a href="cadastroUsuario.php"><i class="fa-solid fa-user-plus"></i> Cadastrar Usuário</a></li>
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

    <!-- Logo Central com efeito -->
    <div class="container mt-5">
      <img src="fxd2.jpg" alt="Logo da Bibliotech" class="img-fluid">
      <div class="tagline">Seu portal para descobrir e gerenciar livros com facilidade.</div>
    </div>
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