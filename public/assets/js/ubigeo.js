document.addEventListener('DOMContentLoaded', () => {
    const departamento = document.getElementById('billing_country_provincia');
    const provincia = document.getElementById('billing_provincia');
    const distrito = document.getElementById('billing_distrito_provincia');

    if (!departamento || !provincia || !distrito) {
        return;
    }

    const ubigeoURL = (() => {
        const dataUrl = departamento.dataset.ubigeoUrl;
        if (dataUrl && dataUrl.trim().length > 0) {
            return dataUrl.trim();
        }
        return 'public/assets/data/ubigeo.json';
    })();

    const obtenerValorGuardado = (select) => (select.dataset.valorGuardado || '').trim();

    const actualizarNiceSelect = (select) => {
        if (window.jQuery && typeof window.jQuery.fn.niceSelect === 'function') {
            window.jQuery(select).niceSelect('update');
        }
    };

    const resetSelect = (select, placeholder) => {
        select.innerHTML = '';
        const opt = document.createElement('option');
        opt.value = '';
        opt.textContent = placeholder;
        select.appendChild(opt);
        actualizarNiceSelect(select);
    };

    let ubigeoData = {};

    const cargarDepartamentos = () => {
        resetSelect(departamento, 'Seleccionar Departamento');
        Object.keys(ubigeoData).forEach(dep => {
            const opt = document.createElement('option');
            opt.value = dep;
            opt.textContent = dep;
            departamento.appendChild(opt);
        });
        actualizarNiceSelect(departamento);
    };

    const cargarProvincias = (departamentoSeleccionado) => {
        resetSelect(provincia, 'Seleccionar Provincia');
        resetSelect(distrito, 'Seleccionar Distrito');

        if (departamentoSeleccionado && ubigeoData[departamentoSeleccionado]) {
            Object.keys(ubigeoData[departamentoSeleccionado]).forEach(prov => {
                const opt = document.createElement('option');
                opt.value = prov;
                opt.textContent = prov;
                provincia.appendChild(opt);
            });
            actualizarNiceSelect(provincia);
        }
    };

    const cargarDistritos = (departamentoSeleccionado, provinciaSeleccionada) => {
        resetSelect(distrito, 'Seleccionar Distrito');

        if (
            departamentoSeleccionado &&
            provinciaSeleccionada &&
            ubigeoData[departamentoSeleccionado] &&
            ubigeoData[departamentoSeleccionado][provinciaSeleccionada]
        ) {
            ubigeoData[departamentoSeleccionado][provinciaSeleccionada].forEach(dist => {
                const opt = document.createElement('option');
                opt.value = dist;
                opt.textContent = dist;
                distrito.appendChild(opt);
            });
            actualizarNiceSelect(distrito);
        }
    };

    departamento.addEventListener('change', function () {
        cargarProvincias(this.value);
    });

    provincia.addEventListener('change', function () {
        cargarDistritos(departamento.value, this.value);
    });

    fetch(ubigeoURL)
        .then(response => {
            if (!response.ok) {
                throw new Error('No se pudo cargar el archivo de ubigeo.');
            }
            return response.json();
        })
        .then(data => {
            ubigeoData = data;
            cargarDepartamentos();

            const departamentoGuardado = obtenerValorGuardado(departamento);
            const provinciaGuardada = obtenerValorGuardado(provincia);
            const distritoGuardado = obtenerValorGuardado(distrito);

            if (departamentoGuardado && ubigeoData[departamentoGuardado]) {
                departamento.value = departamentoGuardado;
                actualizarNiceSelect(departamento);
                cargarProvincias(departamentoGuardado);

                if (provinciaGuardada && ubigeoData[departamentoGuardado][provinciaGuardada]) {
                    provincia.value = provinciaGuardada;
                    actualizarNiceSelect(provincia);
                    cargarDistritos(departamentoGuardado, provinciaGuardada);

                    if (ubigeoData[departamentoGuardado][provinciaGuardada].includes(distritoGuardado)) {
                        distrito.value = distritoGuardado;
                        actualizarNiceSelect(distrito);
                    }
                }
            }
        })
        .catch(error => {
            console.error('Error al cargar el ubigeo:', error);
        });
});
