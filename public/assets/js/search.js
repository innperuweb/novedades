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
                            data.forEach((p) => {
                                const precio = parseFloat(p.precio || 0).toFixed(2);
                                const nombre = escapeHtml(p.nombre || '');
                                const imagen = encodeURIComponent(p.imagen || '');
                                const enlace = `${baseUrl}producto/detalle/${encodeURIComponent(p.id)}`;

                                resultados.innerHTML += `
                  <div class="producto">
                    <img src="${baseUrl}public/assets/img/productos/${imagen}" alt="${nombre}">
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
