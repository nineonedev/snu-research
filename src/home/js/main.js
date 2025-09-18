$(document).ready(function () {
  var main_slide = new Swiper('.no-main-slider', {
    autoplay: {
      delay: 5500,
      disableOnInteraction: false,
    },
    pagination: {
      el: '.no-main-slider .swiper-pagination',
      clickable: true,
      renderBullet: function (index, className) {
        return `<span class="${className}"><span class="timer-bar"></span></span>`;
      },
    },
    navigation: {
      nextEl: '.no-main-slider .swiper-button-next',
      prevEl: '.no-main-slider .swiper-button-prev',
    },

    effect: 'fade',
    speed: 1000,
  });

  var ct_slide;

  function initSwiper() {
    if (window.innerWidth <= 1024) {
      if (!ct_slide) {
        ct_slide = new Swiper('.category-nav', {
          freeMode: true,
          slidesPerView: 'auto',
          spaceBetween: 16,
        });
      }
    } else {
      if (ct_slide) {
        ct_slide.destroy();
        ct_slide = null;
      }
    }
  }

  initSwiper();
  window.addEventListener('resize', initSwiper);
});
