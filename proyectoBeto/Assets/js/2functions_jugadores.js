// Archivo: functions_jugadores.js

let tableJugadores;
let rowTable = "";

// Inicialización de DataTable al cargar la vista
window.addEventListener('load', function () {
  tableJugadores = $('#tableJugadores').dataTable({
    "aProcessing": true,
    "aServerSide": true,
    "language": {
      "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
    },
    "ajax": {
      "url": base_url + "/Jugadores/getJugadores",
      "dataSrc": ""
    },
    "columns": [
      { "data": "id" },
      { "data": "club" },
      { "data": "codigo" },
      { "data": "nombre" },
      { "data": "apellido" },
      { "data": "carnet" },
      { "data": "status" },
      { "data": "fechajuego" },
      { "data": "options" }
    ],
    "resonsieve": "true",
    "bDestroy": true,
    "iDisplayLength": 10,
    "order": [[0, "desc"]]
  });

  // Evento submit del formulario
  if (document.querySelector("#formJugador")) {
    let formJugador = document.querySelector("#formJugador");
    formJugador.onsubmit = function (e) {
      e.preventDefault();

      let idJugador = document.querySelector('#idJugador').value;
      let intClub = document.querySelector('#listClub').value;
      let strNombre = document.querySelector('#txtNombre').value;
      let strApellido = document.querySelector('#txtApellido').value;
      let strCarnet = document.querySelector('#txtCarnet').value;
      let strFechaJuego = document.querySelector('#txtFechaJuego').value;

      if (intClub == "" || strNombre == "" || strApellido == "" || strCarnet == "" || strFechaJuego == "") {
        swal("Atención", "Todos los campos son obligatorios.", "error");
        return false;
      }

      // Crea un objeto de petición AJAX compatible con todos los navegadores
      let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');

      // Define la URL para enviar el formulario (controlador Jugadores.php)
      let ajaxUrl = base_url + '/Jugadores/setJugador';

      // Crea un objeto FormData para enviar los datos del formulario
      let formData = new FormData(formJugador);

      // Abre la conexión AJAX
      request.open("POST", ajaxUrl, true);
      request.send(formData);


      request.onreadystatechange = function () {
        if (request.readyState == 4 && request.status == 200) {
          let objData = JSON.parse(request.responseText);
          if (objData.status) {
            $('#modalFormJugador').modal("hide");
            formJugador.reset();
            swal("Jugador", objData.msg, "success");
            tableJugadores.api().ajax.reload();
          } else {
            // Si ya existe el carnet, permitir editar club
            if (objData.exist_carnet) {
              swal({
                title: "Jugador ya existe",
                text: objData.msg + "\n¿Deseas actualizar el club asignado?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
              }).then((willEdit) => {
                if (willEdit) {
                  document.querySelector('#idJugador').value = objData.data.id;
                  document.querySelector('#listClub').value = objData.data.club_id;
                  $('#modalFormJugador').modal('show');
                }
              });
            } else {
              swal("Error", objData.msg, "error");
            }
          }
        }
      }
    }
  }
});

function openModal() {
  document.querySelector('#idJugador').value = "";
  document.querySelector('#titleModal').innerHTML = "Nuevo Jugador";
  document.querySelector('#btnText').innerHTML = "Guardar";
  document.querySelector('#formJugador').reset();
  cargarClubes();
  $('#modalFormJugador').modal('show');
  
  //document.querySelector('#btnActionForm').classList.replace("btn-info", "btn-primary");
} // Puedes añadir aquí funciones como fntEditJugador, fntDeleteJugador si las necesitas.


