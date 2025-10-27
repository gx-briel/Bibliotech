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
    :root {
      --card: #ffffff;
      --muted: #6b6f73;
      --accent: #b33a3a;
    }

    body {
      background: linear-gradient(to right, #ece9e6, #ffffff);
      margin: 0;
      overflow-x: hidden;
      font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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



    /* Background image effect similar to index.php */
    .content {
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      gap: 1.25rem;
      min-height: 100vh;
      position: relative;
      margin-left: 250px;
      padding: 2rem;
      transition: margin-left 0.3s;
    }


    .content::before {
      content: '';
      position: absolute;
      inset: 0;
      background-image: url('fxd2.jpg');
      background-position: center;
      background-repeat: no-repeat;
      background-size: cover;
      opacity: 0.07;
      z-index: 0;
      pointer-events: none;
      filter: saturate(0.9) blur(0px);
    }

    /* Ensure content is above background */
    .container-fluid {
      position: relative;
      z-index: 1;
    }

    .card {
      border: none;
      border-radius: 12px;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      position: relative;
      z-index: 1;
    }

    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.18);
    }

    /* Gradients for dashboard cards */
    .card.bg-primary {
      background: linear-gradient(135deg,rgb(59, 154, 255) 0%, #0056b3 100%) !important;
    }

    .card.bg-success {
      background: linear-gradient(135deg,rgb(91, 206, 118) 0%, #1e7e34 100%) !important;
    }

    .card.bg-danger {
      background: linear-gradient(135deg,rgb(233, 98, 111) 0%, #c82333 100%) !important;
    }

    .card.bg-light {
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
    }

    .card-title {
      font-weight: 600;
      font-size: 1.1rem;
      letter-spacing: 0.3px;
    }

    .card-text {
      font-weight: 700;
      font-size: 2.5rem;
      margin-top: 0.5rem;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .card {
      animation: fadeIn 0.6s ease-in-out both;
    }

    .col-md-4:nth-child(1) .card {
      animation-delay: 0.1s;
    }

    .col-md-4:nth-child(2) .card {
      animation-delay: 0.2s;
    }

    .col-md-4:nth-child(3) .card {
      animation-delay: 0.3s;
    }

    @media (max-width: 768px) {
      .content {
        margin-left: 0 !important;
        padding: 1rem;
      }
      .card-text {
        font-size: 2rem;
      }
    }
.welcome-bump {
  position: relative;
  top: 0; /* mobile: não desloca */
}

@media (min-width: 768px) {
  .welcome-bump {
    top: -190px; /* tablets/desktop: sobe */
  }
}

.botaodash{
  position: relative;
  top: 0; /* mobile: não desloca */
}

@media (min-width: 768px) {
  .botaodash {
    top: -220px; /* tablets/desktop: sobe */
  }
}

.cards-dashboard{
  position: relative;
  top: 0; /* mobile: não desloca */
}

@media (min-width: 768px) {
  .cards-dashboard {
    top: -100px; /* tablets/desktop: sobe */
  }
}

  </style>
</head>
<body>

<?php include 'components/sidebar-logado.php'; ?>

  <div class="content">
    <div class="container-fluid">

      <h2 class="text-center mb-4 botaodash" style="font-weight: 600; color: #062c2a; font-size: 2rem;">
        <i class="fas fa-tachometer-alt"></i> Dashboard
      </h2>

      <!-- Welcome section -->
      <div class="row">
        <div class="col-12 mb-3 welcome-bump">
          <div class="card bg-light">
            <div class="card-body text-center p-3">
              <h3 class="mb-2" style="color: #062c2a; font-weight: 600;">
                <i class="fas fa-user-circle"></i> Bem-vindo, <?= htmlspecialchars($_SESSION['nome'] ?? 'Usuário'); ?>!
              </h3>
              <p class="mb-0" style="color: var(--muted);">
                Gerencie sua biblioteca de forma eficiente e moderna.
              </p>
            </div>
          </div>
        </div>
      </div>
      
      <div class="row text-center cards-dashboard">
        <div class="col-md-4 mb-4">
          <div class="card bg-primary text-white" style="cursor: pointer;" onclick="window.location.href='acervo.php'">
            <div class="card-body">
              <i class="fas fa-book fa-2x mb-3"></i>
              <h5 class="card-title">Total de Livros</h5>
              <p class="card-text display-4"><?= $totalLivros; ?></p>
            </div>
          </div>
        </div>
        <div class="col-md-4 mb-4">
          <div class="card bg-success text-white" style="cursor: pointer;" onclick="window.location.href='listaEmprestimoAtivo.php'">
            <div class="card-body">
              <i class="fas fa-book-reader fa-2x mb-3"></i>
              <h5 class="card-title">Empréstimos Ativos</h5>
              <p class="card-text display-4"><?= $totalEmp; ?></p>
            </div>
          </div>
        </div>
        <div class="col-md-4 mb-4">
          <div class="card bg-danger text-white" style="cursor: pointer;" onclick="window.location.href='emprestimoVencido.php'">
            <div class="card-body">
              <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
              <h5 class="card-title">Vencidos</h5>
              <p class="card-text display-4"><?= $totalVenc; ?></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

<!-- Scripts -->
<?php include 'components/sidebar-script.php'; ?>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
