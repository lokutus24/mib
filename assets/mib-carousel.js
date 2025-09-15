// Initialize Swiper carousel for property shortcode and load more items on demand
document.addEventListener('DOMContentLoaded', function () {
    var carousels = document.querySelectorAll('.mib-property-carousel');
    carousels.forEach(function (carousel) {
        var swiper = new Swiper(carousel, {
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

        var shortcode = carousel.dataset.shortcode;
        var perPage = carousel.dataset.apartman_number;
        if (shortcode) {
            carousel.dataset.page = carousel.dataset.page || '1';
            swiper.on('reachEnd', function () {
                if (carousel.dataset.loading === '1' || carousel.dataset.finished === '1') return;

                var nextPage = parseInt(carousel.dataset.page, 10) + 1;
                carousel.dataset.loading = '1';

                fetch(ajaxurl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
                    body: new URLSearchParams({
                        action: 'load_more_items',
                        page: nextPage,
                        shortcode: shortcode,
                        apartman_number: perPage,
                        page_type: 'carousel'
                    })
                })
                    .then(function (response) { return response.json(); })
                    .then(function (data) {
                        carousel.dataset.loading = '0';
                        if (data.success && data.data && data.data.count > 0) {
                            var wrapper = carousel.querySelector('.swiper-wrapper');
                            wrapper.insertAdjacentHTML('beforeend', data.data.html);
                            carousel.dataset.page = nextPage;
                            swiper.update();
                        } else {
                            carousel.dataset.finished = '1';
                        }
                    })
                    .catch(function () {
                        carousel.dataset.loading = '0';
                    });
            });
        }
    });
});