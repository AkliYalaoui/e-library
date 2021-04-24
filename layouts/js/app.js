const subNavButton = document.getElementById('sub-nav-button'),
     subNavMenu = document.getElementById('sub-nav-menu');

if(subNavButton && subNavMenu){
    subNavButton.addEventListener('click',(e)=>{
        e.stopPropagation();
        subNavMenu.classList.toggle('open');
    });
    window.addEventListener('click',(e) =>{
        if( ['sub-nav-button','sub-nav-menu'].indexOf(e.target.id) === -1 && subNavMenu.classList.contains('open')){
            subNavMenu.classList.remove('open');
        }
    });
}