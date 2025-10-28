
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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <?php include 'components/sidebar-style.php'; ?>
  <style>
    body {
      background-color: rgb(238, 255, 235);
      margin: 0;
      overflow-x: hidden;
      font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .wrapper {
      display: flex;
    }

    .content {
      margin-left: 250px;
      padding: 2rem;
      flex: 1;
      transition: margin-left 0.3s;
    }

    .card {
      border: none;
      border-radius: 16px;
      box-shadow: 0 2px 12px rgba(0,0,0,0.18);
      margin-bottom: 6px;
      width: 100%;
      max-width: 600px;
      margin-left: auto;
      margin-right: auto;
    }
    .dashboard-card-title {
      font-size: 2rem;
      font-weight: bold;
      color: #fff;
      width: 100%;
      text-align: center;
      word-break: break-word;
    }
    .dashboard-card-value {
      font-size: 3rem;
      font-weight: bold;
      color: #fff;
    }
    .dashboard-card-desc {
      font-size: 1.15rem;
      color: #fff;
    }
    @media (max-width: 768px) {
      .content {
        margin-left: 0 !important;
      }
      .dashboard-card-title {
        font-size: 1.3rem;
        text-align: center;
        width: 100%;
        padding-left: 0;
        padding-right: 0;
      }
      .card-body {
        padding: 1.2rem 0.5rem !important;
      }
    }
  </style>
</head>
<body>

<?php include 'components/sidebar-logado.php'; ?>

  <div class="content">
    <div class="container">
      <div class="row">
        <?php
        require_once 'conexao.php';
        // Usa a variável $conexao definida em conexao.php
        // Total de empréstimos
        $sqlTodos = "SELECT COUNT(*) as total FROM emprestimo";
        $resTodos = mysqli_query($conexao, $sqlTodos);
        $qtdTodos = ($resTodos && $row = mysqli_fetch_assoc($resTodos)) ? $row['total'] : 0;

        // Empréstimos ativos
        $sqlAtivos = "SELECT COUNT(*) as total FROM emprestimo WHERE ativo = '1'";
        $resAtivos = mysqli_query($conexao, $sqlAtivos);
        $qtdAtivos = ($resAtivos && $row = mysqli_fetch_assoc($resAtivos)) ? $row['total'] : 0;

        // Empréstimos à vencer
        $sqlVencer = "SELECT COUNT(*) as total FROM emprestimo WHERE ativo = '1' AND vencimento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 5 DAY)";
        $resVencer = mysqli_query($conexao, $sqlVencer);
        $qtdVencer = ($resVencer && $row = mysqli_fetch_assoc($resVencer)) ? $row['total'] : 0;

        // Empréstimos atrasados
        $sqlAtrasados = "SELECT COUNT(*) as total FROM emprestimo WHERE ativo = '1' AND vencimento < CURDATE()";
        $resAtrasados = mysqli_query($conexao, $sqlAtrasados);
        $qtdAtrasados = ($resAtrasados && $row = mysqli_fetch_assoc($resAtrasados)) ? $row['total'] : 0;
        ?>
        <div class="col-12" style="margin-bottom: 6px;">
          <div class="card text-white bg-primary">
            <a href="todosEmprestimos.php" style="text-decoration:none;display:block;width:100%;">
              <div class="card-body d-flex flex-column justify-content-center align-items-center p-4">
                <h5 class="card-title dashboard-card-title">Todos Empréstimos</h5>
                <span class="dashboard-card-value">
                  <?= $qtdTodos ?>
                </span>
                <small class="dashboard-card-desc">Total de registros</small>
              </div>
            </a>
          </div>
        </div>
        <div class="col-12" style="margin-bottom: 6px;">
          <div class="card text-white bg-success">
            <a href="listaEmprestimoAtivo.php" style="text-decoration:none;display:block;width:100%;">
              <div class="card-body d-flex flex-column justify-content-center align-items-center p-4">
                <h5 class="card-title dashboard-card-title">Empréstimos Ativos</h5>
                <span class="dashboard-card-value">
                  <?= $qtdAtivos ?>
                </span>
                <small class="dashboard-card-desc">Atualmente em aberto</small>
              </div>
            </a>
          </div>
        </div>
        <div class="col-12" style="margin-bottom: 6px;">
          <div class="card text-white bg-warning">
            <a href="emprestimoVence.php" style="text-decoration:none;display:block;width:100%;">
              <div class="card-body d-flex flex-column justify-content-center align-items-center p-4">
                <h5 class="card-title dashboard-card-title">À Vencer</h5>
                <span class="dashboard-card-value">
                  <?= $qtdVencer ?>
                </span>
                <small class="dashboard-card-desc">Vencem em até 5 dias</small>
              </div>
            </a>
          </div>
        </div>
        <div class="col-12" style="margin-bottom: 6px;">
          <div class="card text-white bg-danger">
            <a href="emprestimoVencido.php" style="text-decoration:none;display:block;width:100%;">
              <div class="card-body d-flex flex-column justify-content-center align-items-center p-4">
                <h5 class="card-title dashboard-card-title">Atrasados</h5>
                <span class="dashboard-card-value">
                  <?= $qtdAtrasados ?>
                </span>
                <small class="dashboard-card-desc">Já passaram do prazo</small>
              </div>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

<?php include 'components/sidebar-script.php'; ?>

</body>
</html>
