<?php 
require 'conexao.php';
session_start(); 

if (!isset($_SESSION['id'])) {
    header('Location: index.php');
    exit; 
}

$pesquisa = isset($_GET['pesquisa']) ? $_GET['pesquisa'] : (isset($_POST['pesquisa']) ? $_POST['pesquisa'] : '');

// Proteção contra SQL Injection
$pesquisa = mysqli_real_escape_string($conexao, $pesquisa);
// Remover caracteres especiais do CPF para busca (pontos e traços)
$pesquisa_limpa = preg_replace('/[^0-9]/', '', $pesquisa);

// Paginação
$defaultLimit = 50;
$limit = isset($_GET['limit']) && is_numeric($_GET['limit']) && $_GET['limit'] > 0 ? (int)$_GET['limit'] : $defaultLimit;
$offset = 0; // Sempre começa do início para paginação acumulativa

if ($pesquisa != '') {
    // Busca por nome OU CPF (sem pontos e traços)
    // Sempre busca por nome, e se houver números na pesquisa, também busca por CPF
    $where_conditions = array();
    
    // Sempre adiciona busca por nome
    $where_conditions[] = "nomeCliente LIKE '%$pesquisa%'";
    
    // Se a pesquisa contém números, também busca por CPF
    if (strlen($pesquisa_limpa) > 0) {
        $where_conditions[] = "REPLACE(REPLACE(cpf, '.', ''), '-', '') LIKE '%$pesquisa_limpa%'";
    }
    
    $consulta = "SELECT * FROM clientes WHERE (" . implode(" OR ", $where_conditions) . ") AND ID IN (SELECT idCliente FROM emprestimo where ativo = 1 group by idCliente) and removidoEm IS NULL LIMIT $limit OFFSET $offset";
} else {
    $consulta = "SELECT * FROM clientes WHERE ID IN (SELECT idCliente FROM emprestimo where ativo = 1 group by idCliente) and removidoEm IS NULL LIMIT $limit OFFSET $offset";
}

