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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
  body {
    background-color: rgb(238, 255, 235);
    margin: 0;
    overflow-x: hidden;
  }
  .wrapper {
    display: flex;
  }
  .sidebar {
    width: 250px;
    background: linear-gradient(180deg, #1c0e3f 60%, #e8f5e9 100%);
    color: white;
    min-height: 100vh;
    transition: transform 0.3s ease;
    position: fixed;
    z-index: 999;
    box-shadow: 2px 0 8px rgba(28,14,63,0.08);
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
    letter-spacing: 1px;
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
    transition: color 0.2s;
  }
  .nav-links li a:hover {
    color: #ffcc00;
    text-decoration: underline;
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
  .logout-btn {
    position: absolute;
    bottom: 1rem;
    left: 1rem;
    right: 1rem;
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
    padding: 2rem 0;
    flex: 1;
    transition: margin-left 0.3s;
    min-height: 100vh;
  }
  .sidebar.hidden ~ .content {
    margin-left: 0;
  }
  @media (max-width: 768px) {
    .content {
      margin-left: 0 !important;
    }
    .sidebar {
      width: 200px;
    }
  }
</style>
</head>
<body>

<div class="wrapper">
  <!-- Sidebar -->
<nav id="sidebar" class="sidebar">
<div class="sidebar-header"><a href="indexlogado.php" style="color: #fff; text-decoration: none;"><i class="fa-solid fa-book-open-reader" style="margin-right:8px;"></i><span style="letter-spacing:1px">Bibliotech</span></a></div>   
<button class="toggle-btn btn btn-sm btn-warning w-100 mb-2" onclick="hideSidebar()">← Recolher</button>
    <ul class="nav-links">
      <li><a href="listaCliente.php">Visualizar Clientes</a></li>
      <li><a href="cadastroLivro.php">Cadastrar Livro</a></li>
      <li><a href="acervo.php">Acervo de Livros</a></li>
      <li><a href="criaEmprestimo.php">Criar Empréstimo</a></li>
    </ul>
    <div class="logout-btn">
      <a href="logout.php" class="btn btn-danger w-100"><i class="fa-solid fa-right-from-bracket mr-2"></i> Sair</a>
    </div>
  </nav>
  <button id="showSidebarBtn" class="show-sidebar-btn">☰</button>
  <div class="content">


    <div class="container mt-5">
      <div class="row justify-content-center">
        <div class="col-md-8 col-sm-12">
          <div class="card shadow p-4">
            <h2 class="mb-4 text-center">Cadastro de Clientes</h2>
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
              <button type="submit" class="btn btn-info btn-block">Cadastrar Clientes</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<script>
  function hideSidebar() {
    document.getElementById('sidebar').classList.add('hidden');
    document.getElementById('showSidebarBtn').style.display = 'block';
  }
  function showSidebar() {
    document.getElementById('sidebar').classList.remove('hidden');
    document.getElementById('showSidebarBtn').style.display = 'none';
  }
  document.getElementById('showSidebarBtn').addEventListener('click', showSidebar);

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
