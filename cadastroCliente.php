<?php
require 'conexao.php';
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
  <title>Cadastro de Clientes</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    body {
      background-color: rgb(238, 255, 235);
      padding-bottom: 60px;
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
      <li class="nav-item"><a class="nav-link" href="listaCliente.php">Visualizar Clientes</a></li>
      <li class="nav-item"><a class="nav-link" href="cadastroLivro.php">Cadastrar Livro</a></li>
      <li class="nav-item"><a class="nav-link" href="criaEmprestimo.php">Criar Empréstimo</a></li>
    </ul>
  </div>
</nav>

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-8 col-sm-12">
      <h2>Cadastro de Clientes</h2>
      <br><br>
      <form method="POST" action="insereCliente.php">
        <div class="form-group">
          <label for="nomeCliente">Nome do Cliente:</label>
          <input type="text" class="form-control" id="nomeCliente" name="nome" required>
        </div>
        <div class="form-group">
          <label for="cpf">CPF:</label>
          <input type="text" class="form-control" id="cpf" name="cpf" maxlength="14" oninput="formatarCPF(this)" required>
        </div>
        <div class="form-group">
          <label for="cep">CEP:</label>
          <input type="text" class="form-control" id="cep" name="cep" maxlength="9" onblur="buscarCEP()" required>
        </div>
        <div class="form-group">
          <label for="rua">Rua:</label>
          <input type="text" class="form-control" id="rua" name="rua" required>
        </div>
        <div class="form-group">
          <label for="numero">Número:</label>
          <input type="text" class="form-control" id="numero" name="numero" required>
        </div>
        <div class="form-group">
          <label for="bairro">Bairro:</label>
          <input type="text" class="form-control" id="bairro" name="bairro" required>
        </div>
        <div class="form-group">
          <label for="cidade">Cidade:</label>
          <input type="text" class="form-control" id="cidade" name="cidade" required>
        </div>
        <div class="form-group">
          <label for="estado">Estado:</label>
          <input type="text" class="form-control" id="estado" name="estado" required>
        </div>
        <div class="form-group">
          <label for="telefone">Telefone:</label>
          <input type="text" class="form-control" id="telefone" name="telefone" oninput="formatarTelefone(this)" maxlength="15" required>
        </div>
        <button type="submit" class="btn btn-info btn-block">Cadastrar Cliente</button>
      </form>
    </div>
  </div>
</div>

<script>
  function formatarTelefone(input) {
    let telefone = input.value.replace(/\D/g, '');
    if (telefone.length <= 2) {
      input.value = '(' + telefone;
    } else if (telefone.length <= 6) {
      input.value = '(' + telefone.slice(0, 2) + ') ' + telefone.slice(2);
    } else {
      input.value = '(' + telefone.slice(0, 2) + ') ' + telefone.slice(2, 7) + '-' + telefone.slice(7, 11);
    }
    input.setAttribute('data-raw', telefone);
  }

  function formatarCPF(input) {
    let cpf = input.value.replace(/\D/g, '');
    if (cpf.length <= 3) {
      input.value = cpf;
    } else if (cpf.length <= 6) {
      input.value = cpf.slice(0, 3) + '.' + cpf.slice(3);
    } else if (cpf.length <= 9) {
      input.value = cpf.slice(0, 3) + '.' + cpf.slice(3, 6) + '.' + cpf.slice(6);
    } else {
      input.value = cpf.slice(0, 3) + '.' + cpf.slice(3, 6) + '.' + cpf.slice(6, 9) + '-' + cpf.slice(9, 11);
    }
  }

  function buscarCEP() {
    let cep = document.getElementById('cep').value.replace(/\D/g, '');
    
    if (cep.length != 8) {
      alert("CEP inválido!");
      return;
    }

    fetch(`https://viacep.com.br/ws/${cep}/json/`)
      .then(response => response.json())
      .then(data => {
        if (data.erro) {
          alert("CEP não encontrado!");
        } else {
          document.getElementById('rua').value = data.logradouro;
          document.getElementById('bairro').value = data.bairro;
          document.getElementById('cidade').value = data.localidade;
          document.getElementById('estado').value = data.uf;
        }
      })
      .catch(error => console.log('Erro ao buscar CEP:', error));
  }
</script>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
