    window.onload = function() {
        const swiper = new Swiper('.swiper-hero', {
            loop: true,
            observer: true,
            observeParents: true,
            autoplay: {
                delay: 4000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
        });
    };

    const swiperMarcas = new Swiper('.swiper-marcas', {
        slidesPerView: 2,      // Cuántos logos se ven a la vez
        spaceBetween: 30,
        loop: true,
        freeMode: false, 
        speed: 8000,           // 8 secs
        autoplay: {
        delay: 0,
        disableOnInteraction: false,
        pauseOnMouseEnter: true,
        allowTouchMove: true,
        enabled: true,
        sticky: true,
        momentum: false,
        },
        preventClicks: true,
        preventClicksPropagation: true,
    breakpoints: {
        // Ajuste para celulares
    320: { slidesPerView: 2, spaceBetween: 50},
    768: { slidesPerView: 4, spaceBetween: 50},
    1024: { slidesPerView: 6, spaceBetween: 50}
        }
    });