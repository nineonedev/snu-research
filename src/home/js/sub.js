$(document).ready(function () {
  var nav_slide = new Swiper('.page-nav-list', {
    freeMode: true,
    slidesPerView: 'auto',
    slideToClickedSlide: false,
    centeredSlides: false,
    preventClicks: true,
    preventClicksPropagation: true,
  });

  document.querySelectorAll('.page-nav-list a').forEach((link) => {
    link.addEventListener('mousedown', function (event) {
      event.preventDefault();
    });
  });

  var team_slide = new Swiper('.team-nav', {
    freeMode: true,
    slidesPerView: 'auto',
    slideToClickedSlide: false,
    centeredSlides: false,
    preventClicks: true,
    preventClicksPropagation: true,
  });

  document.querySelectorAll('.team-nav a').forEach((link) => {
    link.addEventListener('mousedown', function (event) {
      event.preventDefault();
    });
  });
});

if (document.querySelectorAll('.about-intro figure').length > 0) {
  gsap.to('.about-intro figure', {
    scrollTrigger: {
      trigger: '.about-intro figure',
      start: 'top 80%',
      end: 'bottom 20%',
      toggleClass: { targets: '.center-ani', className: 'center-show' },
      once: true,
      onEnter: () => {
        gsap.fromTo(
          '.about-intro hgroup h2',
          { opacity: 0, y: 50 },
          { opacity: 1, y: 0, duration: 0.8, ease: 'power2.out', delay: 0.6 }
        );
        gsap.fromTo(
          '.about-intro hgroup p',
          { opacity: 0, y: 50 },
          { opacity: 1, y: 0, duration: 0.8, ease: 'power2.out', delay: 0.9 }
        );
      },
    },
  });
}
