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

    const actualizarNiceSelect = () => {
        if (window.jQuery && typeof window.jQuery.fn.niceSelect === 'function' && window.jQuery('.nice-select').length) {
            window.jQuery('select').niceSelect('update');
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
        actualizarNiceSelect();
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
        actualizarNiceSelect();
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
        actualizarNiceSelect();
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
        actualizarNiceSelect();
    };

    departamentoSelect.addEventListener('change', function () {
        cargarProvincias(this.value);
    });

    provinciaSelect.addEventListener('change', function () {
        cargarDistritos(departamentoSelect.value, this.value);
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
                actualizarNiceSelect();
                cargarProvincias(departamentoGuardado);

                if (provinciaGuardada && ubigeoData[departamentoGuardado][provinciaGuardada]) {
                    provinciaSelect.value = provinciaGuardada;
                    actualizarNiceSelect();
                    cargarDistritos(departamentoGuardado, provinciaGuardada);

                    if (ubigeoData[departamentoGuardado][provinciaGuardada].includes(distritoGuardado)) {
                        distritoSelect.value = distritoGuardado;
                        actualizarNiceSelect();
                    }
                }
            }
        })
        .catch(error => {
            console.error('Error al cargar el ubigeo:', error);
        });
});
