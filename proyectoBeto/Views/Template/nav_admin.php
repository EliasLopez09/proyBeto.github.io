<!-- Sidebar menu -->
<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar">
  <!-- Avatar e información del usuario -->
  <div class="app-sidebar__user">
    <img class="app-sidebar__user-avatar" src="<?= media(); ?>/images/avatar.png" alt="User Image">
    <div>
      <p class="app-sidebar__user-name"><?= $_SESSION['userData']['nombres']; ?></p>
      <p class="app-sidebar__user-designation">Usuario Liga</p>
    </div>
  </div>

  <!-- Menú de navegación -->
  <ul class="app-menu">

    <!-- Dashboard principal -->
    <li>
      <a class="app-menu__item" href="<?= base_url(); ?>/dashboard">
        <i class="app-menu__icon fa fa-dashboard"></i>
        <span class="app-menu__label">Inicio</span>
      </a>
    </li>

    <!-- Módulo Jugadores -->
    <li class="treeview">
      <a class="app-menu__item" href="#" data-toggle="treeview">
        <i class="app-menu__icon fa fa-futbol-o" aria-hidden="true"></i>
        <span class="app-menu__label">Jugadores</span>
        <i class="treeview-indicator fa fa-angle-right"></i>
      </a>
      <ul class="treeview-menu">
        <li>
          <a class="treeview-item" href="<?= base_url(); ?>/jugadores">
            <i class="icon fa fa-circle-o"></i> Gestión de Jugadores
          </a>
        </li>
        <li>
          <a class="treeview-item" href="<?= base_url(); ?>/clubes">
            <i class="icon fa fa-circle-o"></i> Clubes
          </a>
        </li>
        <li>
          <a class="treeview-item" href="<?= base_url(); ?>/historial">
            <i class="icon fa fa-circle-o"></i> Historial de Clubes
          </a>
        </li>
      </ul>
    </li>

    <!-- Logout -->
    <li>
      <a class="app-menu__item" href="<?= base_url(); ?>/logout">
        <i class="app-menu__icon fa fa-sign-out" aria-hidden="true"></i>
        <span class="app-menu__label">Cerrar sesión</span>
      </a>
    </li>
    
  </ul>
</aside>
