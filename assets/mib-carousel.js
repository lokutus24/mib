document.addEventListener('DOMContentLoaded', function () {
  var carousels = document.querySelectorAll('.mib-carousel-outer');

  carousels.forEach(function (outer) {
    var carousel = outer.querySelector('.mib-property-carousel');

    var swiper = new Swiper(carousel, {
      slidesPerView: 1,
      slidesPerGroup: 1,
      spaceBetween: 20,
      navigation: {
        nextEl: outer.querySelector('.swiper-button-next'),
        prevEl: outer.querySelector('.swiper-button-prev')
      },
      pagination: {
        el: outer.querySelector('.swiper-pagination'),
        clickable: true
      },
      breakpoints: {
        768: { slidesPerView: 4, slidesPerGroup: 4 }
      }
    });

    // Dinamikus betöltés (változatlan logika):
    var shortcode = outer.dataset.shortcode;
    var perPage   = outer.dataset.apartman_number;

    if (shortcode) {
      outer.dataset.page = outer.dataset.page || '1';

      swiper.on('reachEnd', function () {
        if (outer.dataset.loading === '1' || outer.dataset.finished === '1') return;

        var nextPage = parseInt(outer.dataset.page, 10) + 1;
        outer.dataset.loading = '1';

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
          outer.dataset.loading = '0';
          if (data.success && data.data && data.data.count > 0) {
            var wrapper = carousel.querySelector('.swiper-wrapper');
            wrapper.insertAdjacentHTML('beforeend', data.data.html);
            outer.dataset.page = nextPage;
            swiper.update();
            swiper.navigation.update();
            swiper.pagination.update();
          } else {
            outer.dataset.finished = '1';
          }
        })
        .catch(function () {
          outer.dataset.loading = '0';
        });
      });
    }
  });
});