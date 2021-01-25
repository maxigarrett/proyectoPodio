if(document.getElementById('foto1')){
    const foto1 = document.getElementById('foto1');
    
    foto1.addEventListener('click',(e)=>{
        if(foto1.style.width=='440px'){
            foto1.style.width='220px';
            foto1.style.position='relative';
            foto1.style.zIndex='0';
        }else{
            foto1.style.width='440px';
            foto1.style.position='absolute';
            foto1.style.bottom='0';
            foto1.style.zIndex='1';
        }
    })
}
if(document.getElementById('foto2')){
    const foto2 = document.getElementById('foto2');
    
    foto2.addEventListener('click',(e)=>{
        if(foto2.style.width=='440px'){
            foto2.style.width='220px';
            foto2.style.position='relative';
            foto2.style.zIndex='0';
        }else{  
            foto2.style.width='440px';
            foto2.style.position='absolute';
            foto2.style.bottom='0';
            foto2.style.zIndex='1';
        }
    })
}
if(document.getElementById('foto3')){
    const foto3 = document.getElementById('foto3');
    
    foto3.addEventListener('click',(e)=>{
        if(foto3.style.width=='440px'){
            foto3.style.width='220px';
            foto3.style.position='relative';
            foto3.style.zIndex='0';
        }else{
            foto3.style.width='440px';
            foto3.style.position='absolute';
            foto3.style.bottom='0';
            foto3.style.zIndex='1';
        }
    })
}