// Editar jugador
function fntEditJugador(id){
    document.querySelector('#titleModal').innerHTML ="Actualizar Jugador";
    document.querySelector('.modal-header').classList.replace("headerRegister", "headerUpdate");
    document.querySelector('#btnActionForm').classList.replace("btn-primary", "btn-info");
    document.querySelector('#btnText').innerHTML ="Actualizar";

    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let ajaxUrl = base_url+'/Jugadores/getJugador/'+id;
    request.open("GET",ajaxUrl,true);
    request.send();
    request.onreadystatechange = function(){
        if(request.readyState == 4 && request.status == 200){
            let objData = JSON.parse(request.responseText);
            if(objData.status){
                document.querySelector("#idJugador").value = objData.data.id;
                document.querySelector("#txtNombre").value = objData.data.nombre;
                document.querySelector("#txtApellido").value = objData.data.apellido;
                document.querySelector("#txtCarnet").value = objData.data.carnet;
                document.querySelector("#listClub").value = objData.data.club_id;
                document.querySelector("#listStatus").value = objData.data.status;
                document.querySelector("#txtFechaJuego").value = objData.data.fechajuego;
                $('#modalFormJugador').modal('show');
            }
        }
    }
}

// Eliminar jugador
function fntDelJugador(id){
    swal({
        title: "Desactivar Jugador",
        text: "¿Seguro que desea eliminar este jugador?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, Desactivar",
        cancelButtonText: "No, cancelar!",
        closeOnConfirm: false,
        closeOnCancel: true
    }, function(isConfirm) {
        if (isConfirm){
            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = base_url+'/Jugadores/delJugador';
            let strData = "idJugador="+id;
            request.open("POST",ajaxUrl,true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(strData);
            request.onreadystatechange = function(){
                if(request.readyState == 4 && request.status == 200){
                    let objData = JSON.parse(request.responseText);
                    if(objData.status){
                        swal("Eliminado!", objData.msg , "success");
                        tableJugadores.api().ajax.reload();
                    }else{
                        swal("Atención!", objData.msg , "error");
                    }
                }
            }
        }
    });
}
// Mostrar jugador
function fntViewJugador(idjugador){
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let ajaxUrl = base_url+'/Jugadores/getJugador/'+idjugador;
    request.open("GET",ajaxUrl,true);
    request.send();
    request.onreadystatechange = function(){
        if(request.readyState == 4 && request.status == 200){
            let objData = JSON.parse(request.responseText);
            if(objData.status){
                let estadoJugador = objData.data.status == 1 ? 
                '<span class="badge badge-success">Activo</span>' : 
                '<span class="badge badge-danger">Inactivo</span>';

                document.querySelector("#celCodigo").innerHTML = objData.data.codigo;
                document.querySelector("#celNombre").innerHTML = objData.data.nombre;
                document.querySelector("#celApellido").innerHTML = objData.data.apellidos;
                document.querySelector("#celCarnet").innerHTML = objData.data.carnet;
                document.querySelector("#celClub").innerHTML = objData.data.club;
                document.querySelector("#celFecha").innerHTML = objData.data.fecha_ultimo_juego;
                document.querySelector("#celEstado").innerHTML = estadoJugador;
                $('#modalViewJugador').modal('show');
            } else {
                swal("Error", objData.msg , "error");
            }
        }
    }
}

// Función para reactivar (activar) un jugador
    function fntRestoreJugador(id) {
        swal({
            title: "Activar Jugador",
            text: "¿Realmente quiere activar el Jugador?",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Sí, activar!",
            cancelButtonText: "No, cancelar!",
            closeOnConfirm: false,
            closeOnCancel: true
        }, function(isConfirm) {
            if (isConfirm) {
                let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
                let ajaxUrl = base_url+ '/Jugadores/restoreJugador';
                let strData = "idJugador=" + id;
                request.open("POST", ajaxUrl, true);
                request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                request.send(strData);

                request.onreadystatechange = function() {
                    if (request.readyState == 4 && request.status == 200) {
                        let objData = JSON.parse(request.responseText);
                        if (objData.status) {
                            swal("Activado!", objData.msg, "success");
                            tableJugadores.api().ajax.reload();
                        } else {
                            swal("Error!", objData.msg, "error");
                        }
                    }
                }
            }
        });
    }

function fntVerClub(id) {
    alert("Hola desde JS, ID del club: " + id);
    }

function cargarClubes() {
    fetch(base_url + "/Jugadores/getSelectClubes")
        .then(response => response.text())
        .then(html => {
            document.querySelector("#listClub").innerHTML = html;
        });
}



