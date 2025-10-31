document.addEventListener('DOMContentLoaded', () => {
    const departamentoSelect = document.getElementById('billing_country_provincia');
    const provinciaSelect = document.getElementById('billing_provincia');
    const distritoSelect = document.getElementById('billing_distrito_provincia');

    if (!departamentoSelect || !provinciaSelect || !distritoSelect) {
        return;
    }

    const obtenerUbigeoUrl = () => {
        const dataUrl = (departamentoSelect.dataset.ubigeoUrl || '').trim();
        if (dataUrl) {
            return dataUrl;
        }
        return '/public/assets/data/ubigeo.json';
    };

    const obtenerValorGuardado = (select) => (select.dataset.valorGuardado || '').trim();

    const actualizarNiceSelect = (select) => {
        if (!(window.jQuery && typeof window.jQuery.fn.niceSelect === 'function')) {
            return;
        }

        const $select = window.jQuery(select);

        try {
            if ($select.data('niceSelect')) {
                $select.niceSelect('update');
            } else {
                $select.niceSelect();
            }
        } catch (error) {
            console.error('No fue posible actualizar nice-select:', error);
        }
    };

    const crearOpcionPorDefecto = () => {
        const option = document.createElement('option');
        option.value = '';
        option.textContent = 'Seleccionar';
        return option;
    };

    const resetSelect = (select) => {
        select.innerHTML = '';
        select.appendChild(crearOpcionPorDefecto());
    };

    const bindChange = (select, handler) => {
        if (window.jQuery && typeof window.jQuery === 'function') {
            window.jQuery(select).on('change', handler);
        } else {
            select.addEventListener('change', handler);
        }
    };

    let ubigeoData = {};

    const cargarDepartamentos = () => {
        resetSelect(departamentoSelect);
        Object.keys(ubigeoData).forEach(dep => {
            const option = document.createElement('option');
            option.value = dep;
            option.textContent = dep;
            departamentoSelect.appendChild(option);
        });
        actualizarNiceSelect(departamentoSelect);
    };

    const cargarProvincias = (departamento) => {
        resetSelect(provinciaSelect);
        resetSelect(distritoSelect);

        if (departamento && ubigeoData[departamento]) {
            Object.keys(ubigeoData[departamento]).forEach(prov => {
                const option = document.createElement('option');
                option.value = prov;
                option.textContent = prov;
                provinciaSelect.appendChild(option);
            });
        }
        actualizarNiceSelect(provinciaSelect);
        actualizarNiceSelect(distritoSelect);
    };

    const cargarDistritos = (departamento, provincia) => {
        resetSelect(distritoSelect);

        if (departamento && provincia && ubigeoData[departamento] && ubigeoData[departamento][provincia]) {
            ubigeoData[departamento][provincia].forEach(dist => {
                const option = document.createElement('option');
                option.value = dist;
                option.textContent = dist;
                distritoSelect.appendChild(option);
            });
        }
        actualizarNiceSelect(distritoSelect);
    };

    bindChange(departamentoSelect, function (event) {
        cargarProvincias(event.target.value);
    });

    bindChange(provinciaSelect, function (event) {
        cargarDistritos(departamentoSelect.value, event.target.value);
    });

    fetch(obtenerUbigeoUrl())
        .then(res => {
            if (!res.ok) {
                throw new Error('No se pudo cargar el archivo de ubigeo.');
            }
            return res.json();
        })
        .then(data => {
            console.log('Ubigeo cargado:', data);
            ubigeoData = data;
            cargarDepartamentos();

            const departamentoGuardado = obtenerValorGuardado(departamentoSelect);
            const provinciaGuardada = obtenerValorGuardado(provinciaSelect);
            const distritoGuardado = obtenerValorGuardado(distritoSelect);

            if (departamentoGuardado && ubigeoData[departamentoGuardado]) {
                departamentoSelect.value = departamentoGuardado;
                actualizarNiceSelect(departamentoSelect);
                cargarProvincias(departamentoGuardado);

                if (provinciaGuardada && ubigeoData[departamentoGuardado][provinciaGuardada]) {
                    provinciaSelect.value = provinciaGuardada;
                    actualizarNiceSelect(provinciaSelect);
                    cargarDistritos(departamentoGuardado, provinciaGuardada);

                    if (ubigeoData[departamentoGuardado][provinciaGuardada].includes(distritoGuardado)) {
                        distritoSelect.value = distritoGuardado;
                        actualizarNiceSelect(distritoSelect);
                    }
                }
            }
        })
        .catch(error => {
            console.error('Error al cargar el ubigeo:', error);
        });
});
