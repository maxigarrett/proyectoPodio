const origen = document.getElementById('torneo-origen');
const tabla = document.getElementById('tabla-import');

origen.addEventListener('change', (e)=>{
    while (tabla.firstChild) {
        tabla.firstChild.remove();
    }
    torneo_origen = origen.value;
    fetch('AJAXequipos.php?torneo='+torneo_origen).then(res=>res.json()).then(data=>{
        let equipos = data;
        
        console.log(equipos);

        for(let i=0; i < equipos.length; i++) {
            let fila = document.createElement('tr')
            let casilla_equipo = document.createElement('td');
            casilla_equipo.innerHTML = equipos[i].nombre_equipo;
            fila.appendChild(casilla_equipo);

            let casilla_importar = document.createElement('td');
            let check_importar = document.createElement('input');
            check_importar.setAttribute('type', 'checkbox');
            check_importar.setAttribute('name', 'equipos[]');
            check_importar.value=equipos[i].nombre_equipo;

            casilla_importar.appendChild(check_importar);
            fila.appendChild(casilla_importar);

            let casilla_lbf = document.createElement('td');
            let check_lbf = document.createElement('input');
            check_lbf.setAttribute('type', 'checkbox');
            check_lbf.setAttribute('name', 'listasBF[]');
            check_lbf.value=equipos[i].nombre_equipo;

            casilla_lbf.appendChild(check_lbf);
            fila.appendChild(casilla_lbf);

            tabla.appendChild(fila);
        }
    })
})