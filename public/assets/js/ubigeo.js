document.addEventListener('DOMContentLoaded', () => {
    const departamentoSelect = document.getElementById('billing_country_provincia');
    const provinciaSelect = document.getElementById('billing_provincia');
    const distritoSelect = document.getElementById('billing_distrito_provincia');

    // Si no existen los selects, detener el script
    if (!departamentoSelect || !provinciaSelect || !distritoSelect) return;

    // Obtener ruta del archivo ubigeo.json
    const obtenerUbigeoUrl = () => {
        const dataUrl = (departamentoSelect.dataset.ubigeoUrl || '').trim();
        return dataUrl || '/public/assets/data/ubigeo.json';
    };

    // Recuperar valores guardados (en caso de persistencia)
    const obtenerValorGuardado = (select) => (select.dataset.valorGuardado || '').trim();

    // Actualizar o inicializar nice-select
    const actualizarNiceSelect = (select) => {
        if (!(window.jQuery && typeof window.jQuery.fn.niceSelect === 'function')) return;
        const $select = window.jQuery(select);
        try {
            if ($select.data('niceSelect')) {
                $select.niceSelect('update');
            } else {
                $select.niceSelect();
            }
        } catch (error) {
            console.error('Error al actualizar nice-select:', error);
        }
    };

    // Crear opción por defecto
    const crearOpcionPorDefecto = () => {
        const option = document.createElement('option');
        option.value = '';
        option.textContent = 'Seleccionar';
        return option;
    };

    // Resetear un select
    const resetSelect = (select) => {
        select.innerHTML = '';
        select.appendChild(crearOpcionPorDefecto());
        actualizarNiceSelect(select);
    };

    // Datos del ubigeo
    let ubigeoData = {};

    // Cargar departamentos
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

    // Cargar provincias según el departamento
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

    // Cargar distritos según la provincia
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
            console.log('Ubigeo cargado correctamente');
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