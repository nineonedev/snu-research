$(document).ready(function () {
    AOS.init();

    gsap.registerPlugin(ScrollTrigger);

    window.addEventListener("resize", function () {
        ScrollTrigger.refresh();
    });

    const header = document.querySelector(".no-header");

    if (!document.querySelector(".no-visual, .no-search")) {
        header?.classList.add("sub");
    }

    header?.classList.add("loaded");

    // header
    $(window).scroll(function () {
        const scroll = $(window).scrollTop();

        if (scroll > 0) {
            $(".no-header").addClass("active");
        } else if (scroll == 0) {
            $(".no-header").removeClass("active");
        }
    });

    if ($(window).scrollTop() >= 80) {
        $(".no-header").addClass("active");
    }

    //header mobile animation
    const m_btn = $(".no-header__btn");
    const m_line_top = $(".no-header__btn-line");
    const m_menu = $(".no-header__m");
    const m_depth1 = $(".no-header__m--gnb");
    const m_depth1_arrow = $(".no-header__m--gnb--arrow");
    m_btn.click(function () {
        $(this).children(m_line_top).toggleClass("active");
        $(m_menu).toggleClass("active");
        $(m_depth1).find("ul").removeClass("active");
        $(m_depth1).find("p").removeClass("active");
        $(m_depth1).find(m_depth1_arrow).removeClass("active");
        $(".no-header").toggleClass("on");
    });
    m_depth1.click(function (e) {
        e.preventDefault(); 
        $(this).siblings().find("ul").removeClass("active");
        $(this).siblings().find("p").removeClass("active");
        $(this).siblings().find(m_depth1_arrow).removeClass("active");
        $(this).find("ul").toggleClass("active");
        $(this).find("p").toggleClass("active");
        $(this).find(m_depth1_arrow).toggleClass("active");
    });

    $(window).resize(function () {
        $(m_depth1).find("ul").removeClass("active");
        $(m_depth1).find("p").removeClass("active");
        $(m_depth1).find(m_depth1_arrow).removeClass("active");
        $(m_btn).children(m_line_top).removeClass("active");
        $(m_menu).removeClass("active");
        $(".no-header").removeClass("on");
        $(".no-header .search-box").slideUp(600);
        $(".search-dimmed").removeClass("on");
    });

    $(".no-header__opt .search-wrap").on("click", function () {
        const searchBox = $(".no-header .search-box");

        if (searchBox.is(":visible")) {
            searchBox.slideUp(600);
            $(".no-header").removeClass("search");
            $(".search-dimmed").removeClass("on");
        } else {
            searchBox.slideDown(600);
            $(".no-header").addClass("search");
            $(".search-dimmed").addClass("on");
        }
    });

    $(".search-dimmed").click(function () {
        const searchBox = $(".no-header .search-box");

        searchBox.slideUp(600);
        $(".search-dimmed").removeClass("on");
    });

    const elements = document.querySelectorAll(".word-reveal");

    elements.forEach((el) => {
        gsap.fromTo(
            el,
            { opacity: 0, y: 50 },
            {
                opacity: 1,
                y: 0,
                duration: 0.9,
                scrollTrigger: {
                    trigger: el,
                    start: "top 80%",
                },
            }
        );
    });

    if (document.querySelectorAll(".list-show").length > 0) {
        document.querySelectorAll(".list-show").forEach((list) => {
            let items = list.querySelectorAll("li");

            gsap.fromTo(
                items,
                { opacity: 0, y: 75 },
                {
                    opacity: 1,
                    y: 0,
                    duration: 0.9,
                    stagger: 0.2,
                    scrollTrigger: {
                        trigger: list,
                        start: "top 80%",
                        end: "bottom 20%",
                    },
                }
            );
        });
    }

    const images = document.querySelectorAll(".move-img img");

    if (images.length > 0) {
        gsap.set(images, {
            scale: 1.1,
        });

        images.forEach((image) => {
            gsap.fromTo(
                image,
                { yPercent: -5 },
                {
                    yPercent: 5,
                    scrollTrigger: {
                        trigger: image,
                        start: "top bottom",
                        end: "bottom top",
                        scrub: 1,
                    },
                }
            );
        });
    }
});

// check
$(".check").click(function () {
    $(".check-wrap input + label").toggleClass("active");
});

$(".check-wrap a").click(function () {
    $(".form-popup").css({ display: "block" });
    $(".popup-bg").addClass("active");
});

$(".p-close").click(function () {
    $(".form-popup").css({ display: "none" });
    $(".popup-bg").removeClass("active");
    $("html, body").removeClass("lock");
});

// lenis
// let lenis;

// lenis = new Lenis({
//     duration: 2,
//     easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
// });

// function raf(time) {
//     lenis.raf(time);
//     requestAnimationFrame(raf);
// }
// requestAnimationFrame(raf);

$(".top_btn").click(function () {
    $("html, body").animate({ scrollTop: 0 }, 1200);
});

const quickMenu = document.querySelector(".quick_menu");
const topBtn = document.querySelector(".quick_menu .top_btn");

if (topBtn) {
    window.addEventListener("scroll", function () {
        if (window.scrollY > 80) {
            topBtn.classList.add("show");
        } else {
            topBtn.classList.remove("show");
        }
    });

    topBtn.addEventListener("click", function () {
        window.scrollTo({ top: 0, behavior: "smooth" });
    });
}

