if (document.getElementById('r2')) {
    const resp2 = document.getElementById("r2")
    const resp3 = document.getElementById("r3")
    const resp4 = document.getElementById("r4")

    resp2.addEventListener('click', (e) => {
        abrirModal("responsable2")
    })
    resp3.addEventListener('click', (e) => {
        abrirModal("responsable3")
    })
    resp4.addEventListener('click', (e) => {
        abrirModal("entrenador")
    })
}

const cerrar = document.getElementById("btn-cerrar")

const modal = document.getElementById("modal-container")

const responsable = document.getElementById('responsable')

// INPUT de FORM MODAL-----------------
let dniFiltro = document.getElementById("dniFiltro")
let nombreFiltro = document.getElementById("nombreFiltro")
let apellidoFiltro = document.getElementById("apellidoFiltro")

let allJugadoras = []

// FILTRO QUE LLENA LOS INPUT MODAL
const filtro = () => {
    dniFiltro.value = ""
    nombreFiltro.value = ""
    apellidoFiltro.value = ""

    dniFiltro.addEventListener("keyup", (e) => {
        let buscaDni = dniFiltro.value;
        // console.log(buscaDni)
        if (buscaDni == '') {
            nombreFiltro.value = '';
            apellidoFiltro.value = '';
        } else {
            fetch(`AJAXpersonas.php?dni=${buscaDni}`).then(res => res.json()).then(data => {
                allJugadoras = data
                // console.log(allJugadoras)
                if (allJugadoras.length == 0) {
                    nombreFiltro.value = '';
                    apellidoFiltro.value = '';
                } else {
                    for (jugadoras of allJugadoras) {
                        // console.log(jugadoras.nombres)
                        nombreFiltro.value = jugadoras.nombres;
                        apellidoFiltro.value = jugadoras.apellidos;
                    }
                }
            })
        }
    })
}

function abrirModal(resp_n) {
    modal.style.display = 'block'
    responsable.setAttribute('value', resp_n)
    // DEPENDE DEL RESPONSABLE LLAMA A LA MISMA FUNCION PARA FILTRAR
    if (resp_n == "responsable2") {
        filtro();
    }
    if (resp_n == "responsable3") {
        filtro();
    }
    if (resp_n == "entrenador") {
        filtro();
    }
}

cerrar.addEventListener('click', (e) => {
    modal.style.display = 'none'
    responsable.setAttribute('value', '')
    dniFiltro.value = ''
    nombreFiltro.value = ''
    apellidoFiltro.value = ''

})
