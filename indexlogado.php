<?php
session_start();
if (!isset($_SESSION['id'])) {
    header('Location: index.php');
    exit;
}

require_once 'conexao.php';

$sql = "SELECT COUNT(*) AS totalLivros FROM livros";
$res = mysqli_query($conexao, $sql);
$row = mysqli_fetch_assoc($res);
$totalLivros = $row['totalLivros'] ?? 0;

$sqlemprestimo = "SELECT COUNT(*) AS totalEmprestimos from emprestimo where ativo = '1'";
$res2 = mysqli_query($conexao, $sqlemprestimo);
$row2 = mysqli_fetch_assoc($res2);
$totalEmp = $row2['totalEmprestimos'] ?? 0;

$sqlvencido = "SELECT COUNT(*) AS totalVencido from emprestimo where vencimento < curdate() AND ativo = '1'";
$res3 = mysqli_query($conexao, $sqlvencido);
$row3 = mysqli_fetch_assoc($res3);
$totalVenc = $row3['totalVencido'] ?? 0;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard - Bibliotech</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
  
  <?php include 'components/sidebar-style.php'; ?>
  
  <style>
    body {
      background-color: rgb(238, 255, 235);
      margin: 0;
      overflow-x: hidden;
    }
    
    .nav-links li a:hover {
      color: #0bec61ff;
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

    .card {
      border: none;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }

    /* Estilo harmonizado para a imagem */
    .img-harmonizada {
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      padding: 1rem;
      background-color: #fff;
      max-width: 900px;
      width: 100%;
      transition: transform 0.3s ease;
    }

    /* Ajuste responsivo para a imagem não ficar muito para baixo em mobile */
    .img-container-mobile-fix {
      min-height: 70vh;
    }
    @media (max-width: 768px) {
      .img-container-mobile-fix {
        min-height: 0;
        margin-top: 1.5rem;
        margin-bottom: 2rem;
        align-items: flex-start !important;
      }
    }
    }
    
  </style>
</head>
<body>

<?php include 'components/sidebar-logado.php'; ?>

  <div class="content">
    <div class="container-fluid">
      <div class="row text-center">
        <div class="col-md-4 mb-4">
          <div class="card bg-primary text-white" style="cursor: pointer;" onclick="window.location.href='acervo.php'">
            <div class="card-body">
              <h5 class="card-title">Total de Livros</h5>
              <p class="card-text display-4"><?= $totalLivros; ?></p>
            </div>
          </div>
        </div>
        <div class="col-md-4 mb-4">
          <div class="card bg-success text-white" style="cursor: pointer;" onclick="window.location.href='listaEmprestimoAtivo.php'">
            <div class="card-body">
              <h5 class="card-title">Empréstimos Ativos</h5>
              <p class="card-text display-4"><?= $totalEmp; ?></p>
            </div>
          </div>
        </div>
        <div class="col-md-4 mb-4">
          <div class="card bg-danger text-white" style="cursor: pointer;" onclick="window.location.href='emprestimoVencido.php'">
            <div class="card-body">
              <h5 class="card-title">Vencidos</h5>
              <p class="card-text display-4"><?= $totalVenc; ?></p>
            </div>
          </div>
        </div>
      </div>

      <div class="row justify-content-center mt-0">
        <div class="d-flex justify-content-center align-items-center img-container-mobile-fix">
          <img
            src="fxd2.jpg"
            alt="Logo Bibliotech"
            class="img-fluid img-harmonizada"
            onmouseenter="document.getElementById('audioHover').play()"
          >
        </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Scripts -->
<?php include 'components/sidebar-script.php'; ?>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
