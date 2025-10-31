document.addEventListener('DOMContentLoaded', () => {
    const departamentoSelect = document.getElementById('billing_country_provincia');
    const provinciaSelect = document.getElementById('billing_provincia');
    const distritoSelect = document.getElementById('billing_distrito_provincia');

    if (!departamentoSelect || !provinciaSelect || !distritoSelect) return;

    // Inicialmente ocultamos Provincia y Distrito
    provinciaSelect.classList.add('hidden');
    distritoSelect.classList.add('hidden');

    const actualizarNiceSelect = (select) => {
        if (!(window.jQuery && typeof window.jQuery.fn.niceSelect === 'function')) return;
        const $select = window.jQuery(select);
        if ($select.data('niceSelect')) $select.niceSelect('update'); else $select.niceSelect();
    };

    const resetSelect = (select, placeholder) => {
        select.innerHTML = '';
        const opt = document.createElement('option');
        opt.value = '';
        opt.textContent = placeholder || 'Seleccionar';
        select.appendChild(opt);
        actualizarNiceSelect(select);
    };

    let ubigeoData = {};

    // Carga inicial del archivo ubigeo.json
    fetch(window.location.origin + '/novedades/public/assets/data/ubigeo.json', { cache: 'no-store' })
        .then((res) => {
            if (!res.ok) {
                throw new Error(`No se pudo cargar el ubigeo.json (HTTP ${res.status})`);
            }
            return res.json();
        })
        .then((data) => {
            console.log('✅ UBIGEO cargado correctamente:', Object.keys(data));
            ubigeoData = data;

            // Limpiar y preparar select
            resetSelect(departamentoSelect, 'Seleccionar Departamento');

            // Cargar departamentos visibles
            Object.keys(ubigeoData).forEach(dep => {
                const opt = document.createElement('option');
                opt.value = dep;
                opt.textContent = dep;
                departamentoSelect.appendChild(opt);
            });

            // Mostrar y actualizar
            departamentoSelect.classList.remove('hidden');
            actualizarNiceSelect(departamentoSelect);
        })
        .catch((err) => {
            console.error('❌ Error cargando ubigeo.json:', err);
            alert('Error cargando lista de departamentos. Verifique el archivo ubigeo.json');
        });

    // Cargar provincias al elegir departamento
    departamentoSelect.addEventListener('change', function () {
        const dep = this.value.trim();
        resetSelect(provinciaSelect, 'Seleccionar Provincia');
        resetSelect(distritoSelect, 'Seleccionar Distrito');
        provinciaSelect.classList.add('hidden');
        distritoSelect.classList.add('hidden');

        if (dep && ubigeoData[dep]) {
            Object.keys(ubigeoData[dep]).forEach(prov => {
                const opt = document.createElement('option');
                opt.value = prov;
                opt.textContent = prov;
                provinciaSelect.appendChild(opt);
            });
            provinciaSelect.classList.remove('hidden');
            actualizarNiceSelect(provinciaSelect);
        }
    });

    // Cargar distritos al elegir provincia
    provinciaSelect.addEventListener('change', function () {
        const dep = departamentoSelect.value.trim();
        const prov = this.value.trim();
        resetSelect(distritoSelect, 'Seleccionar Distrito');
        distritoSelect.classList.add('hidden');

        if (dep && prov && ubigeoData[dep] && ubigeoData[dep][prov]) {
            ubigeoData[dep][prov].forEach(dist => {
                const opt = document.createElement('option');
                opt.value = dist;
                opt.textContent = dist;
                distritoSelect.appendChild(opt);
            });
            distritoSelect.classList.remove('hidden');
            actualizarNiceSelect(distritoSelect);
        }
    });
});
