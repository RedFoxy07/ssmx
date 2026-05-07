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
document.addEventListener('DOMContentLoaded', () => {
    const buscador = document.getElementById('inputBuscador');
    const filtroCategoria = document.getElementById('filtroCategoria');
    const filtroMarca = document.getElementById('filtroMarca');
    const productos = document.querySelectorAll('.tarjeta-producto');
    function filtrarProductos() {
        const texto = buscador ? buscador.value.toLowerCase() : "";
        const categoria = filtroCategoria.value;
        const marca = filtroMarca.value;
        productos.forEach(producto => {        
            const nombre = producto.querySelector('h3').textContent.toLowerCase();
            const descripcion = producto.querySelector('p') ? producto.querySelector('p').textContent.toLowerCase() : "";
            const categoriaProducto = producto.getAttribute('data-categoria');
            const marcaProducto = producto.getAttribute('data-marca');
            const pasaTexto = nombre.includes(texto) || descripcion.includes(texto);
            const pasaCategoria = categoria === 'todas' || categoriaProducto === categoria;
            const pasaMarca = marca === 'todas' || marcaProducto === marca;
            if (pasaTexto && pasaCategoria && pasaMarca) {
                producto.style.display = 'flex';
            } else {
                producto.style.display = 'none';
            }  
        });
    }
    const parametrosURL = new URLSearchParams(window.location.search);
    const categoriaURL = parametrosURL.get('cat');

    if (categoriaURL && filtroCategoria) {
        filtroCategoria.value = categoriaURL;
    }
    filtrarProductos();
        if (buscador) 
        buscador.addEventListener('input', filtrarProductos);
    if (filtroCategoria) 
        filtroCategoria.addEventListener('change', filtrarProductos);
    if (filtroMarca)
        filtroMarca.addEventListener('change', filtrarProductos);
    });