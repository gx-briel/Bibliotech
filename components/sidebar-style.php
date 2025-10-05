<?php
// Componente de estilos CSS para a sidebar
?>
<style>
  /* Reset pequeno e variáveis */
  :root {
    --sidebar-width: 260px;
    --sidebar-bg-1: #0f1724; /* muito escuro */
    --sidebar-bg-2: #172033; /* gradiente */
    --accent: #06b6d4; /* ciano vibrante */
    --muted: #98a4b3;
    --glass: rgba(255,255,255,0.03);
  }

  .wrapper {
    display: flex;
    align-items: stretch;
  }

  /* Sidebar principal */
  .sidebar {
    width: var(--sidebar-width);
    min-height: 100vh;
    position: fixed;
    left: 0;
    top: 0;
    z-index: 1100;
    padding: 18px 16px;
    box-sizing: border-box;
    color: #e6eef6;
    background: linear-gradient(180deg, var(--sidebar-bg-1) 0%, var(--sidebar-bg-2) 100%);
    border-right: 1px solid rgba(255,255,255,0.03);
    box-shadow: 8px 0 30px rgba(2,6,23,0.45);
    transform: translateX(0);
    transition: transform .32s cubic-bezier(.2,.9,.3,1), width .22s ease;
    display: flex;
    flex-direction: column;
    gap: 12px;
  }

  .sidebar.hidden {
    transform: translateX(calc(-1 * var(--sidebar-width)));
  }

  /* Brand */
  .brand {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .brand-link { display:flex; align-items:center; gap:12px; text-decoration:none; color:inherit; }
  .brand-logo { width:46px; height:46px; border-radius:10px; background:linear-gradient(135deg,#0ea5a4 0%, #06b6d4 100%); display:flex; align-items:center; justify-content:center; font-size:1.2rem; color:#062023; box-shadow: 0 6px 18px rgba(6, 22, 33, 0.36); }
  .brand-text strong{ display:block; font-size:1.05rem; }
  .brand-text small{ display:block; font-size:0.72rem; color:var(--muted); }

  .collapse-btn{ margin-left:auto; background:transparent; border:none; color:inherit; padding:8px; cursor:pointer; border-radius:8px; transition:background .15s; }
  .collapse-btn:hover{ background: rgba(255,255,255,0.03); }

  /* Perfil e avatar */
  .profile{ display:flex; gap:12px; align-items:center; padding:8px 4px; border-radius:10px; background: linear-gradient(180deg, rgba(255,255,255,0.015), transparent); }
  .avatar{ width:48px; height:48px; border-radius:10px; background: linear-gradient(135deg,#0ea5a4,#06b6d4); display:flex; align-items:center; justify-content:center; font-size:1.2rem; color:#021316; }
  .profile-info .name{ font-weight:700; }
  .profile-info .role{ font-size:0.8rem; color:var(--muted); }

  /* Busca compacta dentro da sidebar */
  .sidebar-search{ margin-top:6px; }
  .sidebar-search input{ width:100%; padding:10px 12px; border-radius:10px; border:1px solid rgba(255,255,255,0.04); background: rgba(255,255,255,0.02); color:inherit; outline:none; transition: box-shadow .12s; }
  .sidebar-search input::placeholder{ color: rgba(230,238,246,0.5); }
  .sidebar-search input:focus{ box-shadow: 0 6px 24px rgba(6, 22, 33, 0.25); border-color:var(--accent); }

  /* Links principais */
  .nav-links{ list-style:none; padding:0; margin:6px 0 0 0; display:flex; flex-direction:column; gap:6px; }
  .nav-links li a{ display:flex; align-items:center; gap:12px; padding:10px 12px; color:inherit; text-decoration:none; border-radius:8px; transition: background .14s, transform .08s; font-weight:600; }
  .nav-links li a i{ width:26px; text-align:center; font-size:1.05rem; color:rgba(255,255,255,0.92); }
  .nav-links li a span{ flex:1; }
  .nav-links li a:hover{ background: rgba(6, 182, 212, 0.08); transform: translateX(4px); color:var(--accent); }
  .nav-links li a.active{ background: linear-gradient(90deg, rgba(6,182,212,0.12), rgba(6,182,212,0.06)); color:var(--accent); box-shadow: inset 0 0 0 1px rgba(6,182,212,0.04); }

  /* Footer da sidebar */
  .sidebar-footer{ margin-top:auto; display:flex; gap:8px; align-items:center; justify-content:space-between; }
  .sidebar-footer .home-link{ display:flex; align-items:center; gap:8px; padding:8px 10px; border-radius:8px; color:inherit; text-decoration:none; background: rgba(255,255,255,0.02); }
  .sidebar-footer .logout-btn{ background:var(--accent); color:#022; border:none; padding:8px 10px; border-radius:8px; cursor:pointer; box-shadow:0 8px 20px rgba(6,182,212,0.16); }

  /* Botão para reabrir a sidebar quando escondida */
  .show-sidebar-btn{ position:fixed; top:18px; left:18px; z-index:1200; background:var(--accent); color:#021316; border:none; padding:10px 12px; border-radius:10px; font-weight:700; display:none; box-shadow:0 10px 26px rgba(6,182,212,0.12); cursor:pointer; }
  .sidebar.hidden ~ .show-sidebar-btn{ display:block; }

  /* Tornar responsivo */
  @media (max-width: 900px){
    .sidebar{ transform: translateX(calc(-1 * var(--sidebar-width))); position:fixed; }
    .sidebar.hidden{ transform: translateX(0); }
    .show-sidebar-btn{ display:block; }
    /* garante que o conteúdo ocupe toda a largura disponível e mantenha o card centralizado */
    .content{ margin-left:0 !important; width:100%; display:flex; justify-content:center; }
  }

</style>
