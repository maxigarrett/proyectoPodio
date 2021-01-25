const dniDelegada = document.getElementById('dniDelegada');
const selector = document.getElementById('selector-delegada');

dniDelegada.addEventListener('keyup', (e)=>{
    let dniFiltro = dniDelegada.value;
    if (dniFiltro == '') {
        selector.style.display='none';
    } else {
        fetch('AJAXpersonas.php?dni='+dniFiltro).then(res=>res.json()).then(data=>{
            dataPersonas = data;
            if(dataPersonas.length==0){
                selector.style.display='none';
            }else{
                selector.style.display='block';
                while(selector.firstChild){
                    selector.firstChild.remove();
                }
                for(let i=0; i<dataPersonas.length;i++){
                    let fila = document.createElement('option');
                    fila.value = dataPersonas[i].documento;
                    fila.innerHTML = dataPersonas[i].documento+' - '+dataPersonas[i].apellidos+', '+dataPersonas[i].nombres;

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
        dniDelegada.value = datos[0].documento;
        selector.style.display = 'none';
    })
})