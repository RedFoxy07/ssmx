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
            spaceBetween: 20,
            loop: true,
            speed: 8000,
            autoplay: { delay: 0, disableOnInteraction: false, pauseOnMouseEnter: true },
            breakpoints: {
                640: { slidesPerView: 3, spaceBetween: 20},
                768: { slidesPerView: 4, spaceBetween: 30},
                1024: { slidesPerView: 6, spaceBetween: 30}
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
    let animandose = false;
    if (botonesFiltro.length > 0) {
        botonesFiltro.forEach(boton => {
            boton.addEventListener('click', () => {
                if (animandose) return;
                const objetivo = boton.getAttribute('data-target');
                const estaActivo = boton.classList.contains('active');
                if (estaActivo) {
                    animandose = true;
                    boton.classList.remove('active');
                    seccionesSub.forEach(s => s.classList.add('oculta'));

                    setTimeout(() => {
                        if (infoGeneral) infoGeneral.style.display = '';
                        fotosCasos.forEach(foto => foto.style.display = 'block');
                        animandose = false;
                    }, 500);
                } else {
                    animandose = true;
                    const seccionActual = document.querySelector('.seccion-sub:not(.oculta)');
                    if (seccionActual) {
                        seccionActual.classList.add('oculta');
                    }
                    setTimeout(() => {
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

                        animandose = false;
                    }, 500);
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
    document.addEventListener('DOMContentLoaded', function() {
        const botones = document.querySelectorAll('.boton-categoria');
        botones.forEach(function(boton) {
            boton.addEventListener('click', function() {
                
                const contenido = this.nextElementSibling;
                const icono = this.querySelector('.icono');

                if (contenido.style.maxHeight) {
                    contenido.style.maxHeight = null;
                    icono.textContent = '+';
                } else {
                    contenido.style.maxHeight = contenido.scrollHeight + "px";
                    icono.textContent = '-';
                }
            });
        });
    
    });
document.addEventListener('DOMContentLoaded', function() {
    const bolitas = document.querySelectorAll('.contenedor-bolita');
    bolitas.forEach(bolita => {
        bolita.addEventListener('click', function(e) {
            if(window.matchMedia("(hover: hover)").matches) {
                return;
            }
        const estaAbierta = this.classList.contains('activo');
        bolitas.forEach(b => b.classList.remove('activo'));
        if (!estaAbierta) {
            e.preventDefault();
            this.classList.add('activo');
        }
        });
    });
});
document.addEventListener('click', function(e) {
    if (!e.target.closest('.contenedor-bolita')) {
        bolitas.forEach(b => b.classList.remove('activo'));
    }
});
let carrito = [];
function agregarAlKit(nombre, precio, boton) {
    const tarjeta = boton.closest('.tarjeta-producto');
    if (!tarjeta) return;
    const inputCantidad = tarjeta.querySelector('.input-cantidad');
    let cantidadSeleccionada = 1;
    
    if (inputCantidad) {
        cantidadSeleccionada = parseInt(inputCantidad.value) || 1;
    }
    let precioLimpio = parseFloat(precio);
    if (isNaN(precioLimpio)) {
        precioLimpio = parseFloat(
            String(precio)
                .replace(/[^\d.,]/g, '')
                .replace(',', '.')
        ) || 0;
    }
    console.log('Agregando:', { nombre, precio: precioLimpio, cantidad: cantidadSeleccionada });
    const productoExistente = carrito.find(item => item.nombre === nombre);
    if (productoExistente) {
        productoExistente.cantidad += cantidadSeleccionada;
    } else {
        carrito.push({ 
            nombre: nombre, 
            precio: precioLimpio, 
            cantidad: cantidadSeleccionada 
        });
    }
    if (inputCantidad) {
        inputCantidad.value = 1;
    }
    actualizarCarritoUI();
    abrirCarrito();
}
if (boton){
    const textOriginal = boton.textContent;
    boton.textContent = "Agregado";
    boton.style.backgroundColor = '#28a745';
    boton.style.color = '#ffffff';
    boton.disabled = false;
    setTimeout(() => {
        boton.textContent = textOriginal;
        boton.style.backgroundColor = '';
        boton.style.color = '';
        boton.disabled = false;
    }, 2000);
}
function actualizarCarritoUI() {
    const lista = document.getElementById('listaCarrito');
    const totalEl = document.getElementById('total-precio');
    const contadorEl = document.getElementById('contador-items');
    const inputSubtotalEl = document.getElementById('input_subtotal');
    const inputJsonEl = document.getElementById('input_json');
    
    if (!lista || !totalEl) return;
    
    lista.innerHTML = '';
    let total = 0;
    let cantidadTotal = 0;

    carrito.forEach((item, index) => {
        const subtotalItem = item.precio * item.cantidad;
        total += subtotalItem;
        cantidadTotal += item.cantidad;
        
        lista.innerHTML += `
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; padding:10px; background:#222; border-radius:5px;">
                <div>
                    <span style="color:white;">${item.cantidad}x ${item.nombre}</span>
                    <br>
                    <span style="color:#ffd700; font-size:0.9em;">$${subtotalItem.toLocaleString('es-MX', {minimumFractionDigits: 2})}</span>
                </div>
                <button onclick="eliminarDelCarrito(${index})" style="background:#d32f2f; color:white; border:none; padding:5px 10px; border-radius:3px; cursor:pointer;">Eliminar</button>
            </div>
        `;
    });
    if (contadorEl) contadorEl.innerText = cantidadTotal;
    totalEl.innerText = `$${total.toLocaleString('es-MX', {minimumFractionDigits: 2})}`;
    
    if (inputSubtotalEl) inputSubtotalEl.value = total.toFixed(2);
    if (inputJsonEl) inputJsonEl.value = JSON.stringify(carrito);
}
function eliminarDelCarrito(index) {
    carrito.splice(index, 1);
    actualizarCarritoUI();
}
function limpiarCarrito() {
    carrito = [];
    actualizarCarritoUI();
}
function abrirCarrito() {
    const panel = document.getElementById('panelCarrito');
    if (panel) {
        panel.classList.add('activo');
    }
}
function cerrarCarrito() {
    const panel = document.getElementById('panelCarrito');
    if (panel) {
        panel.classList.remove('activo');
    }
}
function verDetallesCotizacion(btn) {
    try {
        const stringEquipos = btn.getAttribute('data-equipos');
        const equipos = JSON.parse(stringEquipos);
        const subtotal = parseFloat(btn.getAttribute('data-subtotal'));
        const iva = parseFloat(btn.getAttribute('data-iva'));
        const total = parseFloat(btn.getAttribute('data-total'));
        const contenedor = document.getElementById('contenedorEquiposDetalle');
        contenedor.innerHTML = '';
        equipos.forEach(item => {
            const nombreProducto = item.nombre || item.producto || Object.values(item)[0] || "Equipo de Seguridad";
            const precioProducto = item.precio || item.costo || Object.values(item)[1] || 0;
            contenedor.innerHTML += `
                <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #222;">
                    <span style="color: #fff;">• ${item.nombre}</span>
                    <span style="color: #ffd700;">$${parseFloat(item.precio).toLocaleString(undefined, {minimumFractionDigits: 2})}</span>
                </div>`;
        });
        document.getElementById('detSubtotal').innerText = `$${subtotal.toLocaleString(undefined, {minimumFractionDigits: 2})}`;
        document.getElementById('detIva').innerText = `$${iva.toLocaleString(undefined, {minimumFractionDigits: 2})}`;
        document.getElementById('detTotal').innerText = `$${total.toLocaleString(undefined, {minimumFractionDigits: 2})}`;
        document.getElementById('panelAdminLateral').classList.add('activo');
    } catch (error) {
        console.error("Error al desempaquetar el kit de equipos:", error);
        alert("Hubo un problema al leer el desglose de esta cotización.");
    }
}
function cerrarPanelAdmin() {
    document.getElementById('panelAdminLateral').classList.remove('activo');
}
function revisarYEnviar() {
    console.log("Contenido actual del carrito antes de enviar:", carrito);
    const jsonTexto = JSON.stringify(carrito);
    document.getElementById('input_json').value = jsonTexto;
    if (carrito.length === 0) {
        alert("El carrito Está vacío. Por favor, agrega al menos un producto antes de enviar la cotización.");
        return;
    }
    document.getElementById('formCotizacion').submit();
}
function cambiarCantidad(botonFlecha, delta) {
    const contenedor = botonFlecha.parentElement;
    const input = contenedor.querySelector('.input-cantidad');
    let nuevaCantidad = parseInt(input.value) + delta;
    if (nuevaCantidad >= 1) {
        input.value = nuevaCantidad;
    }
}