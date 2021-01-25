// si existe el cartel de confirmacion, dar la posibilidad de cerrarlo
if (document.getElementById('confirmacion')) {
    document.getElementById('cerrar-confirmacion').addEventListener('click', () => {
        document.getElementById('confirmacion').style.display = 'none'
    })
}

// si existe el cartel de error, dar la posibilidad de cerrarlo
if (document.getElementById('errores')) {
    document.getElementById('cerrar-error').addEventListener('click', () => {
        document.getElementById('errores').style.display = 'none'
    })
}