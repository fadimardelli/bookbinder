document.addEventListener("DOMContentLoaded", () => {
    document.querySelector(".own-carousel__container").ownCarousel({
        itemPerRow:4,
        itemWidth:24,
        responsive: {
            1000: [4, 24],
            800: [3, 33],
            600: [2, 49],
            400: [1, 100]
        },
        loop:true,
        autoplay:3000,
    });
    responsive();
})