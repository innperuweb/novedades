document.addEventListener('DOMContentLoaded', () => {
    const input = document.querySelector('#buscador');
    const resultados = document.querySelector('#resultados');
    const form = document.querySelector('.searchform');
    const baseUrl = (typeof base_url !== 'undefined' && base_url) ? base_url : '/';

    const escapeHtml = (text) => {
        if (typeof text !== 'string') {
            return '';
        }

        return text
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    };

    if (form) {
        form.addEventListener('submit', (event) => {
            event.preventDefault();
        });
    }

    if (input && resultados) {
        input.addEventListener('keyup', () => {
            const term = input.value.trim();

            if (term.length >= 2) {
                fetch(`${baseUrl}search/ajax?term=${encodeURIComponent(term)}`)
                    .then((res) => res.json())
                    .then((data) => {
                        resultados.innerHTML = '';

                        if (Array.isArray(data) && data.length > 0) {
                            const buildImageUrl = (ruta) => {
                                if (!ruta) {
                                    return `${baseUrl}public/assets/img/products/m02-mockup.png`;
                                }

                                let normalizada = String(ruta).trim();
                                if (normalizada === '') {
                                    return `${baseUrl}public/assets/img/products/m02-mockup.png`;
                                }

                                if (/^https?:\/\//i.test(normalizada)) {
                                    return normalizada;
                                }

                                normalizada = normalizada.replace(/^\/+/, '');

                                if (normalizada.startsWith('public/assets/')) {
                                    return `${baseUrl}${normalizada}`;
                                }

                                if (normalizada.startsWith('assets/')) {
                                    return `${baseUrl}${normalizada}`;
                                }

                                if (normalizada.startsWith('public/uploads/productos/')) {
                                    return `${baseUrl}${normalizada}`;
                                }

                                if (normalizada.startsWith('uploads/')) {
                                    return `${baseUrl}public/assets/${normalizada}`;
                                }

                                if (normalizada.startsWith('productos/') || normalizada.startsWith('products/')) {
                                    return `${baseUrl}public/assets/uploads/${normalizada}`;
                                }

                                return `${baseUrl}public/assets/img/products/${encodeURIComponent(normalizada)}`;
                            };

                            data.forEach((p) => {
                                const precio = parseFloat(p.precio || 0).toFixed(2);
                                const nombre = escapeHtml(p.nombre || '');
                                const imagenUrl = buildImageUrl(p.imagen || '');
                                const enlace = `${baseUrl}producto/detalle/${encodeURIComponent(p.id)}`;

                                resultados.innerHTML += `
                  <div class="producto">
                    <img src="${escapeHtml(imagenUrl)}" alt="${nombre}">
                    <h4>${nombre}</h4>
                    <p>S/ ${precio}</p>
                    <a href="${enlace}" class="btn-ver">Ver detalle</a>
                  </div>`;
                            });
                        } else {
                            resultados.innerHTML = '<p>No se encontraron productos.</p>';
                        }
                    })
                    .catch(() => {
                        resultados.innerHTML = '<p>Ocurrió un error al buscar productos.</p>';
                    });
            } else {
                resultados.innerHTML = '';
            }
        });
    }
});
