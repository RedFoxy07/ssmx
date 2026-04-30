    window.onload = function() {
        const swiper = new Swiper('.swiper', {
            loop: true,
            observer: true,
            observeParents: true,
            autoplay: {
                delay: 5000,
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
        slidesPerView: 6,      // Cuántos logos se ven a la vez
        spaceBetween: 30,
        loop: true,            // Infinito
        freeMode: true,        // Movimiento fluido, no por "pasos"
        speed: 5000,           // Velocidad constante (5 segundos por vuelta)
        autoplay: {
        delay: 0,          // 0 para que nunca se detenga
        disableOnInteraction: false,
        },
    breakpoints: {
        // Ajuste para celulares (cuando la pantalla es pequeña)
    320: { slidesPerView: 2 },
    768: { slidesPerView: 4 },
    1024: { slidesPerView: 6 }
        }
    });