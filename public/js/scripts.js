const addFormToCollection = (e) => {
    const collectionHolder = document.querySelector('.' + e.currentTarget.dataset.collectionHolderClass);
    if( document.querySelectorAll('div.videos > p').length > 4){
      return
    }
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

  /**
   * Get down to the tricks by clicking the arrow
   */
    const height = document.querySelector('.subnav').clientHeight;
    const add = document.querySelector('.navbar').clientHeight;
    const arrow = document.querySelector('.arrow')
    const total = height+add
    arrow.addEventListener('click', (e) => {
      e.preventDefault();
      this.show(total)
    })
    function show(_height){
      window.scroll({
        top: _height,
        behavior: "smooth",
      });
    }


  const width = window.innerWidth
  const targets = document.querySelectorAll('.d-none')
  const button = document.querySelector('.hide')
  const classes = ['d-none', "d-sm-block"]
  responsive(classes)
  window.addEventListener('resize', (e)=>{
    responsive(classes)
  })
  function responsive (classes) {
    if (width <= 575) {
      if (button.classList.value.search(/hide/g) != -1) {
        button.classList.remove('hide')
      }
      button.addEventListener('click', (e) => {
        targets.forEach((target) => {
          if (target.classList.value.search(/d-none/g) != -1) {
            target.classList.remove(...classes)
          }
        })
      })
    }
    else{
      if (button.classList.value.search(/hide/g) == -1) {
          button.classList.add('hide')
      }
      button.addEventListener('click', (e) => {
        targets.forEach((target) => {
          if (target.classList.value.search(/d-none/g) == -1) {
            target.classList.add(...classes)
          }
        })
      })
    }
  }
