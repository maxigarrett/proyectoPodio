// botones de cambiar foto dni
const botonFoto=document.getElementById("btn-foto")
const botonDniFrente=document.getElementById("btn-dni-f")
const botonDniDorso=document.getElementById("btn-dni-d")

const botonCerrarModal=document.getElementById("btn-cerrar");

const form_img=document.getElementById("formIMG")

const abrirModal=(tipo)=>{
    var inputTipo = document.getElementById("input-tipo-imagen");
    inputTipo.setAttribute("value", tipo);
    inputTipo.style.display='none';
    var modal = document.getElementById("modal-container");
    modal.style.display='block';
}

const cerrarModal=()=> {
    const inputFile = document.getElementById("input-foto");
    inputFile.value='';
    
    const modal = document.getElementById("modal-container");
    modal.style.display='none';  
    
    const preview = document.getElementById('vista-previa').firstChild;
    
    if(preview==null || preview!=null)
    {
        preview.remove()
    }
}


// EVENTO CLIC DE BOTONES
botonFoto.addEventListener('click',(e)=>
{
    abrirModal("foto");
})
botonDniFrente.addEventListener('click',(e)=>
{
    abrirModal("dni_f")
})
botonDniDorso.addEventListener('click',(e)=>
{
    abrirModal("dni_d")
})

botonCerrarModal.addEventListener("click",(e)=>
{
    cerrarModal();
})


// RESCATAR IMAGEN 
document.getElementById("input-foto").addEventListener('change',(e)=>
{
    const file = e.target.files[0];
    const fileRead= new FileReader();
    fileRead.readAsDataURL(file);
    fileRead.addEventListener('load',(e)=>
    {
        const vistaPrevia=document.getElementById('vista-previa');
        const img=document.createElement("IMG");//por convencion se usa en mayusculas
        img.setAttribute('src',e.target.result)
        vistaPrevia.innerHTML="";
        vistaPrevia.appendChild(img);
    })
})