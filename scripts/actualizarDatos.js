const btn_leer = document.getElementById('btn-leer');
const modal = document.getElementById('modal-container');
const btn_cerrar = document.getElementById('btn-cerrar');

btn_leer.addEventListener('click',(e)=>{
    modal.style.display='block';
})

btn_cerrar.addEventListener('click',(e)=>{
    modal.style.display='none';
})