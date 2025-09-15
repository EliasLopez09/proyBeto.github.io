<?php 
    // Carga el encabezado del panel con CSS y JS común
    headerAdmin($data); 
    
    // Carga el modal para crear o editar jugadores (debe llamarse modalJugadores.php en la misma vista)
    getModal('modalJugadores', $data); // Asegúrate de tener el modalJugadores.php creado
?>

<!-- Contenedor para insertar respuestas AJAX -->
 <div id="contentAjax"></div>

<main class="app-content">  
    <!-- Encabezado con título y botón para nuevo club -->  
    <div class="app-title">
        <div>
            <h1><i class="fas fa-futbol"></i> <?= $data['page_title'] ?>
                    <!-- Botón para abrir el modal de nuevo club -->
                    <button class="btn btn-primary" type="button" onclick="openModal();">
                        <i class="fas fa-plus-circle"></i> Nuevo
                    </button>
            </h1>
        </div>
        <!-- Breadcrumb de navegación -->
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="<?= base_url(); ?>/jugadores"><?= $data['page_title'] ?></a></li>
        </ul>
    </div>

    <!-- Contenido principal con tabla -->              
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="tile-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="tableJugadores">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Club</th>
                                    <th>Código</th>
                                    <th>Nombre</th>
                                    <th>Apellidos</th>
                                    <th>Carnet</th>
                                    <th>Status</th>
                                    <th>Fecha último juego</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Datos serán llenados por AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php footerAdmin($data); ?>
