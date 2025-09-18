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
    m_depth1.click(function () {
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
}
