/*!
* Start Bootstrap - Bare v5.0.9 (https://startbootstrap.com/template/bare)
* Copyright 2013-2023 Start Bootstrap
* Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-bare/blob/master/LICENSE)
*/
// This file is intentionally blank
// Use this file to add JavaScript to your project

// const button = document.querySelector('#addVideo')
// console.log(document.querySelector(".btn .btn-primary"))
// button.addEventListener('click',(e)=> {
  //  const form = document.querySelector('.add_item_link')
  // form.addEventListener('click',(e)=>{
  //   e.preventDefault()
  // })

  const addFormToCollection = (e) => {
    const collectionHolder = document.querySelector('.' + e.currentTarget.dataset.collectionHolderClass);
    const item = document.createElement('p');
  
    item.innerHTML = collectionHolder
      .dataset
      .prototype
      .replace(
        /__name__/g,
        collectionHolder.dataset.index
      );
  
    collectionHolder.appendChild(item);
  
    collectionHolder.dataset.index++;
  };
    document.querySelectorAll('.add_item_link')
  .forEach(btn => {
      btn.addEventListener("click", addFormToCollection)
  });
// })
