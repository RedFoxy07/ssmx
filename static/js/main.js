window.onload = function() {
    if (document.querySelector('.swiper-hero')) {
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
    }

    if (document.querySelector('.swiper-marcas')) {
        const swiperMarcas = new Swiper('.swiper-marcas', {
            slidesPerView: 2,
            spaceBetween: 30,
            loop: true,
            speed: 8000,
            autoplay: {
                delay: 0,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
            },
            breakpoints: {
                320: { slidesPerView: 2, spaceBetween: 50},
                768: { slidesPerView: 4, spaceBetween: 50},
                1024: { slidesPerView: 6, spaceBetween: 50}
            }
        });
    }
};