const footer = document.querySelector("footer");
if (quickMenu) {
    function handleScroll() {
        const footerTop = footer.getBoundingClientRect().top + window.scrollY;
        const quickMenuHeight = quickMenu.offsetHeight;
        const scrollY = window.scrollY;
        const windowHeight = window.innerHeight;

        const quickMenuBottom = windowHeight - quickMenuHeight - 4 * 16;

        if (scrollY + windowHeight >= footerTop) {
            quickMenu.style.position = "absolute";
            quickMenu.style.bottom = `${windowHeight - footerTop + 2 * 16}px`;
        } else {
            quickMenu.style.position = "fixed";
            quickMenu.style.bottom = "4rem";
        }
    }

    window.addEventListener("scroll", handleScroll);
    
    /**
     * .no-post-content 내부의 float 이미지:
     * 1) 래핑하여 flex 정렬로 좌/우 정렬 유지
     * 2) 최초 width를 data-origin-size(px)에 저장하고 창 크기에 따라 100% / 원래 px로 토글
     */
    function initViewImage() {
  const ROOT = document.querySelector('.no-post-content');
  if (!ROOT) return;

  // [변경] float 유/무에 따라 정렬(side) 판단: left/right/center
  const getAlign = (img) => {
    if (img.classList.contains('note-float-left')) return 'left';
    if (img.classList.contains('note-float-right')) return 'right';

    const inlineFloat = (img.style && (img.style.cssFloat || img.style.float)) || '';
    if (inlineFloat === 'left' || inlineFloat === 'right') return inlineFloat;

    const cs = getComputedStyle(img);
    if (cs.float === 'left' || cs.float === 'right') return cs.float;

    // float이 없다면 기본: 가운데 정렬
    return 'center';
  };

  const imgs = Array.from(ROOT.querySelectorAll('img'));

  // 1) 래핑 + 정렬 + 원본 width 보존
  imgs.forEach((img) => {
    // 이미 처리된 경우 스킵
    if (img.closest('[data-view-image-wrapper="1"]')) return;

    const align = getAlign(img); // 'left' | 'right' | 'center'

    const wrapper = document.createElement('div');
    wrapper.setAttribute('data-view-image-wrapper', '1');
    wrapper.style.display = 'flex';
    wrapper.style.width = '100%';
    wrapper.style.gap = '0';
    wrapper.style.flexWrap = 'nowrap';
    wrapper.style.alignItems = 'flex-start';
    wrapper.style.justifyContent =
      align === 'right' ? 'flex-end' :
      align === 'left' ? 'flex-start' : 'center';   // [변경] 가운데 정렬 추가

    // 이미지의 margin을 wrapper로 승격(좌우/상하 모두 유지)
    const cs = getComputedStyle(img);
    const mt = cs.marginTop, mb = cs.marginBottom, ml = cs.marginLeft, mr = cs.marginRight;
    wrapper.style.marginTop = mt;
    wrapper.style.marginBottom = mb;
    wrapper.style.marginLeft = ml;
    wrapper.style.marginRight = mr;

    // 이미지에는 좌우 마진 제거(정렬은 wrapper가 담당)
    img.style.marginLeft = '0';
    img.style.marginRight = '0';

    // float 제거 + 클래스 제거 (부동이었어도 이제 flex로 대체)
    img.style.cssFloat = '';
    img.style.float = '';
    img.classList.remove('note-float-left', 'note-float-right');

    // 원본 width(px) 추출: inline style 우선, 없으면 실제 렌더 너비 사용
    let originPx = 0;
    if (img.style.width && img.style.width.endsWith('px')) {
      originPx = parseInt(img.style.width, 10);
    } else {
      originPx = Math.round(img.getBoundingClientRect().width || img.naturalWidth || 0);
      if (originPx > 0) img.style.width = originPx + 'px';
    }
    if (originPx <= 0 && img.naturalWidth) {
      originPx = img.naturalWidth;
      img.style.width = originPx + 'px';
    }

    // data-origin-size 저장 (px 숫자만)
    if (originPx > 0) {
      img.dataset.originSize = String(originPx);
    }

    // DOM에 래핑 삽입
    const parent = img.parentElement;
    if (parent) {
      parent.insertBefore(wrapper, img);
      wrapper.appendChild(img);
    }

    // 반응형 안전장치
    img.style.height = 'auto';
    img.style.maxWidth = '100%';
  });

  // 2) 리사이즈 핸들러: window.innerWidth < originSize → width:100%, 아니면 원래 px 복원
  const candidates = Array.from(ROOT.querySelectorAll('img[data-origin-size]'));

  const applyResponsiveWidth = () => {
    const viewport = window.innerWidth;
    candidates.forEach((img) => {
      const origin = parseInt(img.dataset.originSize || '0', 10);
      if (!origin) return;

      if (viewport < origin) {
        if (img.style.width !== '100%') {
          img.style.width = '100%';
        }
      } else {
        const px = origin + 'px';
        if (img.style.width !== px) {
          img.style.width = px;
        }
      }
    });
  };

  // 쓰로틀링 (rAF)
  let ticking = false;
  const onResize = () => {
    if (ticking) return;
    ticking = true;
    requestAnimationFrame(() => {
      applyResponsiveWidth();
      ticking = false;
    });
  };

  // 리스너 갱신
  window.removeEventListener('resize', onResize);
  window.addEventListener('resize', onResize, { passive: true });

  // 최초 1회 적용
  applyResponsiveWidth();
}

    initViewImage();
}
