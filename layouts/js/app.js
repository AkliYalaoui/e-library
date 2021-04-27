const subNavButton = document.getElementById('sub-nav-button'),
     subNavMenu = document.getElementById('sub-nav-menu'),
    navButton = document.getElementById('nav-button'),
    navMenu = document.getElementById('nav-menu'),
    modal = document.getElementById('modal');

if(modal){
    window.addEventListener('load',()=>modal.classList.add('open'));
    modal.lastElementChild.addEventListener('click',()=>modal.remove());
}
if(navButton && navMenu){
    navButton.addEventListener('click',(e)=>{
        e.stopPropagation();
        navMenu.classList.toggle('open');
    });
    window.addEventListener('click',(e) =>{
        if( ['nav-button','nav-menu'].indexOf(e.target.id) === -1 && navMenu.classList.contains('open')){
            navMenu.classList.remove('open');
        }
    });
}

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