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

  .card-body {
    padding: 2rem;
    animation: fadeIn 0.8s ease-out 0.4s both;
  }

  .form-group {
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
  
  .btn-info {
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

  .btn-info::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
    transition: left 0.5s;
  }

  .btn-info:hover::before {
    left: 100%;
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

  .form-group {
    margin-bottom: 1.5rem;
  }

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

  .btn-info:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(8, 71, 11, 0.4);
  }

  .btn-info:active {
    transform: translateY(0);
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

  /* Modal sem animação */
  #modalSucesso,
  #modalSucesso .modal-dialog,
  #modalSucesso .modal-backdrop {
    transition: none !important;
    animation: none !important;
  }
  
  #modalSucesso .modal-dialog {
    transform: none !important;
  }
  
  .modal-backdrop {
    transition: none !important;
    animation: none !important;
  }

  /* Botões do modal sem efeitos de animação */
  #modalSucesso .btn-info,
  #modalSucesso .btn-secondary {
    opacity: 1 !important;
    animation: none !important;
    transition: background-color 0.15s ease-in-out, border-color 0.15s ease-in-out !important;
    transform: none !important;
    overflow: visible !important;
    position: static !important;
  }

  #modalSucesso .btn-info::before,
  #modalSucesso .btn-secondary::before {
    display: none !important;
  }

  #modalSucesso .btn-info:hover,
  #modalSucesso .btn-secondary:hover {
    transform: none !important;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
  }

  #modalSucesso .btn-info:active,
  #modalSucesso .btn-secondary:active {
    transform: none !important;
  }
</style>
</head>
<body>

<?php include 'components/sidebar-logado.php'; ?>

  <div class="content">
    <div class="container mt-5">
      <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9 col-md-11 col-sm-12">
          <div class="card">
            <div class="card-header">
              <h2>
                <i class="fas fa-user-plus"></i>
                Cadastro de Clientes
              </h2>
            </div>
            <div class="card-body">
              <form method="POST" action="insereCliente.php">
                <div class="row">
                  <!-- Nome Completo - Full Width -->
                  <div class="col-12">
                    <div class="form-group">
                      <label for="nomeCliente">
                        <i class="fas fa-user"></i>
                        Nome do Cliente
                      </label>
                      <input type="text" class="form-control" id="nomeCliente" name="nome" placeholder="Digite o nome completo" required>
                    </div>
                  </div>

                  <!-- CPF e Telefone - 2 colunas no desktop -->
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="cpf">
                        <i class="fas fa-id-card"></i>
                        CPF
                      </label>
                      <input type="text" class="form-control" id="cpf" name="cpf" maxlength="14" oninput="formatarCPF(this)" placeholder="000.000.000-00" required>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="telefone">
                        <i class="fas fa-phone"></i>
                        Telefone
                      </label>
                      <input type="text" class="form-control" id="telefone" name="telefone" oninput="formatarTelefone(this)" maxlength="15" placeholder="(00) 00000-0000" required>
                    </div>
                  </div>

                  <!-- CEP - Full Width -->
                  <div class="col-12">
                    <div class="form-group">
                      <label for="cep">
                        <i class="fas fa-map-marker-alt"></i>
                        CEP
                      </label>
                      <input type="text" class="form-control" id="cep" name="cep" maxlength="9" onblur="buscarCEP()" placeholder="00000-000" required>
                    </div>
                  </div>

                  <!-- Rua e Número - 2 colunas no desktop -->
                  <div class="col-md-8">
                    <div class="form-group">
                      <label for="rua">
                        <i class="fas fa-road"></i>
                        Rua
                      </label>
                      <input type="text" class="form-control" id="rua" name="rua" placeholder="Nome da rua" required>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="numero">
                        <i class="fas fa-home"></i>
                        Número
                      </label>
                      <input type="text" class="form-control" id="numero" name="numero" placeholder="Nº" required>
                    </div>
                  </div>

                  <!-- Bairro e Cidade - 2 colunas no desktop -->
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="bairro">
                        <i class="fas fa-map"></i>
                        Bairro
                      </label>
                      <input type="text" class="form-control" id="bairro" name="bairro" placeholder="Nome do bairro" required>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="cidade">
                        <i class="fas fa-city"></i>
                        Cidade
                      </label>
                      <input type="text" class="form-control" id="cidade" name="cidade" placeholder="Nome da cidade" required>
                    </div>
                  </div>
                  <div class="col-md-2">
                    <div class="form-group">
                      <label for="estado">
                        <i class="fas fa-map-pin"></i>
                        Estado
                      </label>
                      <input type="text" class="form-control" id="estado" name="estado" placeholder="UF" maxlength="2" required>
                    </div>
                  </div>
                </div>

                <div class="row mt-4">
                  <div class="col-12">
                    <button type="submit" class="btn btn-info btn-block">
                      <i class="fas fa-save mr-2"></i>
                      Cadastrar Cliente
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

  <!-- Modal de Confirmação de Sucesso -->
  <div class="modal" id="modalSucesso" tabindex="-1" role="dialog" aria-labelledby="modalSucessoLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);">
        <div class="modal-body text-center p-4">
          <div class="mb-3">
            <i class="fas fa-check-circle" style="font-size: 4rem; color: #28a745;"></i>
          </div>
          <h4 class="modal-title mb-3" id="modalSucessoLabel" style="color: #062c2a; font-weight: 700;">
            Cliente Cadastrado com Sucesso!
          </h4>
          <p class="mb-4" style="color: #666;">
            O cliente foi cadastrado no sistema com sucesso.
          </p>
          <div class="d-flex flex-column flex-sm-row justify-content-center gap-2 gap-sm-3">
            <button type="button" class="btn btn-secondary" onclick="window.location.href='cadastroCliente.php'" style="border-radius: 10px; padding: 0.5rem 1.25rem; font-size: 0.9rem;">
              <i class="fas fa-plus mr-2"></i>
              Cadastrar Outro
            </button>
            <button type="button" class="btn btn-info" onclick="window.location.href='listaCliente.php'" style="border-radius: 10px; padding: 0.5rem 1.25rem; font-size: 0.9rem; background: linear-gradient(135deg, #08470b 0%, #062c2a 100%); border: none; color: white;">
              <i class="fas fa-list mr-2"></i>
              Ver Lista de Clientes
            </button>
          </div>
        </div>
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

<?php include 'components/sidebar-script.php'; ?>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
  // Verificar se deve exibir o modal de sucesso
  <?php if (isset($_GET['sucesso']) && $_GET['sucesso'] == '1'): ?>
    $(document).ready(function() {
      // Mostrar modal sem animação (sem fade)
      $('#modalSucesso').modal({show: true, backdrop: true});
      
      // Limpar parâmetro da URL quando o modal for fechado
      $('#modalSucesso').on('hidden.bs.modal', function () {
        if (window.history.replaceState) {
          window.history.replaceState(null, null, window.location.pathname);
        }
      });
    });
  <?php endif; ?>
</script>

</body>
</html>
