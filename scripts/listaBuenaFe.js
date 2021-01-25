if (document.getElementById('btn-agregar-jugadora')) {
    const agregar = document.getElementById('btn-agregar-jugadora');

    agregar.addEventListener('click', (e) => {
        abrirModal();
    })
}
const cerrar = document.getElementById('btn-cerrar');
const modal = document.getElementById('modal-container');

// inputs del modal
const dniFiltro = document.getElementById('dniFiltro');
const nombreFiltro = document.getElementById('nombreFiltro');
const apellidoFiltro = document.getElementById('apellidoFiltro');
const emailFiltro = document.getElementById('emailFiltro');
const fechaFiltro = document.getElementById('fechaFiltro');
const selector = document.getElementById('sel'); 

// nuevo lista fiiltro
dniFiltro.addEventListener('keyup', (e)=>{
    let dni = dniFiltro.value;
    if (dni == ''){
        selector.style.display = 'none';
    }else{
        fetch('AJAXpersonas.php?dni='+dni).then(res=>res.json()).then(data=>{
            allJugadoras = data;
            if(allJugadoras.length==0){
                selector.style.display='none';
            }else{
                selector.style.display='block';
                while(selector.firstChild){
                    selector.firstChild.remove();
                }
                for(let i=0; i<allJugadoras.length;i++){
                    let fila = document.createElement('option');
                    fila.value = allJugadoras[i].documento;
                    fila.innerHTML = allJugadoras[i].documento+' - '+allJugadoras[i].apellidos+', '+allJugadoras[i].nombres;

                    selector.appendChild(fila);
                }
            }
        })
    }
})
selector.addEventListener('change', (e)=>{
    dni = selector.value;
    fetch('AJAXpersonas.php?dni='+dni).then(res=>res.json()).then(data=>{
        datos = data;
        dniFiltro.value = datos[0].documento;
        nombreFiltro.value = datos[0].nombres;
        apellidoFiltro.value = datos[0].apellidos;
        emailFiltro.value = datos[0].correo_electronico;
        fechaFiltro.value = datos[0].fecha_nacimiento;
        selector.style.display = 'none';
    })
})

const abrirModal = () => {
    modal.style.display = 'block';
}

cerrar.addEventListener('click', (e) => {
    modal.style.display = 'none';
    dniFiltro.value = '';
    nombreFiltro.value = '';
    apellidoFiltro.value = '';
})