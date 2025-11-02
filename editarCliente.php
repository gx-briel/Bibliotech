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
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <?php include 'components/sidebar-style.php'; ?>
  
  <style>
    html, body {
      height: 100%;
    }
    
    body {
      background: linear-gradient(to right, #ece9e6, #ffffff);
      margin: 0;
      overflow-x: hidden;
      font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      min-height: 100vh;
    }

    .content {
      margin-left: 250px;
      padding: 2rem;
      padding-bottom: 4rem;
      transition: margin-left 0.3s;
      position: relative;
      min-height: 100vh;
      background: transparent;
    }

    .content::before {
      content: '';
      position: fixed;
      top: 0;
      left: 250px;
      right: 0;
      bottom: 0;
      width: calc(100% - 250px);
      height: 100vh;
      background-image: url('bg1.jpg');
      background-position: center;
      background-repeat: no-repeat;
      background-size: cover;
      opacity: 0.15;
      z-index: 0;
      pointer-events: none;
      filter: saturate(0.9) blur(0px);
      transition: left 0.3s ease, width 0.3s ease;
    }

    .sidebar.hidden ~ .content::before {
      left: 0;
      width: 100%;
    }

    @media (max-width: 768px) {
      .content {
        margin-left: 0 !important;
        padding: 1rem;
      }
      .content::before {
        left: 0;
        width: 100%;
      }
    }

    .content > * {
      position: relative;
      z-index: 1;
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
      }
      to {
        opacity: 1;
      }
    }

    .card {
      border: none;
      border-radius: 16px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
      overflow: hidden;
      background: rgba(255, 255, 255, 0.98);
      backdrop-filter: blur(10px);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      animation: fadeInUp 0.6s ease-out;
    }

    .card:hover {
      transform: translateY(-4px);
      box-shadow: 0 12px 40px rgba(0, 0, 0, 0.18);
    }

    .card-header {
      background: linear-gradient(135deg, #062c2a 0%, #08470b 100%);
      color: white;
      padding: 1.5rem 2rem;
      border: none;
      position: relative;
      animation: fadeIn 0.8s ease-out 0.2s both;
    }

    .card-header::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      height: 3px;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    }

    .card-header h2 {
      margin: 0;
      font-size: 1.75rem;
      font-weight: 700;
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    .card-header h2 i {
      font-size: 1.5rem;
    }

    .card-body {
      padding: 2rem;
      animation: fadeIn 0.8s ease-out 0.4s both;
    }

    .form-group {
      margin-bottom: 1.5rem;
      opacity: 0;
      animation: fadeInUp 0.5s ease-out forwards;
    }

    .row > div:nth-of-type(1) .form-group { animation-delay: 0.5s; }
    .row > div:nth-of-type(2) .form-group { animation-delay: 0.6s; }
    .row > div:nth-of-type(3) .form-group { animation-delay: 0.7s; }
    .row > div:nth-of-type(4) .form-group { animation-delay: 0.8s; }
    .row > div:nth-of-type(5) .form-group { animation-delay: 0.9s; }
    .row > div:nth-of-type(6) .form-group { animation-delay: 1.0s; }
    .row > div:nth-of-type(7) .form-group { animation-delay: 1.1s; }
    .row > div:nth-of-type(8) .form-group { animation-delay: 1.2s; }
    .row > div:nth-of-type(9) .form-group { animation-delay: 1.3s; }

    .form-group label {
      font-weight: 600;
      color: #062c2a;
      margin-bottom: 0.5rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      font-size: 0.95rem;
    }

    .form-group label i {
      color: #08470b;
      font-size: 0.9rem;
    }

    .form-control {
      border: 2px solid #e0e0e0;
      border-radius: 10px;
      padding: 0.75rem 1rem;
      font-size: 0.95rem;
      transition: all 0.3s ease;
      background: white;
    }

    .form-control:focus {
      border-color: #08470b;
      box-shadow: 0 0 0 0.2rem rgba(8, 71, 11, 0.15);
      outline: none;
    }

    .btn-primary {
      background: linear-gradient(135deg, #08470b 0%, #062c2a 100%);
      border: none;
      border-radius: 10px;
      padding: 0.875rem 2rem;
      font-weight: 600;
      font-size: 1rem;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(8, 71, 11, 0.3);
      opacity: 0;
      animation: fadeInUp 0.5s ease-out 1.4s forwards;
      color: white;
      position: relative;
      overflow: hidden;
    }

    .btn-primary::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
      transition: left 0.5s;
    }

    .btn-primary:hover::before {
      left: 100%;
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(8, 71, 11, 0.4);
    }

    .btn-primary:active {
      transform: translateY(0);
    }

    .btn-secondary {
      background: linear-gradient(135deg, #b8860b 0%, #8b6914 100%);
      border: none;
      border-radius: 10px;
      padding: 0.875rem 2rem;
      font-weight: 600;
      font-size: 1rem;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(184, 134, 11, 0.3);
      opacity: 0;
      animation: fadeInUp 0.5s ease-out 1.4s forwards;
      color: white;
      position: relative;
      overflow: hidden;
    }

    .btn-secondary::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
      transition: left 0.5s;
    }

    .btn-secondary:hover::before {
      left: 100%;
    }

    .btn-secondary:hover {
      background: linear-gradient(135deg, #8b6914 0%, #b8860b 100%);
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(184, 134, 11, 0.4);
      color: white;
    }

    /* Modal estilizado */
    .modal {
      z-index: 1060 !important;
    }

    .modal-backdrop {
      z-index: 1050 !important;
      background-color: rgba(0, 0, 0, 0.6);
      backdrop-filter: blur(4px);
    }

    .modal-content {
      border-radius: 16px;
      border: none;
      box-shadow: 0 12px 40px rgba(0, 0, 0, 0.25);
      overflow: hidden;
      animation: none !important;
    }

    .modal-header {
      background: linear-gradient(135deg, #062c2a 0%, #08470b 100%);
      color: white;
      border-radius: 16px 16px 0 0;
      border: none;
      padding: 1.5rem;
      animation: none !important;
    }

    .modal-header .close {
      color: white;
      opacity: 0.9;
      text-shadow: none;
      font-size: 1.5rem;
    }

    .modal-header .close:hover {
      opacity: 1;
      color: white;
    }

    .modal-header .modal-title {
      font-weight: 700;
      font-size: 1.25rem;
    }

    .modal-body {
      padding: 1.5rem;
      color: #6b6f73;
      animation: none !important;
    }

    .modal-footer {
      padding: 1rem 1.5rem;
      border-top: 1px solid #e0e0e0;
      display: flex;
      justify-content: flex-end;
      gap: 0.75rem;
      animation: none !important;
    }

    .modal-footer .btn,
    .modal .btn-primary,
    .modal .btn-secondary {
      animation: none !important;
      opacity: 1 !important;
      transform: none !important;
    }

    .modal .btn-primary:hover,
    .modal .btn-secondary:hover {
      transform: translateY(-2px) !important;
    }

    @media (max-width: 768px) {
      .card-body {
        padding: 1.5rem;
      }
      
      .card-header {
        padding: 1.25rem 1.5rem;
      }
      
      .card-header h2 {
        font-size: 1.5rem;
      }
    }
  </style>
</head>
<body>


<?php include 'components/sidebar-logado.php'; ?>

  <!-- Conteúdo principal -->
  <div class="content">
    <div class="container mt-5">
      <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9 col-md-11 col-sm-12">
          <div class="card">
            <div class="card-header">
              <h2>
                <i class="fas fa-user-edit"></i>
                Editar Cliente
              </h2>
            </div>
            <div class="card-body">
              <form id="formEditar" action="atualizarCliente.php" method="POST">
                <div class="row">
                  <!-- Nome Completo - Full Width -->
                  <div class="col-12">
                    <div class="form-group">
                      <label for="nomeCliente">
                        <i class="fas fa-user"></i>
                        Nome do Cliente
                      </label>
                      <input type="text" class="form-control" id="nomeCliente" name="nomeCliente" value="<?= htmlspecialchars($cliente['nomeCliente']); ?>" placeholder="Digite o nome completo" required>
                    </div>
                  </div>

                  <!-- CPF e Telefone - 2 colunas no desktop -->
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="cpf">
                        <i class="fas fa-id-card"></i>
                        CPF
                      </label>
                      <input type="text" class="form-control" id="cpf" name="cpf" value="<?= htmlspecialchars($cliente['cpf']); ?>" maxlength="14" oninput="formatarCPF(this)" placeholder="000.000.000-00" required>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="telefone">
                        <i class="fas fa-phone"></i>
                        Telefone
                      </label>
                      <input type="text" class="form-control" id="telefone" name="telefone" value="<?= htmlspecialchars($cliente['telefone']); ?>" oninput="formatarTelefone(this)" maxlength="15" placeholder="(00) 00000-0000" required>
                    </div>
                  </div>

                  <!-- CEP - Full Width -->
                  <div class="col-12">
                    <div class="form-group">
                      <label for="cep">
                        <i class="fas fa-map-marker-alt"></i>
                        CEP
                      </label>
                      <input type="text" class="form-control" id="cep" name="cep" maxlength="9" value="<?= htmlspecialchars($cliente['cep']); ?>" onblur="buscarCEP()" placeholder="00000-000" required>
                    </div>
                  </div>

                  <!-- Rua e Número - 2 colunas no desktop -->
                  <div class="col-md-8">
                    <div class="form-group">
                      <label for="rua">
                        <i class="fas fa-road"></i>
                        Rua
                      </label>
                      <input type="text" class="form-control" id="rua" name="rua" value="<?= htmlspecialchars($cliente['rua']); ?>" placeholder="Nome da rua" required>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="numero">
                        <i class="fas fa-home"></i>
                        Número
                      </label>
                      <input type="text" class="form-control" id="numero" name="numero" value="<?= htmlspecialchars($cliente['numero']); ?>" placeholder="Nº" required>
                    </div>
                  </div>

                  <!-- Bairro e Cidade - 2 colunas no desktop -->
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="bairro">
                        <i class="fas fa-map"></i>
                        Bairro
                      </label>
                      <input type="text" class="form-control" id="bairro" name="bairro" value="<?= htmlspecialchars($cliente['bairro']); ?>" placeholder="Nome do bairro" required>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="cidade">
                        <i class="fas fa-city"></i>
                        Cidade
                      </label>
                      <input type="text" class="form-control" id="cidade" name="cidade" value="<?= htmlspecialchars($cliente['cidade']); ?>" placeholder="Nome da cidade" required>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group">
                      <label for="estado">
                        <i class="fas fa-map-pin"></i>
                        Estado
                      </label>
                      <input type="text" class="form-control" id="estado" name="estado" value="<?= htmlspecialchars($cliente['estado']); ?>" placeholder="UF" maxlength="2" required>
                    </div>
                  </div>
                </div>

                <input type="hidden" name="id" value="<?= $cliente['id']; ?>">

                <div class="row mt-4">
                  <div class="col-12 d-flex justify-content-end" style="gap: 1rem;">
                    <a href="listaCliente.php" class="btn btn-secondary">
                      <i class="fas fa-times mr-2"></i>
                      Cancelar
                    </a>
                    <button type="button" class="btn btn-primary" onclick="mostrarModal()">
                      <i class="fas fa-save mr-2"></i>
                      Atualizar
                    </button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

<div id="modalConfirmacao" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalConfirmacaoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalConfirmacaoLabel">
          <i class="fas fa-exclamation-circle mr-2"></i>
          Confirmar Atualização
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Tem certeza de que deseja atualizar as informações do cliente <strong>"<?= htmlspecialchars($cliente['nomeCliente']); ?>"</strong>?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          <i class="fas fa-times mr-1"></i>
          Cancelar
        </button>
        <button type="button" class="btn btn-primary" onclick="confirmarAtualizacao()">
          <i class="fas fa-check mr-1"></i>
          Confirmar
        </button>
      </div>
    </div>
  </div>
</div>



<script>
  function mostrarModal() {
    $('#modalConfirmacao').modal('show');
  }

  function confirmarAtualizacao() {
    document.getElementById('formEditar').submit();
  }

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
          document.getElementById('rua').value = data.logradouro || '';
          document.getElementById('bairro').value = data.bairro || '';
          document.getElementById('cidade').value = data.localidade || '';
          document.getElementById('estado').value = data.uf || '';
        }
      })
      .catch(error => console.log('Erro ao buscar CEP:', error));
  }

  // Format campos ao carregar página
  $(document).ready(function() {
    // Formatar CPF e telefone se já tiver valores
    var cpf = document.getElementById('cpf');
    if (cpf.value) {
      formatarCPF(cpf);
    }
    var telefone = document.getElementById('telefone');
    if (telefone.value) {
      formatarTelefone(telefone);
    }
  });
</script>

<?php include 'components/sidebar-script.php'; ?>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
