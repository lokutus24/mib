// Initialize Swiper carousel for property shortcode
document.addEventListener('DOMContentLoaded', function () {
    var carousels = document.querySelectorAll('.mib-property-carousel');
    carousels.forEach(function (carousel) {
        new Swiper(carousel, {
            slidesPerView: 1,
            slidesPerGroup: 1, // mobilon 1-et lapoz
            spaceBetween: 20,
            navigation: {
                nextEl: carousel.querySelector('.swiper-button-next'),
                prevEl: carousel.querySelector('.swiper-button-prev')
            },
            pagination: {
                el: carousel.querySelector('.swiper-pagination'),
                clickable: true
            },
            breakpoints: {
                768: {
                    slidesPerView: 4,
                    slidesPerGroup: 4 // asztalon 4-et lapoz egyszerre
                }
            }
        });
    });
});