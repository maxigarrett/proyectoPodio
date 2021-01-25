const torneo = document.getElementById('torneo-torneo');
const categoria = document.getElementById('categoria-torneo');
const nombre = document.getElementById('nombre-torneo');
const anio = document.getElementById('anio-torneo');
const inicio = document.getElementById('inicio-torneo');
const fin = document.getElementById('fin-torneo');
const cierre = document.getElementById('cierre-lista-torneo');
const prefijo = document.getElementById('prefijo-torneo');

const selector = document.getElementById('sel');

torneo.addEventListener('keyup', (e) => {
    let filtroTorneo = torneo.value;
    if (filtroTorneo == '') {
        selector.style.display = 'none';
        torneo.value = '';
                categoria.value = '';
                nombre.value = '';
                anio.value = '';
                inicio.value = '';
                fin.value = '';
                cierre.value = '';
                prefijo.value = '';
    } else {
        fetch('AJAXtorneos.php?torneo=' + filtroTorneo).then(res => res.json()).then(data => {
            torneos = data;
            if (torneos.length == 0) {
                selector.style.display = 'none';
            } else {
                selector.style.display = 'block';
                while (selector.firstChild) {
                    selector.firstChild.remove();
                }
                for (let i = 0; i < torneos.length; i++) {
                    let fila = document.createElement('option');
                    fila.value = torneos[i].torneo;
                    fila.innerHTML = torneos[i].torneo;

                    selector.appendChild(fila);
                }
            }
        })
    }
})

selector.addEventListener('change', (e) => {
    torneo_seleccionado = selector.value;
    fetch('AJAXtorneos.php?torneo=' + torneo_seleccionado).then(res => res.json()).then(data => {
        datos = data;
        torneo.value = datos[0].torneo;
        categoria.value = datos[0].categoria;
        nombre.value = datos[0].nombre;
        anio.value = datos[0].anio;
        inicio.value = datos[0].fecha_inicio;
        fin.value = datos[0].fecha_fin;
        cierre.value = datos[0].fecha_cierre_lista_buena_fe;
        prefijo.value = datos[0].prefijo_partidos;
        selector.style.display = 'none';
    })
})