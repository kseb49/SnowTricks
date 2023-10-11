const addFormToCollection = (e) => {
	const collectionHolder = document.querySelector('.' + e.currentTarget.dataset.collectionHolderClass)
    if (document.querySelectorAll("div.videos > p").length >= 4) {
		return
    }

    const item = document.createElement("p");
    item.innerHTML = collectionHolder
	.dataset
	.prototype
	.replace(/__name__/g, collectionHolder.dataset.index )
	collectionHolder.appendChild(item);
	collectionHolder.dataset.index++;
}

document.querySelectorAll(".add_item_link").forEach(btn => {
    btn.addEventListener("click", addFormToCollection)
});



const targets = document.querySelectorAll(".d-none")
const button = document.querySelector("#hide")
const classes = ["d-none", "d-sm-block"]

responsive(classes)

window.addEventListener("resize", (e) => {
  responsive(classes)
})

/**
 * Show the media on click
 * @param {*} classes
 */
function responsive (classes) {
    if (button !== null) {
        if (window.innerWidth <= 575) {
            if (button.classList.value.search(/hide/g) != -1) {
                button.classList.remove('hide')
            }
            button.addEventListener('click', (e) => {
                targets.forEach((target) => {
                if (target.classList.value.search(/d-none/g) != -1) {
                    target.classList.remove(...classes)
                }
                button.classList.add('hide')
                })
            })
        }
        else {
            if (button.classList.value.search(/hide/g) == -1) {
                button.classList.add('hide')
            }
            targets.forEach((target) => {
                if (target.classList.value.search(/d-none/g) == -1) {
                    target.classList.add(...classes)
                }
            })
        }
    }
}

/**
 * Get down to the tricks by clicking the arrow
 */
const headerHeight = document.querySelector(".subnav")
const navToAdd = document.querySelector(".navbar")
if (headerHeight !== null && navToAdd !== null) {
    const height = headerHeight.clientHeight
    const add = navToAdd.clientHeight
    const arrow = document.querySelector(".arrow")
    const total = height+add
    if(arrow !== null) {
        arrow.addEventListener("click", (e) => {
            e.preventDefault();
            this.show(total)
        })
    }
    function show(_height){
        window.scroll({
            top: _height,
            behavior: "smooth",
    })
    }
}
/**
 * Go straight up to the top of the window
 */
const up = document.querySelector(".up")
if(up !== null) {
    up.addEventListener("click", () => {
        window.scroll(0, 0)
    })
}

