<?php
session_start(); 

if (!isset($_SESSION['id'])) {

    header('Location: index.php');
    exit; 
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Relatórios</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>

    body {
      background-color: rgb(214, 218, 255);
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

    .img-logo {
      max-width: 100%;
      height: auto;
      max-height: 240px;
    }

    h2 {
      color: #1c0e3f;
      font-size: 1.5rem;
    }

    .left-align {
      text-align: left;
    }

    .content-section {
      margin-bottom: 30px;
    }

    @media (max-width: 767px) {
      .content-section {
        margin-bottom: 20px;
      }
      .col-md-6 {
        margin-bottom: 20px;
      }
      .img-logo {
        width: 100%;
      }
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
        <a class="nav-link" href="todosEmprestimos.php">| Todos Empréstimos</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="listaEmprestimoAtivo.php">| Empréstimos Ativos</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="emprestimoVence.php">| Empréstimos à Vencer</a>
      </li>  
      <li class="nav-item">
        <a class="nav-link" href="emprestimoVencido.php">| Empréstimos Atrasados</a>
      </li>
    </ul>
  </div>
</nav>

<div class="container mt-5">
  <div class="row">
    <div class="col-md-6 offset-md-3 text-center">
      <img src="fxd.jpg" alt="logo Bibliotech" class="img-logo">
    </div>
  </div>

  <div class="row content-section">
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

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
