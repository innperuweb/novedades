<script>
document.addEventListener("DOMContentLoaded", function () {
    // Rutas del JSON de ubigeo
    const ubigeoURL = "/public/assets/data/ubigeo.json";

    // Elementos del formulario (checkout)
    const departamentoSelect = document.getElementById("billing_country_provincia");
    const provinciaSelect = document.getElementById("billing_provincia");
    const distritoSelect = document.getElementById("billing_distrito_provincia");

    // Datos cargados del archivo JSON
    let ubigeoData = {};

    // --- 1. Cargar el archivo JSON ---
    fetch(ubigeoURL)
        .then(response => {
            if (!response.ok) throw new Error("No se pudo cargar el archivo ubigeo.json");
            return response.json();
        })
        .then(data => {
            ubigeoData = data;
            cargarDepartamentos();
        })
        .catch(error => console.error("Error al cargar los datos de ubigeo:", error));

    // --- 2. Llenar los departamentos ---
    function cargarDepartamentos() {
        limpiarSelect(departamentoSelect, "Seleccionar Departamento");
        Object.keys(ubigeoData).forEach(dep => {
            const option = document.createElement("option");
            option.value = dep;
            option.textContent = dep;
            departamentoSelect.appendChild(option);
        });
    }

    // --- 3. Al cambiar el departamento ---
    departamentoSelect.addEventListener("change", function () {
        const departamento = this.value;
        limpiarSelect(provinciaSelect, "Seleccionar Provincia");
        limpiarSelect(distritoSelect, "Seleccionar Distrito");

        if (departamento && ubigeoData[departamento]) {
            Object.keys(ubigeoData[departamento]).forEach(prov => {
                const option = document.createElement("option");
                option.value = prov;
                option.textContent = prov;
                provinciaSelect.appendChild(option);
            });
        }
    });

    // --- 4. Al cambiar la provincia ---
    provinciaSelect.addEventListener("change", function () {
        const departamento = departamentoSelect.value;
        const provincia = this.value;

        limpiarSelect(distritoSelect, "Seleccionar Distrito");

        if (departamento && provincia && ubigeoData[departamento][provincia]) {
            ubigeoData[departamento][provincia].forEach(dist => {
                const option = document.createElement("option");
                option.value = dist;
                option.textContent = dist;
                distritoSelect.appendChild(option);
            });
        }
    });

    // --- 5. Función para limpiar select ---
    function limpiarSelect(select, placeholder) {
        select.innerHTML = "";
        const opt = document.createElement("option");
        opt.value = "";
        opt.textContent = placeholder;
        select.appendChild(opt);
    }
});
</script>