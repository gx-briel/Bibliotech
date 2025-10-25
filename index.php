<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Bibliotech</title>

  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <?php include 'components/sidebar-style.php'; ?>

  <style>
    :root{--bg:#f6f7f8;--card:#ffffff;--muted:#6b6f73;--accent:#e9edf0}
    html,body{
      height:100%;
    }
    body {
      background-color: var(--bg);
      margin: 0;
      overflow-x: hidden;
      padding-bottom: 40px;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: #222;
      -webkit-font-smoothing:antialiased;
      -moz-osx-font-smoothing:grayscale;
    }

    .main-hero{
      min-height: 85vh;
      display:flex;
      align-items:center;
      justify-content:center;
      padding: 3rem 1rem;
    }

    .hero-card{
      background: linear-gradient(180deg, rgba(255,255,255,0.9), rgba(255,255,255,0.95));
      border-radius: 12px;
      box-shadow: 0 10px 30px rgba(29,31,33,0.06);
      max-width:1100px;
      width:100%;
      display:grid;
      grid-template-columns: 1fr 460px;
      gap: 2rem;
      padding: 2.25rem;
      align-items:center;
    }

    .hero-content h1{
      margin:0 0 .5rem 0;
      font-size:2rem;
      color:#111;
      line-height:1.05;
    }

    .hero-content p{
      color:var(--muted);
      margin-bottom:1.25rem;
      font-size:1.03rem;
    }

    .hero-cta{display:flex;gap:0.75rem;flex-wrap:wrap}
    .btn-primary{
      background-color:#2b6ea3;color:#fff;border:0;padding:.6rem 1rem;border-radius:8px;font-weight:600;text-decoration:none;display:inline-block
    }
    .btn-outline{background:transparent;border:1px solid #d6dbe0;color:#2b2f33;padding:.55rem .9rem;border-radius:8px;text-decoration:none}

    .hero-visual{
      display:flex;align-items:center;justify-content:center;
    }

    .visual-frame{
      background: linear-gradient(180deg,var(--accent),#fff);
      border-radius:10px;padding:1rem;width:100%;height:100%;display:flex;align-items:center;justify-content:center;box-shadow:inset 0 1px 0 rgba(255,255,255,0.6);
    }

    .illustration{max-width:380px;width:100%;height:auto}

    footer{padding:12px 0;text-align:center;color:var(--muted);font-size:.9rem}

    @media (max-width: 992px){
      .hero-card{grid-template-columns:1fr;max-width:780px}
      .hero-visual{order:-1;margin-bottom:0.5rem}
    }
    @media (max-width:560px){
      .hero-card{padding:1rem}
      .hero-content h1{font-size:1.5rem}
    }
  </style>
</head>
<body>
  
<?php include 'components/sidebar-logoff.php'; ?>

  <!-- Conteúdo principal -->
  <div class="content main-hero">
    <div class="hero-card">
      <div class="hero-content">
        <h1>Gestão de empréstimos pensada para bibliotecas modernas</h1>
        <p>Bibliotech ajuda bibliotecários e leitores a controlar empréstimos, prazos e renovações com simplicidade e segurança. Reduza faltas, aumente a disponibilidade e ofereça uma experiência digital profissional para seus usuários.</p>

        <div class="hero-cta">
          <a class="btn-primary" href="login.php">Entrar / Meu Acesso</a>
          <a class="btn-outline" href="#features">Saiba mais</a>
        </div>

        <div style="margin-top:1.25rem;color:var(--muted);font-size:.9rem">Teste grátis com dados reais. Sem cadastros desnecessários — comece a organizar acervos hoje.</div>
      </div>

      <div class="hero-visual">
        <div class="visual-frame" aria-hidden="true">
          <!-- Ilustração SVG leve para representar livros/gestão -->
          <svg class="illustration" viewBox="0 0 600 400" xmlns="http://www.w3.org/2000/svg" role="img">
            <rect x="12" y="40" width="200" height="280" rx="10" fill="#2b6ea3" opacity="0.95"/>
            <rect x="220" y="70" width="200" height="250" rx="10" fill="#5aa1d8" opacity="0.95"/>
            <rect x="430" y="100" width="140" height="210" rx="10" fill="#9cc7e8" opacity="0.95"/>
            <g fill="#fff" opacity="0.95">
              <rect x="36" y="92" width="140" height="12" rx="3"/>
              <rect x="36" y="116" width="110" height="10" rx="3"/>
              <rect x="244" y="122" width="130" height="10" rx="3"/>
              <rect x="244" y="148" width="90" height="10" rx="3"/>
              <rect x="454" y="150" width="80" height="10" rx="3"/>
            </g>
            <circle cx="520" cy="320" r="38" fill="#ffffff" opacity="0.08"/>
          </svg>
        </div>
      </div>
    </div>
  </div>

</div>

<?php include 'components/sidebar-script.php'; ?>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>