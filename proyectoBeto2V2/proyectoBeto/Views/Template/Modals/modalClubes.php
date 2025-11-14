<!-- Modal para registro/edición de Clubes -->
<div class="modal fade" id="modalFormClub" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      
      <!-- Cabecera del modal -->
      <div class="modal-header headerRegister">
        <h5 class="modal-title" id="titleModal">Nuevo Club34</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      
      <!-- Cuerpo del modal -->
      <div class="modal-body">
        <div class="tile">
          <div class="tile-body">
            <form id="formClub" name="formClub">
              <input type="hidden" id="idClub" name="idClub" value="">

              <!-- Campo para el nombre del club -->
              <div class="form-group">
                <label class="control-label">Nombre del club</label>
                <input class="form-control" id="txtNombre" name="txtNombre" type="text" placeholder="Ej. Deportivo Aurora" required>
              </div>

              <!-- Selector de estado--> 
              <div class="form-group" style="display: none;">
                <label for="listStatus">Estado</label>
                <select class="form-control" id="listStatus" name="listStatus" required>
                  <option value="1">Activo</option>
                  <option value="0">Inactivo</option>
                </select>
              </div>
            
              <!-- Botones -->
              <div class="tile-footer text-right">
                <button id="btnActionForm" class="btn btn-primary" type="submit">
                  <i class="fa fa-fw fa-lg fa-check-circle"></i>
                  <span id="btnText">Guardar</span>
                </button>
                &nbsp;&nbsp;&nbsp;
                <a class="btn btn-secondary" href="#" data-dismiss="modal">
                  <i class="fa fa-fw fa-lg fa-times-circle"></i> Cancelar
                </a>
              </div>

            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
