// Al hacer clic en un elemento con data-toggle="flip" dentro de .login-content,
// se alterna la clase "flipped" en .login-box para mostrar u ocultar el formulario de recuperación
$('.login-content [data-toggle="flip"]').click(function() {
    $('.login-box').toggleClass('flipped'); // Cambia la clase para mostrar otra vista del formulario
    return false; // Evita que el enlace recargue la página
});

// Referencia al elemento de carga (spinner o loader)
var divLoading = document.querySelector("#divLoading");

// Cuando el contenido de la página ya está completamente cargado
document.addEventListener('DOMContentLoaded', function() {

    // --- Validación del formulario de inicio de sesión ---
    if (document.querySelector("#formLogin")) {
        let formLogin = document.querySelector("#formLogin");

        formLogin.onsubmit = function(e) {
            e.preventDefault(); // Evita recargar la página al enviar el formulario

            let strEmail = document.querySelector('#txtEmail').value;
            let strPassword = document.querySelector('#txtPassword').value;

            if (strEmail == "" || strPassword == "") {
                swal("Por favor", "Escribe usuario y contraseña.", "error");
                return false;
            } else {
                divLoading.style.display = "flex"; // Muestra el loader

                var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
                var ajaxUrl = base_url + '/Login/loginUser'; // Ruta al controlador en el backend
                var formData = new FormData(formLogin); // Obtiene los datos del formulario
                request.open("POST", ajaxUrl, true);
                console.log("fura del");
                request.send(formData);
                
                request.onreadystatechange = function() {

                    if (request.readyState != 4) return;

                    if (request.status == 200) {
                        var objData = JSON.parse(request.responseText);
                        if (objData.status) {
                            window.location = base_url + '/dashboard'; // Redirige si login es correcto
                        } else {
                            swal("Atenciónn", objData.msg, "error");
                            document.querySelector('#txtPassword').value = "";
                        }
                    } else {
                        swal("Atenciónn", "Error en el proceso", "error");
                    }
                    divLoading.style.display = "none"; // Oculta el loader
                    return false;
                }
            }
        }
    }

    // --- Validación del formulario de recuperación de contraseña ---
    if (document.querySelector("#formRecetPass")) {
        let formRecetPass = document.querySelector("#formRecetPass");

        formRecetPass.onsubmit = function(e) {
            e.preventDefault(); // Evita recargar la página

            let strEmail = document.querySelector('#txtEmailReset').value;

            if (strEmail == "") {
                swal("Por favor", "Escribe tu correo electrónico.", "error");
                return false;
            } else {
                divLoading.style.display = "flex"; // Muestra el loader

                var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
                var ajaxUrl = base_url + '/Login/resetPass';
                var formData = new FormData(formRecetPass);
                request.open("POST", ajaxUrl, true);
                request.send(formData);

                request.onreadystatechange = function() {
                    if (request.readyState != 4) return;

                    if (request.status == 200) {
                        var objData = JSON.parse(request.responseText);
                        if (objData.status) {
                            swal({
                                title: "",
                                text: objData.msg,
                                type: "success",
                                confirmButtonText: "Aceptar",
                                closeOnConfirm: false,
                            }, function(isConfirm) {
                                if (isConfirm) {
                                    window.location = base_url; // Vuelve al inicio
                                }
                            });
                        } else {
                            swal("Atención", objData.msg, "error");
                        }
                    } else {
                        swal("Atención", "Error en el proceso", "error");
                    }
                    divLoading.style.display = "none"; // Oculta el loader
                    return false;
                }
            }
        }
    }

    // --- Validación del formulario de cambio de contraseña ---
    if (document.querySelector("#formCambiarPass")) {
        let formCambiarPass = document.querySelector("#formCambiarPass");

        formCambiarPass.onsubmit = function(e) {
            e.preventDefault(); // Evita recargar la página

            let strPassword = document.querySelector('#txtPassword').value;
            let strPasswordConfirm = document.querySelector('#txtPasswordConfirm').value;
            let idUsuario = document.querySelector('#idUsuario').value;

            if (strPassword == "" || strPasswordConfirm == "") {
                swal("Por favor", "Escribe la nueva contraseña.", "error");
                return false;
            } else {
                if (strPassword.length < 5) {
                    swal("Atención", "La contraseña debe tener un mínimo de 5 caracteres.", "info");
                    return false;
                }
                if (strPassword != strPasswordConfirm) {
                    swal("Atención", "Las contraseñas no son iguales.", "error");
                    return false;
                }

                divLoading.style.display = "flex"; // Muestra el loader

                var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
                var ajaxUrl = base_url + '/Login/setPassword';
                var formData = new FormData(formCambiarPass);
                request.open("POST", ajaxUrl, true);
                request.send(formData);

                request.onreadystatechange = function() {
                    if (request.readyState != 4) return;

                    if (request.status == 200) {
                        var objData = JSON.parse(request.responseText);
                        if (objData.status) {
                            swal({
                                title: "",
                                text: objData.msg,
                                type: "success",
                                confirmButtonText: "Iniciar sesión",
                                closeOnConfirm: false,
                            }, function(isConfirm) {
                                if (isConfirm) {
                                    window.location = base_url + '/login'; // Redirige al login
                                }
                            });
                        } else {
                            swal("Atención", objData.msg, "error");
                        }
                    } else {
                        swal("Atención", "Error en el proceso", "error");
                    }
                    divLoading.style.display = "none"; // Oculta el loader
                }
            }
        }
    }

}, false);
