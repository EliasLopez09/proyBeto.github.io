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
    var formJugador = document.querySelector("#formJugador");
    formJugador.onsubmit = function (e) {
      e.preventDefault();

      var idJugador = document.querySelector('#idJugador').value;
      var strCodigo = document.querySelector('#txtCodigo').value;
      var intClub = document.querySelector('#listClub').value;
      var strNombre = document.querySelector('#txtNombre').value;
      var strApellido = document.querySelector('#txtApellido').value;
      var strCarnet = document.querySelector('#txtCarnet').value;
      var strFechaJuego = document.querySelector('#txtFechaJuego').value;

      if (strCodigo == "" || intClub == "" || strNombre == "" || strApellido == "" || strCarnet == "" || strFechaJuego == "") {
        swal("Atención", "Todos los campos son obligatorios.", "error");
        return false;
      }
      divLoading.style.display = "flex"; // Muestra el loader

      // Crea un objeto de petición AJAX compatible con todos los navegadores
      var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');

      // Define la URL para enviar el formulario (controlador Jugadores.php)
      var ajaxUrl = base_url + '/Jugadores/setJugador';

      // Crea un objeto FormData para enviar los datos del formulario
      var formData = new FormData(formJugador);

      // Abre la conexión AJAX
      request.open("POST", ajaxUrl, true);
      request.send(formData);


      request.onreadystatechange = function () {
        if (request.readyState == 4 && request.status == 200) {
          var objData = JSON.parse(request.responseText);
          if (objData.status) {
            $('#modalFormJugador').modal("hide");
            formJugador.reset();
            swal("Jugador", objData.msg, "success");
            tableJugadores.api().ajax.reload();
          } else {
              swal("Error", objData.msg, "error");
            // Si ya existe el carnet, permitir editar club
            /*if (objData.exist_carnet) {
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
            }*/
          }
        }
        divLoading.style.display = "none"; // Oculta el loader
            return false;
      }
    }
  
});


function openModal() {
    document.querySelector('#idJugador').value = "";
    document.querySelector('#listClub').value = "";
    document.querySelector('#txtCodigo').value = "";
    document.querySelector('#txtNombre').value = "";
    document.querySelector('#txtApellido').value = "";
    document.querySelector('#txtCarnet').value = "";
    document.querySelector('#listStatus').value = "";
    document.querySelector('#txtFechaJuego').value = "";

    document.querySelector('#titleModal').innerHTML = "Nuevo Jugador";
    document.querySelector('.modal-header').classList.replace("headerUpdate", "headerRegister");
    document.querySelector('#btnText').innerHTML = "Guardar";
    document.querySelector('#btnActionForm').classList.replace("btn-info", "btn-primary");
    fntCargarClubes();
    $('#modalFormJugador').modal('show');
    
}



// Editar jugador
function fntEditJugador(id) {
    document.querySelector('#titleModal').innerHTML = "Actualizar Jugador";
    document.querySelector('.modal-header').classList.replace("headerRegister", "headerUpdate");
    document.querySelector('#btnActionForm').classList.replace("btn-primary", "btn-info");
    document.querySelector('#btnText').innerHTML = "Actualizar";

    var ajaxUrl = base_url + "/Jugadores/getJugador/" + id;
    var request = new XMLHttpRequest();
    request.open("GET", ajaxUrl, true);
    request.send();

    request.onreadystatechange = function () {
        if (request.readyState == 4 && request.status == 200) {
            let objData = JSON.parse(request.responseText);

            if (objData.status) {
                // Primero cargamos los clubes
                fntCargarClubes();

                // Pequeño delay para que se complete el AJAX anterior
                setTimeout(function () {
                    document.querySelector("#idJugador").value = objData.data.id;
                    //document.querySelector('#txtCodigo').value = objData.data.codigo;
                    document.querySelector("#txtNombre").value = objData.data.nombre;
                    document.querySelector("#txtApellido").value = objData.data.apellido;
                    document.querySelector("#txtCarnet").value = objData.data.carnet;
                    document.querySelector("#txtFechaJuego").value = objData.data.fechajuego;

                    // Seleccionar club correctamente
                    document.querySelector("#listClub").value = objData.data.club_id;

                    // Seleccionar estado
                    document.querySelector("#listStatus").value = objData.data.status;

                    // Mostrar modal
                    $('#modalFormJugador').modal('show');
                }, 100); // Ajusta el delay si es necesario
            } else {
                swal("Error", objData.msg, "error");
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
            var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            var ajaxUrl = base_url+'/Jugadores/delJugador';
            var strData = "idJugador="+ id;
            request.open("POST",ajaxUrl,true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(strData);

            request.onreadystatechange = function(){
                if(request.readyState == 4 && request.status == 200){
                    var objData = JSON.parse(request.responseText);
                    if(objData.status){
                        swal({
                            title: "Desactivado!",
                            text: objData.msg,
                            type: "success",
                            timer: 1000, // 3 segundos
                            showConfirmButton: false
                        });
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
        text: "¿Realmente quiere activar este jugador?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, activar!",
        cancelButtonText: "No, cancelar!",
        closeOnConfirm: false,
        closeOnCancel: true
    }, function(isConfirm) {
        if (isConfirm) {
            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = base_url + '/Jugadores/restoreJugador';
            let strData = "idJugador=" + id;
            request.open("POST", ajaxUrl, true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(strData);

            request.onreadystatechange = function() {
                if (request.readyState == 4 && request.status == 200) {
                    let objData = JSON.parse(request.responseText);
                    if (objData.status) {
                        swal({
                            title: "Activado!",
                            text: objData.msg,
                            type: "success",
                            timer: 1000, // 3 segundos
                            showConfirmButton: false
                        });
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
    alert("Hola desde JS de jugadore : " + id);
    }

function fntCargarClubes() {
    if (document.querySelector('#listClub')) {
        let ajaxUrl = base_url + "/Clubes/getSelectClubes";
        let request = new XMLHttpRequest();
        request.open("GET", ajaxUrl, true);
        request.send();

        request.onreadystatechange = function () {
            if (request.readyState == 4 && request.status == 200) {
                document.querySelector('#listClub').innerHTML = request.responseText;
            }
        }
    }
}

  function fntPrueba(id) {
    alert("Hola desde JS, ID del club: " + id);
    }




