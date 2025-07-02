<?php
session_start(); // Inicia a sessão

// Verifica se o usuário está logado
if (!isset($_SESSION['id'])) {
    // Se não estiver logado, redireciona para a página de login
    header('Location: index.php');
    exit; // Encerra o script para garantir que o redirecionamento aconteça
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Página inicial</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    footer {
      position: fixed;
      bottom: 0;
      width: 100%;
      z-index: 1030;
    }

    body {
      background-color: rgb(238, 255, 235);
    }

    .container {
      padding-left: 15px;
      padding-right: 15px;
    }

    @media (max-width: 768px) {
      .col-md-6 {
        text-align: center;
      }

      .footer {
        padding: 20px 0;
      }

      .footer .container {
        padding-left: 15px;
        padding-right: 15px;
      }

      .footer span {
        font-size: 0.9rem;
      }
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
      <li class="nav-item"><a class="nav-link" href="cadastroCliente.php">Cadastrar Cliente</a></li>
      <li class="nav-item"><a class="nav-link" href="cadastroLivro.php">Cadastrar Livro</a></li>
      <li class="nav-item"><a class="nav-link" href="acervo.php">Acervo de Livros</a></li>
      <li class="nav-item"><a class="nav-link" href="listaCliente.php">Lista Clientes</a></li>
      <li class="nav-item"><a class="nav-link" href="criaEmprestimo.php">Criar Empréstimo</a></li>
      <li class="nav-item"><a class="nav-link" href="relatorios.php">Empréstimos</a></li>
    </ul>
  </div>
</nav>

<div class="container mt-5">
  <div class="row">
    <div class="col-md-6 offset-md-3 text-center">
      <img src="fxd.jpg" alt="logo Bibliotech" class="img-fluid" style="max-width: 400px; height: auto;">
    </div>
  </div>
  
  <div class="row mt-4">
    <div class="col-md-6">
      <h2>Nossa Missão</h2>
      <p style="text-align: justify;">A nossa missão é fornecer um gerenciamento claro e preciso de livros à disposição para empréstimos, assim como mostrar clientes inadimplentes com maior eficiência possível para lhes bem atender.</p>
    </div>
    <div class="col-md-6">
      <h2>Nossos Serviços</h2>
      <ul>
        <li>Cadastro de Livros</li>
        <li>Cadastro de Clientes</li>
        <li>Controle de Disponibilidade</li>
      </ul>
    </div>
  </div>
</div>

    <div class="col-md-12 mt-4 mb-4">
      <!-- Botão de Logout -->
      <a href="logout.php">
        <button type="button" class="btn btn-danger btn-block">SAIR</button>
      </a>
    </div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
