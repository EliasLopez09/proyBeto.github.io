// Función para controlar qué teclas puede presionar el usuario en un campo de entrada
function controlTag(e) {
    // Obtiene el código de la tecla presionada, compatible con navegadores antiguos (document.all para IE) y modernos
    tecla = (document.all) ? e.keyCode : e.which;

    // Permite la tecla de retroceso (backspace, código 8)
    if (tecla == 8) return true; 

    // Permite teclas de control como Tab (código 9) y otras teclas de navegación (código 0)
    else if (tecla == 0 || tecla == 9) return true;

    // Define un patrón para aceptar solo números (0-9) y espacios
    patron = /[0-9\s]/;

    // Convierte el código de la tecla a su carácter correspondiente (ejemplo: tecla 65 -> 'A')
    n = String.fromCharCode(tecla);

    // Verifica si el carácter ingresado coincide con el patrón (números o espacio)
    return patron.test(n); 
}

// Función para validar que un texto contenga solo letras (a-z, A-Z, Ñ, acentos) y espacios
function testText(txtString) {
    // Define un patrón para aceptar solo letras (mayúsculas, minúsculas, Ñ, acentos) y espacios
    var stringText = new RegExp(/^[a-zA-ZÑñÁáÉéÍíÓóÚúÜü\s]+$/);

    // Verifica si el texto cumple con el patrón
    if (stringText.test(txtString)) {
        return true; // Texto válido
    } else {
        return false; // Texto no válido
    }
}

// Función para validar que un texto contenga solo números enteros (0-9)
function testEntero(intCant) {
    // Define un patrón para aceptar solo números (0-9), sin signos ni decimales
    var intCantidad = new RegExp(/^([0-9])*$/);

    // Verifica si el texto cumple con el patrón
    if (intCantidad.test(intCant)) {
        return true; // Número válido
    } else {
        return false; // Número no válido
    }
}

// Función para validar un correo electrónico
function fntEmailValidate(email) {
    // Define un patrón para validar correos electrónicos (formato: algo@dominio.com)
    var stringEmail = new RegExp(/^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/);

    // Verifica si el correo cumple con el patrón
    if (stringEmail.test(email) == false) {
        return false; // Correo no válido
    } else {
        return true; // Correo válido
    }
}

// Función para validar campos de texto con la clase "validText"
function fntValidText() {
    // Selecciona todos los elementos con la clase "validText"
    let validText = document.querySelectorAll(".validText");

    // Itera sobre cada elemento con la clase "validText"
    validText.forEach(function(validText) {
        // Agrega un evento "keyup" para validar cada vez que el usuario escribe
        validText.addEventListener('keyup', function() {
            let inputValue = this.value; // Obtiene el valor del campo

            // Valida el texto usando testText()
            if (!testText(inputValue)) {
                // Si no es válido, agrega la clase "is-invalid" para resaltar el error (puede usarse con Bootstrap)
                this.classList.add('is-invalid');
            } else {
                // Si es válido, elimina la clase "is-invalid"
                this.classList.remove('is-invalid');
            }				
        });
    });
}

// Función para validar campos numéricos con la clase "validNumber"
function fntValidNumber() {
    // Selecciona todos los elementos con la clase "validNumber"
    let validNumber = document.querySelectorAll(".validNumber");

    // Itera sobre cada elemento con la clase "validNumber"
    validNumber.forEach(function(validNumber) {
        // Agrega un evento "keyup" para validar cada vez que el usuario escribe
        validNumber.addEventListener('keyup', function() {
            let inputValue = this.value; // Obtiene el valor del campo

            // Valida el número usando testEntero()
            if (!testEntero(inputValue)) {
                // Si no es válido, agrega la clase "is-invalid"
                this.classList.add('is-invalid');
            } else {
                // Si es válido, elimina la clase "is-invalid"
                this.classList.remove('is-invalid');
            }				
        });
    });
}

// Función para validar campos de correo con la clase "validEmail"
function fntValidEmail() {
    // Selecciona todos los elementos con la clase "validEmail"
    let validEmail = document.querySelectorAll(".validEmail");

    // Itera sobre cada elemento con la clase "validEmail"
    validEmail.forEach(function(validEmail) {
        // Agrega un evento "keyup" para validar cada vez que el usuario escribe
        validEmail.addEventListener('keyup', function() {
            let inputValue = this.value; // Obtiene el valor del campo

            // Valida el correo usando fntEmailValidate()
            if (!fntEmailValidate(inputValue)) {
                // Si no es válido, agrega la clase "is-invalid"
                this.classList.add('is-invalid');
            } else {
                // Si es válido, elimina la clase "is-invalid"
                this.classList.remove('is-invalid');
            }				
        });
    });
}

// Ejecuta las funciones de validación cuando la página ha cargado completamente
window.addEventListener('load', function() {
    // Llama a las funciones para inicializar las validaciones
    fntValidText(); // Valida campos de texto
    fntValidEmail(); // Valida campos de correo
    fntValidNumber(); // Valida campos numéricos
}, false);