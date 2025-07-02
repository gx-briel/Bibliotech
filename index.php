<?php
session_start();
$mensagem = $_SESSION['mensagem'] ?? null;
$tipo = $_SESSION['tipo'] ?? null;
unset($_SESSION['mensagem'], $_SESSION['tipo']);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Página inicial</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
      background-color: rgb(216, 107, 107);
      padding-bottom: 80px;
    }
    footer {
      position: fixed;
      bottom: 0;
      width: 100%;
      z-index: 1030;
    }
    .navbar {
      background-color: #1c0e3f;
    }
    .navbar-brand, .nav-link {
      color: white !important;
      font-weight: bold;
    }
    .navbar-nav .nav-link:hover {
      color: #ffcc00 !important;
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
  <a class="navbar-brand" href="indexlogado.php">Bibliotech</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
    <span class="navbar-toggler-icon text-white">&#9776;</span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav ml-auto">
       <li class="nav-item">
        <a class="nav-link" href="login.php">Realizar Login</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="cadastroUsuario.php">Cadastrar Usuário</a>
      </li>
    </ul>
  </div>
</nav>

<div class="container mt-5 text-center">
  <img src="fxd.jpg" alt="Logo da Bibliotech" class="img-fluid mb-4" style="max-width: 400px;">
</div>

<div class="container">
  <div class="row">
    <div class="col-md-6 mb-4">
      <h2>Nossa Missão</h2>
      <p style="text-align: justify;">
        A nossa missão é fornecer um gerenciamento claro e preciso de livros à disposição para empréstimos,
        assim como mostrar clientes inadimplentes com a maior eficiência possível para melhor atendê-los.
      </p>
    </div>
    <div class="col-md-6 mb-4">
      <h2>Nossos Serviços</h2>
      <ul>
        <li>Cadastro de Livros</li>
        <li>Cadastro de Clientes</li>
        <li>Controle de Disponibilidade</li>
      </ul>
    </div>
  </div>
</div>

<footer class="footer bg-light mt-5">
  <div class="container text-center py-2">
    <span class="text-muted">© 2024 Bibliotech. Todos os direitos reservados.</span>
  </div>
</footer>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
  const mensagem = <?php echo json_encode($mensagem); ?>;
  const tipo = <?php echo json_encode($tipo); ?>;

  if (mensagem) {
    Swal.fire({
      title: mensagem,
      icon: tipo === 'sucesso' ? 'success' : 'error',
      showConfirmButton: false,
      timer: 3000
    });
  }
</script>

</body>
</html>
