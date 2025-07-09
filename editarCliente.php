<?php 
require 'conexao.php';
session_start();

if (!isset($_SESSION['id'])) {
    header('Location: index.php');
    exit;
}

if (!isset($_GET['id'])) {
    echo "ID do cliente não especificado!";
    exit;
}

$idCliente = $_GET['id'];

$query = "SELECT * FROM clientes WHERE id = $idCliente AND removidoEm is null";
$resultado = mysqli_query($conexao, $query);

if (mysqli_num_rows($resultado) == 0) {
    echo "Cliente não encontrado!";
    exit;
}

$cliente = mysqli_fetch_assoc($resultado);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Editar Cliente</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
  
  <?php include 'components/sidebar-style.php'; ?>
  
  <style>
    body {
      background-color: rgb(238, 255, 235);
      margin: 0;
      overflow-x: hidden;
    }
      position: fixed;
      z-index: 999;
      box-shadow: 2px 0 8px rgba(28,14,63,0.08);
    }
    .sidebar.hidden {
      transform: translateX(-100%);
    }
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
      min-height: 100vh;
    }
    .sidebar.hidden ~ .content {
      margin-left: 0;
    }
    @media (max-width: 768px) {
      .content {
        margin-left: 0 !important;
      }
    }
  </style>
</head>
<body>


<?php include 'components/sidebar-logado.php'; ?>

  <!-- Conteúdo principal -->
  <div class="content">

<div class="container mt-5">
  <h2>Editar Cliente</h2>
  <form id="formEditar" action="atualizarCliente.php" method="POST">
    <div class="form-group">
      <label for="nomeCliente">Nome:</label>
      <input type="text" class="form-control" id="nomeCliente" name="nomeCliente" value="<?= $cliente['nomeCliente']; ?>" required>
    </div>
    <div class="form-group">
      <label for="cpf">CPF:</label>
      <input type="text" class="form-control" id="cpf" name="cpf" value="<?= $cliente['cpf']; ?>" maxlength="11" required>
    </div>
    <div class="form-group">
      <label for="telefone">Telefone:</label>
      <input type="text" class="form-control" id="telefone" name="telefone" value="<?= $cliente['telefone']; ?>" maxlength="15" required>
    </div>

    <div class="form-group">
      <label for="cep">CEP:</label>
      <input type="text" class="form-control" id="cep" name="cep" maxlength="9" value="<?= $cliente['cep']; ?>" onblur="buscarCEP()" required>
    </div>
    <div class="form-group">
      <label for="rua">Rua:</label>
      <input type="text" class="form-control" id="rua" name="rua" value="<?= $cliente['rua']; ?>" required>
    </div>
    <div class="form-group">
      <label for="numero">Número:</label>
      <input type="text" class="form-control" id="numero" name="numero" value="<?= $cliente['numero']; ?>" required>
    </div>
    <div class="form-group">
      <label for="bairro">Bairro:</label>
      <input type="text" class="form-control" id="bairro" name="bairro" value="<?= $cliente['bairro']; ?>" required>
    </div>
    <div class="form-group">
      <label for="cidade">Cidade:</label>
      <input type="text" class="form-control" id="cidade" name="cidade" value="<?= $cliente['cidade']; ?>" required>
    </div>
    <div class="form-group">
      <label for="estado">Estado:</label>
      <input type="text" class="form-control" id="estado" name="estado" value="<?= $cliente['estado']; ?>" required>
    </div>

    <input type="hidden" name="id" value="<?= $cliente['id']; ?>">

    <button type="button" class="btn btn-primary" onclick="mostrarModal()">Atualizar</button>
    <a href="listaCliente.php" class="btn btn-secondary">Cancelar</a>
  </form>
</div>

<div id="modalConfirmacao" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirmar Atualização</h5>
        <button type="button" class="close" onclick="fecharModal()">&times;</button>
      </div>
      <div class="modal-body">
        <p>Tem certeza de que deseja atualizar as informações do cliente?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="fecharModal()">Cancelar</button>
        <button type="button" class="btn btn-primary" onclick="confirmarAtualizacao()">Confirmar</button>
      </div>
    </div>
  </div>
</div>



<script>
  function mostrarModal() {
    document.getElementById('modalConfirmacao').style.display = 'block';
  }

  function fecharModal() {
    document.getElementById('modalConfirmacao').style.display = 'none';
  }

  function confirmarAtualizacao() {
    document.getElementById('formEditar').submit();
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

<?php include 'components/sidebar-script.php'; ?>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
