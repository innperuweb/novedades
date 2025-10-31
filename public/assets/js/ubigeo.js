document.addEventListener('DOMContentLoaded', () => {
    const departamentoSelect = document.getElementById('billing_country_provincia');
    const provinciaSelect = document.getElementById('billing_provincia');
    const distritoSelect = document.getElementById('billing_distrito_provincia');

    // Si no existen los selects, detener el script
    if (!departamentoSelect || !provinciaSelect || !distritoSelect) return;

    // Obtener ruta del archivo ubigeo.json
    const obtenerUbigeoUrl = () => {
        const dataUrl = (departamentoSelect.dataset.ubigeoUrl || '').trim();
        if (dataUrl) {
            try {
                return new URL(dataUrl, window.location.origin).toString();
            } catch (error) {
                return dataUrl;
            }
        }

        const base = (window.location && window.location.origin) ? window.location.origin : '';
        return `${base.replace(/\/?$/, '')}/public/assets/data/ubigeo.json`;
    };

    // Recuperar valores guardados (en caso de persistencia)
    const obtenerValorGuardado = (select) => (select.dataset.valorGuardado || '').trim();

    // Utilidad: normaliza claves y valores
    const norm = (s) => (s || '').toString().trim();

    // Refresca nice-select de un select concreto (sin romper estilos) y sincroniza visibilidad
    const actualizarNiceSelect = (select) => {
        if (window.jQuery && typeof window.jQuery.fn.niceSelect === 'function') {
            const $s = window.jQuery(select);
            if ($s.data('niceSelect')) {
                $s.niceSelect('update');
            } else {
                $s.niceSelect();
            }
        }

        const niceElement = select.nextElementSibling;
        if (niceElement && niceElement.classList && niceElement.classList.contains('nice-select')) {
            if (select.classList.contains('hidden')) {
                niceElement.classList.add('hidden');
            } else {
                niceElement.classList.remove('hidden');
            }
        }
    };

    const ocultarSelect = (select) => {
        if (!select.classList.contains('hidden')) {
            select.classList.add('hidden');
        }
        actualizarNiceSelect(select);
    };

    const mostrarSelect = (select) => {
        if (select.classList.contains('hidden')) {
            select.classList.remove('hidden');
        }
        actualizarNiceSelect(select);
    };

    // Crea la opción "Seleccionar"
    const makePlaceholder = (txt = 'Seleccionar') => {
        const o = document.createElement('option');
        o.value = '';
        o.textContent = txt;
        return o;
    };

    // Resetea un select y deja solo el placeholder
    const resetSelect = (select, placeholder = 'Seleccionar') => {
        select.innerHTML = '';
        select.appendChild(makePlaceholder(placeholder));
        actualizarNiceSelect(select);
    };

    // Datos del ubigeo
    let ubigeoData = {};

    ocultarSelect(provinciaSelect);
    ocultarSelect(distritoSelect);

    // Cargar departamentos
    const cargarDepartamentos = () => {
        resetSelect(departamentoSelect, 'Seleccionar Departamento');
        Object.keys(ubigeoData).forEach(dep => {
            const option = document.createElement('option');
            option.value = dep;
            option.textContent = dep;
            departamentoSelect.appendChild(option);
        });
        actualizarNiceSelect(departamentoSelect);
    };

    // Cargar provincias según el departamento
    const cargarProvincias = (dep) => {
        const d = norm(dep);
        resetSelect(provinciaSelect, 'Seleccionar Provincia');
        resetSelect(distritoSelect, 'Seleccionar Distrito');
        ocultarSelect(provinciaSelect);
        ocultarSelect(distritoSelect);

        if (!d || !ubigeoData[d]) { return; }

        Object.keys(ubigeoData[d]).forEach((prov) => {
            const opt = document.createElement('option');
            opt.value = prov;
            opt.textContent = prov;
            provinciaSelect.appendChild(opt);
        });

        mostrarSelect(provinciaSelect);
    };

    // Cargar distritos según la provincia
    const cargarDistritos = (dep, prov) => {
        const d = norm(dep);
        const p = norm(prov);
        resetSelect(distritoSelect, 'Seleccionar Distrito');
        ocultarSelect(distritoSelect);

        if (!d || !p || !ubigeoData[d] || !ubigeoData[d][p]) { return; }

        ubigeoData[d][p].forEach((dist) => {
            const opt = document.createElement('option');
            opt.value = dist;
            opt.textContent = dist;
            distritoSelect.appendChild(opt);
        });

        mostrarSelect(distritoSelect);
    };

    // Eventos de cambio
    departamentoSelect.addEventListener('change', function () {
        cargarProvincias(this.value);
    });

    provinciaSelect.addEventListener('change', function () {
        cargarDistritos(departamentoSelect.value, this.value);
    });

    // Cargar datos del JSON
    fetch(obtenerUbigeoUrl())
        .then(res => {
            if (!res.ok) throw new Error('No se pudo cargar el archivo de ubigeo.');
            return res.json();
        })
        .then(data => {
            ubigeoData = data;
            cargarDepartamentos();

            // Restaurar selección previa si existe
            const depGuardado = obtenerValorGuardado(departamentoSelect);
            const provGuardado = obtenerValorGuardado(provinciaSelect);
            const distGuardado = obtenerValorGuardado(distritoSelect);

            if (depGuardado && ubigeoData[depGuardado]) {
                departamentoSelect.value = depGuardado;
                actualizarNiceSelect(departamentoSelect);
                cargarProvincias(depGuardado);

                if (provGuardado && ubigeoData[depGuardado][provGuardado]) {
                    provinciaSelect.value = provGuardado;
                    actualizarNiceSelect(provinciaSelect);
                    cargarDistritos(depGuardado, provGuardado);

                    if (ubigeoData[depGuardado][provGuardado].includes(distGuardado)) {
                        distritoSelect.value = distGuardado;
                        actualizarNiceSelect(distritoSelect);
                    }
                }
            }
        })
        .catch(error => {
            console.error('Error al cargar el ubigeo:', error);
        });
});
