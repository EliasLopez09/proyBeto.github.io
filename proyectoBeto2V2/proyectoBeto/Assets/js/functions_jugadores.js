// Archivo: functions_jugadores.js

let tableJugadores;
let rowTable = "";

// Inicialización de DataTable al cargar la vista
window.addEventListener('load', function () {
   let urlAjax;
    // Detectar si estamos en jugadores o jugadoresLibres
    if (window.location.href.includes("jugadoresLibres")) {
        urlAjax = base_url + "/Jugadores/getJugadoresLibres";
    } else {
        urlAjax = base_url + "/Jugadores/getJugadoresActivos";
    }
    tableJugadores = $('#tableJugadores').dataTable({
    "aProcessing": true,
    "aServerSide": true,
    "language": {
      "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
    },
    "ajax": {
      "url": urlAjax,
      "dataSrc": ""
    },
    "columns": [
        { data: 'id' },   
        { data: 'club' },
        { data: 'codigo' },
        { data: 'nombre' },
        { data: 'apellido' },
        { data: 'carnet' },
        { data: 'status' },
        { data: 'fechanacimiento' },
        { data: 'historial' },
        { data: 'options' }
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
      var strFechaNacimiento = document.querySelector('#txtFechaNacimiento').value;
      var strFechaPartido = document.querySelector('#txtFechaPartido').value;

      

      if (intClub == "" || strNombre == "" || strApellido == "" || strCarnet == "" || strFechaNacimiento == "") {
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
      
      const objDataDebug = {};
      formData.forEach((value, key) => objDataDebug[key] = value);
      console.log("Datos a enviar:", objDataDebug);

      // Abre la conexión AJAX
      request.open("POST", ajaxUrl, true);

      request.send(formData);


      request.onreadystatechange = function () {
        if (request.readyState == 4 && request.status == 200) {
            console.log("Respuesta del servidor:", request.responseText);
            var objData = JSON.parse(request.responseText);
            if (objData.status) {
                $('#modalFormJugador').modal("hide");
                formJugador.reset();
                swal("Jugador", objData.msg, "success");
                tableJugadores.api().ajax.reload();
            } else {
                swal("Error", objData.msg, "error");
                // Si ya existe el carnet, permitir editar club
                console.log("usuario ya existe, chequeando carnet...");
                console.log("Respuesta del servidor:", objData);
                if (objData.exist_carnet) {
                    swal({
                    title: "Jugador ya existe",
                    text: objData.msg + "\n¿Deseas ir a la edición?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Editar",
                    cancelButtonText: "Cancelar",
                    closeOnConfirm: true
                }, function (isConfirm) {
                    if (isConfirm) {
                        // Llamamos a la función de edición directamente
                        fntEditJugador(objData.data.id);    
                    }
                });
                }
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
    //document.querySelector('#listStatus').value = "";
    document.querySelector('#txtFechaNacimiento').value = "";

    document.querySelector('#titleModal').innerHTML = "Nuevo Jugador";
    document.querySelector('.modal-header').classList.replace("headerUpdate", "headerRegister");
    document.querySelector('#btnText').innerHTML = "Guardar";
    document.querySelector('#btnActionForm').classList.replace("btn-info", "btn-primary");
    fntCargarClubes();
    $('#modalFormJugador').modal('show');
    
}
// Obtener fecha del servidor y asignarla al campo de fecha
// document.addEventListener('DOMContentLoaded', function() {
//   const btnFecha = document.querySelector('#btnFechaActual');
//   if (btnFecha) {
//     btnFecha.addEventListener('click', function() {
//       let request = new XMLHttpRequest();
//       let ajaxUrl = base_url + '/Jugadores/getFechaServidor';
//       request.open("GET", ajaxUrl, true);
//       request.send();
//       request.onreadystatechange = function() {
//         if (request.readyState == 4 && request.status == 200) {
//           try {
//             let objData = JSON.parse(request.responseText);
//             if (objData.fecha) {
//               document.querySelector('#txtFechaPartido').value = objData.fecha;
//             }
//           } catch (error) {
//             console.error("Error al parsear JSON:", request.responseText);
//           }
//         }
//       }
//     });
//   }
// });





// Editar jugador
function fntEditJugador(id) {
    console.log("ID del jugador a editar: " + id);
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
                    document.querySelector('#txtCodigo').value = objData.data.codigo;
                    document.querySelector("#txtNombre").value = objData.data.nombre;
                    document.querySelector("#txtApellido").value = objData.data.apellido;
                    document.querySelector("#txtCarnet").value = objData.data.carnet;
                    document.querySelector("#txtFechaNacimiento").value = objData.data.fechanacimiento;
                    //document.querySelector("#txtFechaJuego").value = objData.data.fechanacimiento;

                    // Seleccionar club correctamente
                    document.querySelector("#listClub").value = objData.data.club_id;

                    // Seleccionar estado
                    //document.querySelector("#listStatus").value = objData.data.status;

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

function cargarHistorial(jugador_id, boton) {
    // Celda donde colocaremos el select
    const celda = boton.parentNode;

    fetch(base_url + '/Jugadores/getHistorialPorJugador/' + jugador_id)
        .then(res => res.json())
        .then(data => {
            if (data.status && data.historial.length > 0) {
                let opciones = data.historial.map(item => `<option>${item.fecha_partido}</option>`).join('');
                celda.innerHTML = `
                    <select class="form-control form-control-sm">
                        ${opciones}
                    </select>
                `;
            } else {
                celda.innerHTML = '<em>Sin historial</em>';
            }
        })
        .catch(err => {
            console.error(err);
            celda.innerHTML = '<em>Error al cargar</em>';
        });
}

// $('#btnFechaServidor').on('click', function(){
//     $.ajax({
//         url: base_url + '/Jugadores/getServerDate?action=getServerDate',
//         type: 'GET',
//         success: function(response){
//             const data = JSON.parse(response);
//             $('#txtFechaPartido').val(data.fecha); // llena el campo tipo date
//         }
//     });
// });

//ejemplo con alerta 

$('#btnFechaServidor').on('click', function(){
    $.ajax({
        url: base_url + '/Jugadores/getServerDate', 
        type: 'GET',
        // Asegúrate de usar 'json' aquí, ya que el servidor devuelve JSON
        dataType: 'json', 
        success: function(data){
            // PASO 1: Imprimir el objeto completo en la consola del navegador
            //console.log("Respuesta del servidor (objeto 'data'):", data);
            
            // PASO 2: Verificar el status y si es correcto, mostrar una alerta
            if(data.status){
                // Muestra la fecha como una alerta
                //alert('¡AJAX funciona! Fecha del servidor: ' + data.fecha);
                
                // Pasa el valor al campo de texto, como lo tenías originalmente
                $('#txtFechaPartido').val(data.fecha); 
            } else {
                // Muestra una alerta si el status es 'false' (aunque no debería pasar aquí)
                alert('Error al obtener la fecha: ' + data.msg);
                console.error("Error lógico del servidor:", data.msg);
            }
        },
        error: function(xhr, status, error) {
            // Se ejecuta si hay un problema en la conexión o en el servidor (código 404, 500, etc.)
            alert("Error de Conexión/Servidor. Revisa la consola.");
            console.error("Error en la llamada AJAX. Status:", status, "Error:", error);
        }
    });
});









