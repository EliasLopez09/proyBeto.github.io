// Tabla de clubes y elemento de carga
//console.log("JS cargado correctamente.");
var tableClubes;
// var divLoading = document.querySelector("#divLoading");
let rowTable = "";



// Ejecutar cuando cargue la vista
window.addEventListener('load', function () {
    
    let urlAjax;

    // Detectar si estamos en la vista de clubes libres
    if (window.location.href.includes("clubesLibres")) {
        urlAjax = base_url + "/Clubes/getClubesLibres";
    } else {
        urlAjax = base_url + "/Clubes/getClubesActivos";
    }

    // Inicialización de DataTable para Clubes
    tableClubes = $('#tableClubes').dataTable({
        "aProcessing": true,
        "aServerSide": true,
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "ajax": {
            "url": urlAjax,
            "dataSrc": "data"
        },
        "columns": [
            {"data": "id"},             // ID del club
            {"data": "nombre"},         // Nombre del club
            {"data": "status"},         // Estado (activo/inactivo)
            {"data": "options"}         // Botones de acción (editar, eliminar)
        ],
        "responsive": "true",
        "bDestroy": true,
        "iDisplayLength": 10,           // Número de filas por página
        "order": [[0, "desc"]]          // Orden descendente por el ID
    });

    // Captura el formulario de Club (nuevo o editar)
    var formClub = document.querySelector("#formClub");
    formClub.onsubmit = function(e) {
        e.preventDefault(); // Previene el envío tradicional

        var intIdClub = document.querySelector('#idClub').value;
        var strNombre = document.querySelector('#txtNombre').value;
        //var intStatus = document.querySelector('#listStatus').value;

        // Validación: campos obligatorios
        if (strNombre == '') {
            swal("Atención", "El nombre es obligatorio.", "error");
            return false;
        }


        divLoading.style.display = "flex"; // Muestra el loader

        var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
        var ajaxUrl = base_url + '/Clubes/setClub';
        var formData = new FormData(formClub);
        request.open("POST", ajaxUrl, true);
        request.send(formData);

        request.onreadystatechange = function() {
            if (request.readyState == 4 && request.status == 200) {
                var objData = JSON.parse(request.responseText);
                if (objData.status) {
                    $('#modalFormClub').modal("hide");
                    formClub.reset();
                    swal("Clubes", objData.msg, "success");
                    tableClubes.api().ajax.reload(); // Recarga la tabla
                } else {
                    swal("Error", objData.msg, "error");
                }
            }
            divLoading.style.display = "none"; // Oculta el loader
            return false;
        }
    }

});

// Inicializa DataTable por si acaso (precaución redundante)
$('#tableClubes').DataTable();

// Abre el modal para registrar un nuevo club
function openModal() {
    document.querySelector('#idClub').value = "";
    document.querySelector('.modal-header').classList.replace("headerUpdate", "headerRegister");
    document.querySelector('#btnActionForm').classList.replace("btn-info", "btn-primary");
    document.querySelector('#btnText').innerHTML = "Guardar";
    document.querySelector('#titleModal').innerHTML = "Nuevo Club";
    document.querySelector("#formClub").reset();
    $('#modalFormClub').modal('show');
}

// Función para editar un club
function fntEditClub(id) {
    document.querySelector('#titleModal').innerHTML = "Actualizar Club";
    document.querySelector('.modal-header').classList.replace("headerRegister", "headerUpdate");
    document.querySelector('#btnActionForm').classList.replace("btn-primary", "btn-info");
    document.querySelector('#btnText').innerHTML = "Actualizar";

    var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    var ajaxUrl = base_url + '/Clubes/getClub/' + id;
    request.open("GET", ajaxUrl, true);
    request.send();

    request.onreadystatechange = function() {
        if (request.readyState == 4 && request.status == 200) {
            var objData = JSON.parse(request.responseText);
            if (objData.status) {
                document.querySelector("#idClub").value = objData.data.id;
                document.querySelector("#txtNombre").value = objData.data.nombre;

                var optionSelect = objData.data.status == 1
                    ? '<option value="1" selected class="notBlock">Activo</option>'
                    : '<option value="0" selected class="notBlock">Inactivo</option>';

                var htmlSelect = `${optionSelect}
                                  <option value="1">Activo</option>
                                  <option value="0">Inactivo</option>`;
                document.querySelector("#listStatus").innerHTML = htmlSelect;

                $('#modalFormClub').modal('show');
            } else {
                swal("Error", objData.msg, "error");
            }
        }
    }
}

// Función para eliminar (desactivar) un club
function fntDelClub(id) {
    
    swal({
        title: "Desactivar Club",
        text: "¿Realmente quiere desactivar el club?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, desactivar!",
        cancelButtonText: "No, cancelar!",
        closeOnConfirm: false,
        closeOnCancel: true
    }, function(isConfirm) {
        if (isConfirm) {
            var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            var ajaxUrl = base_url + '/Clubes/delClub/';
            var strData = "idClub=" + id;
            request.open("POST", ajaxUrl, true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(strData);

            request.onreadystatechange = function() {
                if (request.readyState == 4 && request.status == 200) {
                    var objData = JSON.parse(request.responseText);
                    if (objData.status) {
                        swal({
                            title: "Desactivado!",
                            text: objData.msg,
                            type: "success",
                            timer: 1000, // 3 segundos
                            showConfirmButton: false
                        });
                        tableClubes.api().ajax.reload();
                    } else {
                        swal("Atención!", objData.msg, "error");
                    }
                }
            }
        }
    });
}
// Función para reactivar (activar) un club
    function fntRestoreClub(id) {
        swal({
            title: "Activar Club",
            text: "¿Realmente quiere activar el club?",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Sí, activar!",
            cancelButtonText: "No, cancelar!",
            closeOnConfirm: false,
            closeOnCancel: true
        }, function(isConfirm) {
            if (isConfirm) {
                let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
                let ajaxUrl = base_url + '/Clubes/restoreClub';
                let strData = "idClub=" + id;
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
                        tableClubes.api().ajax.reload();
                        } else {
                            swal("Error!", objData.msg, "error");
                        }
                    }
                }
            }
        });
    }
    function fntPrueba(id) {
    alert("Hola desde JS, ID del club: " + id);
    }
