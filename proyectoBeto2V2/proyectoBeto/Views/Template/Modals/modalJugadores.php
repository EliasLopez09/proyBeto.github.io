<!-- Modal para Registrar o Editar Jugador -->
<div class="modal fade" id="modalFormJugador" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">

      <!-- Cabecera del modal -->
      <div class="modal-header headerRegister">
        <h5 class="modal-title" id="titleModal">Nuevo Jugador</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <!-- Cuerpo del modal -->
      <div class="modal-body">
        <div class="tile">
          <div class="tile-body">
            <form id="formJugador" name="formJugador">
              <!-- Campo oculto para ID -->
              <input type="hidden" id="idJugador" name="idJugador">

              <p class="text-primary">Todos los campos son obligatorios.</p>

              <div class="form-group">
                <label for="listClub">Club</label>
                <select class="form-control" id="listClub" name="listClub" required>
                  <option value="">Seleccione club</option>
                  <?php
                    foreach ($data['clubes'] as $club) {
                      echo '<option value="'.$club['id'].'">'.$club['nombre'].'</option>';
                    }
                  ?>
                </select>
              </div>
              <!--
              <div class="form-group">
                <label for="txtCarnet">Código</label>
                <input type="text" class="form-control" id="txtCodigo" name="txtCodigo" required>
              </div> -->
              
              <!-- Código autogenerado (solo lectura)--> 
              <div class="form-group">
                <label for="txtCodigo">Código</label>
                <input type="text" class="form-control" id="txtCodigo" name="txtCodigo" readonly>
              </div> 
              
              <div class="form-group">
                <label for="txtCarnet">Carnet</label>
                <input type="text" class="form-control" id="txtCarnet" name="txtCarnet" required>
              </div>

              <div class="form-group">
                <label for="txtNombre">Nombre</label>
                <input type="text" class="form-control" id="txtNombre" name="txtNombre" required>
              </div>

              <div class="form-group">
                <label for="txtApellido">Apellido</label>
                <input type="text" class="form-control" id="txtApellido" name="txtApellido" required>
              </div>
            
              <!-- Estado -->
              <!-- <div class="form-group">
                <label for="listStatus">Estado</label>
                <select class="form-control" id="listStatus" name="listStatus" required>
                  <option value="">Seleccione estado</option>
                  <option value="1" selected>Activo</option>
                  <option value="0">Inactivo</option>
                </select>
              </div> -->
              <div class="form-group">
                <label for="txtFechaNacimiento">Fecha de Nacimiento</label>
                <input type="date" class="form-control" id="txtFechaNacimiento" name="txtFechaNacimiento" required value="1999-01-01" >
              </div>
              <!-- <div class="form-group">
                <label for="txtFechaPartido">Fecha de Partido</label>
                <input type="date" class="form-control" id="txtFechaPartido" name="txtFechaPartido" required>
              </div> -->

              <div class="form-group">
                <label for="txtFechaPartido">Fecha del Partido</label>
                <div class="input-group">
                  <input type="date" id="txtFechaPartido" class="form-control" name="txtFechaPartido">
                  <button type="button" id="btnFechaServidor" class="btn btn-info">Usar Fecha Actual</button>
                </div>
              </div>

              <div class="tile-footer">
                <button id="btnActionForm" class="btn btn-primary" type="submit">
                  <i class="fa fa-fw fa-lg fa-check-circle"></i><span id="btnText">Guardar</span>
                </button>
                <button class="btn btn-danger" type="button" data-dismiss="modal">
                  <i class="fa fa-fw fa-lg fa-times-circle"></i>Cerrar
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>
