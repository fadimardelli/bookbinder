let star_btns = document.querySelector('.rating_star_container').querySelectorAll('button');
let ratings = [];
let elementIds = [];
let ratingInput = document.getElementById('form_rating');
let submitted = false;
let selected_index = -1;
let submitButton = document.getElementById('form_save');

star_btns.forEach((btn, index) => {
    btn.addEventListener('mousedown', () => {
        submitted = true;
        selected_index = index;
        ratingInput.value = selected_index + 1; // Set the selected rating value
        console.log('Selected rating:', ratingInput.value);
        star_btns.forEach(btn2 => {
            btn2.innerHTML = '<i class="fa-regular fa-star"></i>';
        });
        for (let i = index; i >= 0; i--) {
            star_btns[i].innerHTML = '<i class="fa-solid fa-star"></i>';
        }
    });

    btn.addEventListener('mouseenter', () => {
        star_btns.forEach(btn2 => {
            btn2.innerHTML = '<i class="fa-regular fa-star"></i>';
        });
        for (let i = index; i >= 0; i--) {
            star_btns[i].innerHTML = '<i class="fa-solid fa-star"></i>';
        }
    });

    btn.addEventListener('mouseleave', () => {
        if (!submitted) {
            star_btns.forEach(btn2 => {
                btn2.innerHTML = '<i class="fa-regular fa-star"></i>';
            });
        } else {
            star_btns.forEach(btn2 => {
                btn2.innerHTML = '<i class="fa-regular fa-star"></i>';
            });
            for (let i = selected_index; i >= 0; i--) {
                star_btns[i].innerHTML = '<i class="fa-solid fa-star"></i>';
            }
        }
    });
});

function displayRatingStars(rating, sectionId) {
    let ratingStars = document.getElementById(sectionId);

    ratingStars.innerHTML = '';

    let fullStars = Math.floor(rating);

    for (let i = 0; i < fullStars; i++) {
        let li = document.createElement("li");
        li.innerHTML = '<i class="fa-solid fa-star"></i>';
        ratingStars.appendChild(li);
    }

    if (rating % 1 !== 0) {
        let li = document.createElement("li");
        li.innerHTML = '<i class="fa-solid fa-star-half-stroke"></i>';
        ratingStars.appendChild(li);
        fullStars++;
    }

    for (let i = fullStars; i < 5; i++) {
        let li = document.createElement("li");
        li.innerHTML = '<i class="fa-regular fa-star"></i>';
        ratingStars.appendChild(li);
    }
}

