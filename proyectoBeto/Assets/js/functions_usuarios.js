// Variables globales
let tableUsuarios;
let rowTable = ""; // Para guardar la fila seleccionada si se va a editar
let divLoading = document.querySelector("#divLoading");

// Espera que todo el HTML se cargue antes de ejecutar esto
document.addEventListener('DOMContentLoaded', function() {

    // Configuración del plugin DataTables
    tableUsuarios = $('#tableUsuarios').dataTable({
        "aProcessing": true,       // Habilita el procesamiento del lado del cliente
        "aServerSide": true,       // Habilita el procesamiento del lado del servidor
        "language": {
            // Traduce los textos del DataTable al español
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
        "ajax": {
            // URL del backend (PHP, Laravel, etc.) que devuelve los datos JSON
            "url": " " + base_url + "/Usuarios/getUsuarios",
            "dataSrc": "" // Significa que los datos están en la raíz del JSON
        },
        // Define las columnas que mostrará la tabla
        "columns": [
            {"data": "idpersona"},
            {"data": "nombres"},
            {"data": "apellidos"},
            {"data": "email_user"},
            {"data": "telefono"},
            {"data": "nombrerol"},
            {"data": "status"},
            {"data": "options"} // Aquí irán los botones de acción (editar, eliminar)
        ],
        // Botones de exportación
        'dom': 'lBfrtip',
        'buttons': [
            {
                "extend": "copyHtml5",
                "text": "<i class='far fa-copy'></i> Copiar",
                "titleAttr": "Copiar",
                "className": "btn btn-secondary"
            },{
                "extend": "excelHtml5",
                "text": "<i class='fas fa-file-excel'></i> Excel",
                "titleAttr": "Esportar a Excel",
                "className": "btn btn-success"
            },{
                "extend": "pdfHtml5",
                "text": "<i class='fas fa-file-pdf'></i> PDF",
                "titleAttr": "Esportar a PDF",
                "className": "btn btn-danger"
            },{
                "extend": "csvHtml5",
                "text": "<i class='fas fa-file-csv'></i> CSV",
                "titleAttr": "Esportar a CSV",
                "className": "btn btn-info"
            }
        ],
        "resonsieve": "true",
        "bDestroy": true,          // Permite reinicializar el DataTable
        "iDisplayLength": 10,      // Cantidad de registros por página
        "order": [[0,"desc"]]      // Orden descendente por la primera columna
    });


    // Verifica si existe el formulario con id "formUsuario"
if(document.querySelector("#formUsuario")){

    // Obtiene una referencia al formulario
    let formUsuario = document.querySelector("#formUsuario");

    // Asigna la función que se ejecuta cuando se envía el formulario
    formUsuario.onsubmit = function(e) {
        e.preventDefault(); // Evita que se recargue la página al enviar el formulario

        // Captura los valores de los campos del formulario
        let strIdentificacion = document.querySelector('#txtIdentificacion').value;
        let strNombre = document.querySelector('#txtNombre').value;
        let strApellido = document.querySelector('#txtApellido').value;
        let strEmail = document.querySelector('#txtEmail').value;
        let intTelefono = document.querySelector('#txtTelefono').value;
        let intTipousuario = document.querySelector('#listRolid').value;
        let strPassword = document.querySelector('#txtPassword').value;
        let intStatus = document.querySelector('#listStatus').value;

        // Valida que no haya campos vacíos obligatorios
        if(strIdentificacion == '' || strApellido == '' || strNombre == '' || strEmail == '' || intTelefono == '' || intTipousuario == '')
        {
            swal("Atención", "Todos los campos son obligatorios." , "error");
            return false;
        }

        // Verifica si hay elementos con clase 'valid' que contengan errores
        let elementsValid = document.getElementsByClassName("valid");
        for (let i = 0; i < elementsValid.length; i++) { 
            if(elementsValid[i].classList.contains('is-invalid')) { 
                swal("Atención", "Por favor verifique los campos en rojo." , "error");
                return false;
            } 
        } 

        // Muestra el div de carga (loading)
        divLoading.style.display = "flex";

        // Prepara la solicitud AJAX
        let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
        let ajaxUrl = base_url+'/Usuarios/setUsuario'; // Ruta del backend
        let formData = new FormData(formUsuario); // Crea un objeto FormData con los datos del formulario

        request.open("POST",ajaxUrl,true); // Configura el método POST
        request.send(formData); // Envía los datos al servidor

        // Espera la respuesta del servidor
        request.onreadystatechange = function(){
            if(request.readyState == 4 && request.status == 200){
                let objData = JSON.parse(request.responseText); // Parsea la respuesta

                // Si la respuesta es exitosa
                if(objData.status){
                    // Si no estamos editando una fila específica, recargamos la tabla
                    if(rowTable == ""){
                        tableUsuarios.api().ajax.reload(); // Recarga los datos de la tabla con DataTables
                    } else {
                        // Si estamos editando una fila, actualizamos solo esa fila
                        htmlStatus = intStatus == 1 ? 
                            '<span class="badge badge-success">Activo</span>' : 
                            '<span class="badge badge-danger">Inactivo</span>';

                        rowTable.cells[1].textContent = strNombre;
                        rowTable.cells[2].textContent = strApellido;
                        rowTable.cells[3].textContent = strEmail;
                        rowTable.cells[4].textContent = intTelefono;
                        rowTable.cells[5].textContent = document.querySelector("#listRolid").selectedOptions[0].text;
                        rowTable.cells[6].innerHTML = htmlStatus;

                        rowTable = ""; // Limpia la variable
                    }

                    // Oculta el modal y resetea el formulario
                    $('#modalFormUsuario').modal("hide");
                    formUsuario.reset();
                    swal("Usuarios", objData.msg ,"success"); // Muestra mensaje de éxito
                } else {
                    swal("Error", objData.msg , "error"); // Muestra error si la operación falla
                }
            }

            // Oculta el div de carga
            divLoading.style.display = "none";
            return false;
        }
    }
}
    //Actualizar Perfil
    if(document.querySelector("#formPerfil")){
        let formPerfil = document.querySelector("#formPerfil");
        formPerfil.onsubmit = function(e) {
            e.preventDefault();
            let strIdentificacion = document.querySelector('#txtIdentificacion').value;
            let strNombre = document.querySelector('#txtNombre').value;
            let strApellido = document.querySelector('#txtApellido').value;
            let intTelefono = document.querySelector('#txtTelefono').value;
            let strPassword = document.querySelector('#txtPassword').value;
            let strPasswordConfirm = document.querySelector('#txtPasswordConfirm').value;

            if(strIdentificacion == '' || strApellido == '' || strNombre == '' || intTelefono == '' )
            {
                swal("Atención", "Todos los campos son obligatorios." , "error");
                return false;
            }

            if(strPassword != "" || strPasswordConfirm != "")
            {   
                if( strPassword != strPasswordConfirm ){
                    swal("Atención", "Las contraseñas no son iguales." , "info");
                    return false;
                }           
                if(strPassword.length < 5 ){
                    swal("Atención", "La contraseña debe tener un mínimo de 5 caracteres." , "info");
                    return false;
                }
            }

            let elementsValid = document.getElementsByClassName("valid");
            for (let i = 0; i < elementsValid.length; i++) { 
                if(elementsValid[i].classList.contains('is-invalid')) { 
                    swal("Atención", "Por favor verifique los campos en rojo." , "error");
                    return false;
                } 
            } 
            divLoading.style.display = "flex";
            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = base_url+'/Usuarios/putPerfil'; 
            let formData = new FormData(formPerfil);
            request.open("POST",ajaxUrl,true);
            request.send(formData);
            request.onreadystatechange = function(){
                if(request.readyState != 4 ) return; 
                if(request.status == 200){
                    let objData = JSON.parse(request.responseText);
                    if(objData.status)
                    {
                        $('#modalFormPerfil').modal("hide");
                        swal({
                            title: "",
                            text: objData.msg,
                            type: "success",
                            confirmButtonText: "Aceptar",
                            closeOnConfirm: false,
                        }, function(isConfirm) {
                            if (isConfirm) {
                                location.reload();
                            }
                        });
                    }else{
                        swal("Error", objData.msg , "error");
                    }
                }
                divLoading.style.display = "none";
                return false;
            }
        }
    }
    //Actualizar Datos Fiscales
    if(document.querySelector("#formDataFiscal")){
        let formDataFiscal = document.querySelector("#formDataFiscal");
        formDataFiscal.onsubmit = function(e) {
            e.preventDefault();
            let strNit = document.querySelector('#txtNit').value;
            let strNombreFiscal = document.querySelector('#txtNombreFiscal').value;
            let strDirFiscal = document.querySelector('#txtDirFiscal').value;
           
            if(strNit == '' || strNombreFiscal == '' || strDirFiscal == '' )
            {
                swal("Atención", "Todos los campos son obligatorios." , "error");
                return false;
            }
            divLoading.style.display = "flex";
            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = base_url+'/Usuarios/putDFical'; 
            let formData = new FormData(formDataFiscal);
            request.open("POST",ajaxUrl,true);
            request.send(formData);
            request.onreadystatechange = function(){
                if(request.readyState != 4 ) return; 
                if(request.status == 200){
                    let objData = JSON.parse(request.responseText);
                    if(objData.status)
                    {
                        $('#modalFormPerfil').modal("hide");
                        swal({
                            title: "",
                            text: objData.msg,
                            type: "success",
                            confirmButtonText: "Aceptar",
                            closeOnConfirm: false,
                        }, function(isConfirm) {
                            if (isConfirm) {
                                location.reload();
                            }
                        });
                    }else{
                        swal("Error", objData.msg , "error");
                    }
                }
                divLoading.style.display = "none";
                return false;
            }
        }
    }
}, false);


window.addEventListener('load', function() {
        fntRolesUsuario();
}, false);

function fntRolesUsuario(){
    // Verifica si existe un elemento con ID 'listRolid' (por ejemplo, un <select>)
    if(document.querySelector('#listRolid')){
        // URL del backend que retorna los roles en formato <option>
        let ajaxUrl = base_url+'/Roles/getSelectRoles';

        // Crea un objeto XMLHttpRequest (AJAX) compatible con navegadores antiguos
        let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');

        // Abre una conexión GET a la URL especificada (asíncrona)
        request.open("GET", ajaxUrl, true);

        // Envía la solicitud al servidor
        request.send();

        // Espera la respuesta del servidor
        request.onreadystatechange = function(){
            // readyState 4: petición terminada, status 200: respuesta exitosa
            if(request.readyState == 4 && request.status == 200){
                // Inserta la respuesta (HTML <option>...) dentro del <select> con ID 'listRolid'
                document.querySelector('#listRolid').innerHTML = request.responseText;

                // Refresca el componente select con estilos Bootstrap (selectpicker)
                $('#listRolid').selectpicker('render');
            }
        }
    }
}


function fntViewUsuario(idpersona){
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let ajaxUrl = base_url+'/Usuarios/getUsuario/'+idpersona;
    request.open("GET",ajaxUrl,true);
    request.send();
    request.onreadystatechange = function(){
        if(request.readyState == 4 && request.status == 200){
            let objData = JSON.parse(request.responseText);

            if(objData.status)
            {
               let estadoUsuario = objData.data.status == 1 ? 
                '<span class="badge badge-success">Activo</span>' : 
                '<span class="badge badge-danger">Inactivo</span>';

                document.querySelector("#celIdentificacion").innerHTML = objData.data.identificacion;
                document.querySelector("#celNombre").innerHTML = objData.data.nombres;
                document.querySelector("#celApellido").innerHTML = objData.data.apellidos;
                document.querySelector("#celTelefono").innerHTML = objData.data.telefono;
                document.querySelector("#celEmail").innerHTML = objData.data.email_user;
                document.querySelector("#celTipoUsuario").innerHTML = objData.data.nombrerol;
                document.querySelector("#celEstado").innerHTML = estadoUsuario;
                document.querySelector("#celFechaRegistro").innerHTML = objData.data.fechaRegistro; 
                $('#modalViewUser').modal('show');
            }else{
                swal("Error", objData.msg , "error");
            }
        }
    }
}

function fntEditUsuario(element, idpersona) {
    // 1. Guarda la fila completa de la tabla que contiene el botón presionado.
    rowTable = element.parentNode.parentNode.parentNode;

    // 2. Cambia el título del modal a "Actualizar Usuario".
    document.querySelector('#titleModal').innerHTML = "Actualizar Usuario";

    // 3. Cambia el estilo de la cabecera del modal para reflejar que es una actualización.
    document.querySelector('.modal-header').classList.replace("headerRegister", "headerUpdate");

    // 4. Cambia el color del botón de acción del modal de azul (guardar) a celeste (actualizar).
    document.querySelector('#btnActionForm').classList.replace("btn-primary", "btn-info");

    // 5. Cambia el texto del botón de acción a "Actualizar".
    document.querySelector('#btnText').innerHTML = "Actualizar";

    // 6. Prepara una solicitud AJAX (GET) al servidor para obtener los datos del usuario por ID.
    let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
    let ajaxUrl = base_url + '/Usuarios/getUsuario/' + idpersona;
    request.open("GET", ajaxUrl, true);
    request.send();

    // 7. Espera la respuesta del servidor.
    request.onreadystatechange = function () {

        // 8. Cuando la respuesta está completa y fue exitosa (estado 200):
        if (request.readyState == 4 && request.status == 200) {
            let objData = JSON.parse(request.responseText); // Convierte la respuesta JSON en un objeto JS

            // 9. Si la respuesta tiene status positivo (true):
            if (objData.status) {
                // 10. Llena los campos del formulario con los datos obtenidos del usuario.
                document.querySelector("#idUsuario").value = objData.data.idpersona;
                document.querySelector("#txtIdentificacion").value = objData.data.identificacion;
                document.querySelector("#txtNombre").value = objData.data.nombres;
                document.querySelector("#txtApellido").value = objData.data.apellidos;
                document.querySelector("#txtTelefono").value = objData.data.telefono;
                document.querySelector("#txtEmail").value = objData.data.email_user;

                // 11. Selecciona el rol actual del usuario en el select (con bootstrap-select)
                document.querySelector("#listRolid").value = objData.data.idrol;
                $('#listRolid').selectpicker('render'); // Refresca visualmente el select

                // 12. Selecciona el estado del usuario (1 = activo, 2 = inactivo)
                document.querySelector("#listStatus").value = objData.data.status == 1 ? 1 : 2;
                $('#listStatus').selectpicker('render'); // También refresca visualmente
            }
        }

        // 13. Muestra el modal con el formulario ya lleno para editar.
        $('#modalFormUsuario').modal('show');
    }
}


function fntDelUsuario(idpersona){
    swal({
        title: "Eliminar Usuario",
        text: "¿Realmente quiere eliminar el Usuario?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Si, eliminar!",
        cancelButtonText: "No, cancelar!",
        closeOnConfirm: false,
        closeOnCancel: true
    }, function(isConfirm) {
        
        if (isConfirm) 
        {
            let request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
            let ajaxUrl = base_url+'/Usuarios/delUsuario';
            let strData = "idUsuario="+idpersona;
            request.open("POST",ajaxUrl,true);
            request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            request.send(strData);
            request.onreadystatechange = function(){
                if(request.readyState == 4 && request.status == 200){
                    let objData = JSON.parse(request.responseText);
                    if(objData.status)
                    {
                        swal("Eliminar!", objData.msg , "success");
                        tableUsuarios.api().ajax.reload();
                    }else{
                        swal("Atención!", objData.msg , "error");
                    }
                }
            }
        }

    });

}


function openModal()
{
    document.querySelector('#idUsuario').value ="";
    document.querySelector('.modal-header').classList.replace("headerUpdate", "headerRegister");
    document.querySelector('#btnActionForm').classList.replace("btn-info", "btn-primary");
    document.querySelector('#btnText').innerHTML ="Guardar";
    document.querySelector('#titleModal').innerHTML = "Nuevo Usuario";
    document.querySelector("#formUsuario").reset();
    $('#modalFormUsuario').modal('show');
}

function openModalPerfil(){
    $('#modalFormPerfil').modal('show');
}