 var closeBtn = document.querySelectorAll('.js-close');

 for (let i = 0; i < closeBtn.length; i++) {
     closeBtn[i].addEventListener("click", function(){
         var target = document.querySelector('#'+closeBtn[i].dataset.target);
         target.classList.toggle('d-none')
     })
 }