$resultado = mysqli_query($conexao, $consulta);
$numRows = mysqli_num_rows($resultado);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Listagem de Clientes</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

  <?php include 'components/sidebar-style.php'; ?>
  
  <style>
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(15px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Ajuste de largura para colunas de CPF e Telefone */
    .table th.cpf-col, .table td.cpf-col {
      min-width: 140px;
      max-width: 180px;
      white-space: nowrap;
    }
    .table th.tel-col, .table td.tel-col {
      min-width: 130px;
      max-width: 170px;
      white-space: nowrap;
    }
    html, body {
      height: 100%;
    }
    
    body {
      margin: 0;
      overflow-x: hidden;
      padding-bottom: 0;
      font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(to right, #ece9e6, #ffffff);
      min-height: 100vh;
    }

    .page-header-card {
      border: none;
      border-radius: 16px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
      overflow: hidden;
      background: rgba(255, 255, 255, 0.98);
      backdrop-filter: blur(10px);
      margin-bottom: 2rem;
      animation: fadeInUp 0.4s ease-out;
    }

    .page-header-card .card-header {
      background: linear-gradient(135deg, #062c2a 0%, #08470b 100%);
      color: white;
      padding: 1.5rem 2rem;
      border: none;
      position: relative;
    }

    .page-header-card .card-header::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      height: 3px;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    }

    .page-header-card .card-header h2 {
      margin: 0;
      font-size: 1.75rem;
      font-weight: 700;
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    .page-header-card .card-header h2 i {
      font-size: 1.5rem;
    }

    .page-header-card .card-body {
      padding: 1.5rem 2rem;
    }

    .search-form .input-group {
      display: flex;
      width: 100%;
    }

    .search-form .form-control {
      border: 2px solid #e0e0e0;
      border-radius: 10px 0 0 10px;
      border-right: none;
      padding: 0.75rem 1rem;
      font-size: 0.95rem;
      transition: all 0.3s ease;
      flex: 1;
    }

    .search-form .form-control:focus {
      border-color: #08470b;
      box-shadow: none;
      outline: none;
      border-right: none;
      z-index: 1;
    }

    .search-form .form-control:focus + .input-group-append .btn-info {
      border-color: #08470b;
      z-index: 2;
    }

    .search-form .input-group-append {
      display: flex;
      margin: 0;
    }

    .search-form .btn-info {
      background: linear-gradient(135deg, #08470b 0%, #062c2a 100%);
      border: 2px solid #08470b;
      border-left: none;
      border-radius: 0 10px 10px 0;
      padding: 0.75rem 1.5rem;
      font-weight: 600;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(8, 71, 11, 0.3);
      display: flex;
      align-items: center;
      justify-content: center;
      white-space: nowrap;
      height: 70%;
      margin: 0;
    }

    .search-form .btn-info:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(8, 71, 11, 0.4);
      background: linear-gradient(135deg, #062c2a 0%, #08470b 100%);
      border-color: #062c2a;
    }

    .search-form .btn-info:focus {
      border-color: #08470b;
      border-left: none;
      outline: none;
      box-shadow: 0 0 0 0.2rem rgba(8, 71, 11, 0.25);
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
      .content::before {
        left: 0;
        width: 100%;
      }
    }

    .content > * {
      position: relative;
      z-index: 1;
    }

    .content .container {
      position: relative;
      z-index: 1;
    }

    .table-wrapper {
      border: none;
      border-radius: 16px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
      overflow: hidden;
      background: rgba(255, 255, 255, 0.98);
      backdrop-filter: blur(10px);
      animation: fadeInUp 0.4s ease-out 0.15s both;
    }

    .table-container {
      position: relative;
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
      z-index: 1;
    }

    .table-container::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      width: 100%;
      height: 100%;
      background-image: url('bg1.jpg');
      background-position: center;
      background-repeat: no-repeat;
      background-size: cover;
      opacity: 0.05;
      z-index: 0;
      pointer-events: none;
      filter: saturate(0.9) blur(0px);
    }

    .table-container table {
      position: relative;
      z-index: 1;
      background-color: rgba(255, 255, 255, 0.95);
      margin-bottom: 0;
    }

    .sidebar.hidden ~ .content {
      margin-left: 0;
    }
    table {
      width: 100%;
    }
    .table-striped tbody tr:nth-of-type(odd) {
      background-color: rgba(0, 0, 0, 0.05);
    }
    .table th, .table td {
      border: 1px solid #ccc !important;
      vertical-align: middle;
      padding: 12px 15px;
    }
    .table thead th {
      background: linear-gradient(135deg, #062c2a 0%, #08470b 100%);
      color: white;
      font-weight: 600;
      border: none !important;
      padding: 1rem 15px;
    }

    .table tbody td {
      border-color: #e0e0e0 !important;
      padding: 1rem 15px;
    }

    .table-striped tbody tr:nth-of-type(odd) {
      background-color: rgba(6, 44, 42, 0.03);
    }

    .table-striped tbody tr:hover {
      background-color: rgba(8, 71, 11, 0.08);
      transition: background-color 0.2s ease;
    }

    .btn-danger {
      background-color: #dc3545;
      border-color: #dc3545;
      border-radius: 8px;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .btn-primary {
      background: linear-gradient(135deg, #ffcc00 0%, #e0a800 100%);
      border: none;
      border-radius: 8px;
      font-weight: 600;
      color: #062c2a !important;
      transition: all 0.3s ease;
      box-shadow: 0 2px 8px rgba(255, 204, 0, 0.3);
    }

    .btn-danger:hover {
      background-color: #c82333;
      border-color: #bd2130;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4);
    }

    .btn-primary:hover {
      background: linear-gradient(135deg, #e0a800 0%, #ffcc00 100%);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(255, 204, 0, 0.4);
      color: #062c2a !important;
    }

    .btn-sm {
      padding: 0.5rem 1rem;
      font-size: 0.875rem;
    }

    .pagination-buttons {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 1.5rem;
      padding: 1rem;
      background: rgba(255, 255, 255, 0.7);
      border-radius: 10px;
    }

    .btn-show-more, .btn-show-less {
      background: linear-gradient(135deg, #08470b 0%, #062c2a 100%);
      border: none;
      border-radius: 10px;
      padding: 0.75rem 1.5rem;
      font-weight: 600;
      color: white;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(8, 71, 11, 0.3);
    }

    .btn-show-more:hover, .btn-show-less:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(8, 71, 11, 0.4);
      background: linear-gradient(135deg, #062c2a 0%, #08470b 100%);
      color: white;
    }

    .btn-secondary {
      background-color: #6c757d;
      border-color: #6c757d;
      border-radius: 8px;
    }
    .btn-group {
      display: flex;
      justify-content: space-around;
      gap: 10px;
    }
    .modal {
      display: none;
      position: fixed;
      z-index: 1050;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.6);
      backdrop-filter: blur(4px);
    }
    .modal-content {
      background-color: white;
      padding: 0;
      border-radius: 16px;
      position: relative;
      top: 30%;
      left: 50%;
      transform: translateX(-50%);
      width: 90%;
      max-width: 450px;
      box-shadow: 0 12px 40px rgba(0, 0, 0, 0.25);
      border: none;
    }
    .modal-content .modal-header {
      background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
      color: white;
      border: none;
      border-radius: 16px 16px 0 0;
      padding: 1.5rem;
      text-align: center;
    }
    .modal-content .modal-header h4 {
      margin: 0;
      font-size: 1.5rem;
      font-weight: 700;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
    }
    .modal-content .modal-body {
      padding: 2rem;
      text-align: center;
    }
    .modal-content .modal-body .modal-icon {
      font-size: 4rem;
      color: #dc3545;
      margin-bottom: 1rem;
    }
    .modal-content .modal-body p {
      color: #6b6f73;
      font-size: 1.1rem;
      margin-bottom: 0;
    }
    .modal-content .modal-footer {
      display: flex;
      justify-content: center;
      gap: 1rem;
      padding: 1.5rem;
      border-top: 1px solid #e9ecef;
    }
    @media (max-width: 768px) {
      .content {
        margin-left: 0 !important;
        padding: 1rem;
      }
      .table-responsive {
        overflow-x: auto;
      }
    }
  </style>

  <script type="text/javascript">
    function mostrarModal(id) {
      document.getElementById('modal').style.display = 'block';
      document.getElementById('confirmarRemocao').onclick = function() {
        document.getElementById('formRemove' + id).submit();
      };
    }

    function fecharModal() {
      document.getElementById('modal').style.display = 'none';
    }

    function aplicarMascaraCPF(cpf) {
      return cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
    }

    function aplicarMascaraTelefone(telefone) {
      return telefone.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
    }

    function formatarTabela() {
      var cpfs = document.querySelectorAll('.cpf');
      var telefones = document.querySelectorAll('.telefone');

      cpfs.forEach(function(cpf) {
        cpf.textContent = aplicarMascaraCPF(cpf.textContent);
      });

      telefones.forEach(function(telefone) {
        telefone.textContent = aplicarMascaraTelefone(telefone.textContent);
      });
    }

    window.onload = formatarTabela;
  </script>
</head>
<body>


<?php include 'components/sidebar-logado.php'; ?>

  <!-- Conteúdo principal -->
  <div class="content">
    <div class="container mt-5">
      <!-- Header Card -->
      <div class="page-header-card">
        <div class="card-header">
          <h2>
            <i class="fas fa-users"></i>
            Listagem de Clientes Com Empréstimo
          </h2>
        </div>
        <div class="card-body">
          <form method="GET" action="" class="search-form"> 
            <div class="input-group">
              <input type="text" class="form-control" placeholder="Buscar por nome ou CPF..." name="pesquisa" value="<?= htmlspecialchars($pesquisa) ?>">
              <div class="input-group-append">
                <button class="btn btn-info" type="submit">
                  <i class="fas fa-search mr-2"></i>
                  Buscar
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>

      <!-- Table Card -->
      <div class="table-wrapper">
        <div class="table-container table-responsive">
          <table class="table table-striped mb-0">
            <thead>
              <tr>
                <th>ID</th>
                <th>Nome Cliente</th>
                <th class="cpf-col">CPF</th>
                <th class="tel-col">Telefone</th>
                <th>Endereço</th>
                <th>Editar</th>
                <th>Remover</th>
              </tr>
            </thead>
            <tbody>
            <?php
            if ($numRows > 0) {
              while ($clientes = mysqli_fetch_assoc($resultado)) {
            ?>
            <tr>
              <td><?= $clientes['id']; ?></td>
              <td><?= htmlspecialchars($clientes['nomeCliente']); ?></td>
              <td class="cpf-col cpf"><?= $clientes['cpf']; ?></td>
              <td class="tel-col telefone"><?= $clientes['telefone']; ?></td>
              <td>
                <strong>Rua:</strong> <?= htmlspecialchars($clientes['rua']); ?>, Nº <?= htmlspecialchars($clientes['numero']); ?><br>
                <strong>Bairro:</strong> <?= htmlspecialchars($clientes['bairro']); ?><br>
                <strong>Cidade:</strong> <?= htmlspecialchars($clientes['cidade']); ?> - <?= htmlspecialchars($clientes['estado']); ?><br>
                <strong>CEP:</strong> <?= htmlspecialchars($clientes['cep']); ?>
              </td>
              <td>
                <a href="editarCliente.php?id=<?= $clientes['id']; ?>" class="btn btn-primary btn-sm">
                  <i class="fas fa-edit mr-1"></i>
                  Editar
                </a>
              </td>
              <td>
                <form id="formRemove<?= $clientes['id']; ?>" method="POST" action="removeCliente.php" style="display:inline;">
                  <input type="hidden" name="id" value="<?= $clientes['id']; ?>">
                  <button type="button" class="btn btn-danger btn-sm" onclick="mostrarModal(<?= $clientes['id']; ?>)">
                    <i class="fas fa-trash mr-1"></i>
                    Remover
                  </button>
                </form>
              </td>
            </tr>
            <?php
              }
            } else {
              echo "<tr><td colspan='7' class='text-center py-4'><i class='fas fa-inbox mr-2'></i>Nenhum Cliente Cadastrado.</td></tr>";
            }
            ?>
            </tbody>
          </table>
        </div>

        <!-- Pagination Buttons -->
        <div class="pagination-buttons">
          <div></div>
          <div>
            <?php
            // Botão mostrar mais 50
            if ($numRows === $limit) {
              $params = $_GET;
              if ($pesquisa) {
                $params['pesquisa'] = $pesquisa;
              }
              $params['limit'] = $limit + $defaultLimit;
              $url = '?' . http_build_query($params);
              echo '<a href="' . $url . '" class="btn btn-show-more mr-2" id="mostrarMais50">
                      <i class="fas fa-chevron-down mr-2"></i>
                      Mostrar mais 50
                    </a>';
            }
            // Botão mostrar menos
            if ($limit > $defaultLimit) {
              $params = $_GET;
              if ($pesquisa) {
                $params['pesquisa'] = $pesquisa;
              }
              $params['limit'] = $defaultLimit;
              $url = '?' . http_build_query($params);
              echo '<a href="' . $url . '" class="btn btn-show-less" id="mostrarMenos">
                      <i class="fas fa-chevron-up mr-2"></i>
                      Mostrar menos
                    </a>';
            }
            ?>
          </div>
        </div>
      </div>
    </div>
  </div>

<?php include 'components/sidebar-script.php'; ?>

<div id="modal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h4>
        <i class="fas fa-exclamation-triangle"></i>
        Confirmação de Remoção
      </h4>
    </div>
    <div class="modal-body">
      <div class="modal-icon">
        <i class="fas fa-trash-alt"></i>
      </div>
      <p>Tem certeza que deseja remover este cliente?</p>
      <p style="color: #dc3545; font-weight: 600; margin-top: 1rem;">Esta ação não pode ser desfeita.</p>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="fecharModal()">
        <i class="fas fa-times mr-2"></i>Cancelar
      </button>
      <button id="confirmarRemocao" class="btn btn-danger">
        <i class="fas fa-check mr-2"></i>Confirmar Remoção
      </button>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
  $(document).ready(function() {
    // Preservar posição de scroll apenas ao clicar em "mostrar mais/menos"
    $(document).on('click', '#mostrarMais50, #mostrarMenos', function() {
      sessionStorage.setItem('scrollPosCliente', window.scrollY);
      sessionStorage.setItem('shouldRestoreScroll', 'true');
    });
    
    // Restaurar posição apenas se veio de um clique, não de um refresh
    if (sessionStorage.getItem('shouldRestoreScroll') === 'true') {
      var pos = parseInt(sessionStorage.getItem('scrollPosCliente'), 10);
      if (pos > 0) {
        setTimeout(function() {
          window.scrollTo({top: pos, behavior: 'auto'});
          sessionStorage.removeItem('scrollPosCliente');
          sessionStorage.removeItem('shouldRestoreScroll');
        }, 100);
      }
    } else {
      // Limpar qualquer posição salva se for um refresh normal
      sessionStorage.removeItem('scrollPosCliente');
      sessionStorage.removeItem('shouldRestoreScroll');
    }
  });
</script>

</body>
</html>
