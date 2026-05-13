window.onload = function() {
    if (document.querySelector('.swiper-hero')) {
        new Swiper('.swiper-hero', {
            loop: true,
            observer: true,
            observeParents: true,
            autoplay: { delay: 4500, disableOnInteraction: false },
            pagination: { el: '.swiper-pagination', clickable: true },
            navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
        });
    }

    if (document.querySelector('.swiper-marcas')) {
        new Swiper('.swiper-marcas', {
            slidesPerView: 2,
            spaceBetween: 30,
            loop: true,
            speed: 8000,
            autoplay: { delay: 0, disableOnInteraction: false, pauseOnMouseEnter: true },
            breakpoints: {
                320: { slidesPerView: 2, spaceBetween: 50},
                768: { slidesPerView: 4, spaceBetween: 50},
                1024: { slidesPerView: 6, spaceBetween: 50}
            }
        });
    }
};

document.addEventListener('DOMContentLoaded', () => {

    const filtroCategoria = document.getElementById('filtroCategoria');
    const filtroMarca = document.getElementById('filtroMarca');
    const buscador = document.getElementById('inputBuscador');
    const productos = document.querySelectorAll('.tarjeta-producto');

    function filtrarProductos() {
        if (!filtroCategoria || !filtroMarca) return;

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

            producto.style.display = (pasaTexto && pasaCategoria && pasaMarca) ? 'flex' : 'none';
        });
    }

    const parametrosURL = new URLSearchParams(window.location.search);
    const categoriaURL = parametrosURL.get('cat');
    if (categoriaURL && filtroCategoria) {
        filtroCategoria.value = categoriaURL;
    }

    filtrarProductos();

    if (buscador) buscador.addEventListener('input', filtrarProductos);
    if (filtroCategoria) filtroCategoria.addEventListener('change', filtrarProductos);
    if (filtroMarca) filtroMarca.addEventListener('change', filtrarProductos);

    const botonesFiltro = document.querySelectorAll('.btn-filtro');
    const seccionesSub = document.querySelectorAll('.seccion-sub');
    const infoGeneral = document.getElementById('info-general-categoria');
    const fotosCasos = document.querySelectorAll('.foto-caso');

    if (botonesFiltro.length > 0) {
        botonesFiltro.forEach(boton => {
            boton.addEventListener('click', () => {
                const objetivo = boton.getAttribute('data-target');
                if (boton.classList.contains('active')) {
                    boton.classList.remove('active');
                    if (infoGeneral) infoGeneral.style.display = '';
                    seccionesSub.forEach(s => s.classList.add('oculta'));
                    fotosCasos.forEach(foto => foto.style.display = 'block');
                } 
                else {
                    if (infoGeneral) infoGeneral.style.display = 'none';
                    botonesFiltro.forEach(b => b.classList.remove('active'));
                    boton.classList.add('active');
                    seccionesSub.forEach(seccion => {
                        if (seccion.id === objetivo) {
                            seccion.classList.remove('oculta');
                        } else {
                            seccion.classList.add('oculta');
                        }
                    });
                    fotosCasos.forEach(foto => {
                        const catFoto = foto.getAttribute('data-categoria');
                        foto.style.display = (catFoto === objetivo) ? 'block' : 'none';
                    });
                }
            });
        });
    }
});
document.addEventListener("DOMContentLoaded", function() {
    const modal = document.getElementById("modal-imagen");
    const imgAmpliada = document.getElementById("img-ampliada");
    const btnCerrar = document.querySelector(".cerrar-modal");
    const fotosGaleria = document.querySelectorAll(".foto-caso");
    fotosGaleria.forEach(foto => {
        foto.addEventListener("click", function() {
            if(modal && imgAmpliada) {
                modal.style.display = "flex";
                imgAmpliada.src = this.src;
            }
        });
    });
    if (btnCerrar) {
        btnCerrar.addEventListener("click", function() {
            modal.style.display = "none";
        });
    }
    if (modal) {
        modal.addEventListener("click", function(event) {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        });
    }
});