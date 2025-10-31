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

    // Refresca nice-select de un select concreto (sin romper estilos)
    const refreshNice = (select) => {
        if (window.jQuery && typeof window.jQuery.fn.niceSelect === 'function') {
            const $s = window.jQuery(select);
            if ($s.data('niceSelect')) $s.niceSelect('update'); else $s.niceSelect();
        }
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
        refreshNice(select);
    };

    // Datos del ubigeo
    let ubigeoData = {};

    // Cargar departamentos
    const cargarDepartamentos = () => {
        resetSelect(departamentoSelect, 'Seleccionar Departamento');
        Object.keys(ubigeoData).forEach(dep => {
            const option = document.createElement('option');
            option.value = dep;
            option.textContent = dep;
            departamentoSelect.appendChild(option);
        });
        refreshNice(departamentoSelect);
    };

    // Cargar provincias según el departamento
    const cargarProvincias = (dep, ubigeoData) => {
        const d = norm(dep);
        resetSelect(provinciaSelect, 'Seleccionar Provincia');
        resetSelect(distritoSelect, 'Seleccionar Distrito');

        if (!d || !ubigeoData[d]) { refreshNice(provinciaSelect); refreshNice(distritoSelect); return; }

        Object.keys(ubigeoData[d]).forEach((prov) => {
            const opt = document.createElement('option');
            opt.value = prov;
            opt.textContent = prov;
            provinciaSelect.appendChild(opt);
        });

        refreshNice(provinciaSelect);
        refreshNice(distritoSelect);
    };

    // Cargar distritos según la provincia
    const cargarDistritos = (dep, prov, ubigeoData) => {
        const d = norm(dep);
        const p = norm(prov);
        resetSelect(distritoSelect, 'Seleccionar Distrito');

        if (!d || !p || !ubigeoData[d] || !ubigeoData[d][p]) { refreshNice(distritoSelect); return; }

        ubigeoData[d][p].forEach((dist) => {
            const opt = document.createElement('option');
            opt.value = dist;
            opt.textContent = dist;
            distritoSelect.appendChild(opt);
        });

        refreshNice(distritoSelect);
    };

    // Eventos de cambio
    departamentoSelect.addEventListener('change', function () {
        cargarProvincias(this.value, ubigeoData);
    });

    provinciaSelect.addEventListener('change', function () {
        cargarDistritos(departamentoSelect.value, this.value, ubigeoData);
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
                refreshNice(departamentoSelect);
                cargarProvincias(depGuardado, ubigeoData);

                if (provGuardado && ubigeoData[depGuardado][provGuardado]) {
                    provinciaSelect.value = provGuardado;
                    refreshNice(provinciaSelect);
                    cargarDistritos(depGuardado, provGuardado, ubigeoData);

                    if (ubigeoData[depGuardado][provGuardado].includes(distGuardado)) {
                        distritoSelect.value = distGuardado;
                        refreshNice(distritoSelect);
                    }
                }
            }
        })
        .catch(error => {
            console.error('Error al cargar el ubigeo:', error);
        });
});
