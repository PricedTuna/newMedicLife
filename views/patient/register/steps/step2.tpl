<div class="form-step" id="step-2" style="display: none;">
    <div class="form-group">
        <label for="street">Calle
            <span class="tooltip-container">
                <i class="bi bi-question-circle tooltip-icon"></i>
                <span class="tooltip-text">Ingrese el nombre de la calle donde vive el paciente. Máximo 30 caracteres.</span>
            </span>
        </label>
        <input type="text" id="street" value="{$patient.street|default: ''}" name="street" required maxlength="30">
    </div>
    <div class="form-group">
        <label for="neighborhood">Colonia
            <span class="tooltip-container">
                <i class="bi bi-question-circle tooltip-icon"></i>
                <span class="tooltip-text">Ingrese el nombre de la colonia donde vive el paciente. Máximo 30 caracteres.</span>
            </span>
        </label>
        <input type="text" id="neighborhood" value="{$patient.neighborhood|default: ''}" name="neighborhood" required maxlength="30">
    </div>
    <div class="form-group">
        <label for="postalCode">Código Postal
            <span class="tooltip-container">
                <i class="bi bi-question-circle tooltip-icon"></i>
                <span class="tooltip-text">Ingrese el código postal de la ubicación del paciente. Debe tener 5 dígitos.</span>
            </span>
        </label>
        <input type="number" id="postalCode" value="{$patient.CP|default: ''}" name="postalCode" required maxlength="5" oninput="if(this.value.length > 5) this.value = this.value.slice(0, 5);">
    </div>
    <div class="form-group">
        <label for="extNumber">Número Exterior
            <span class="tooltip-container">
                <i class="bi bi-question-circle tooltip-icon"></i>
                <span class="tooltip-text">Ingrese el número exterior de la vivienda del paciente. Máximo 10 caracteres.</span>
            </span>
        </label>
        <input type="text" id="extNumber" value="{$patient.external_number|default: ''}" name="extNumber" required maxlength="10">
    </div>
    <div class="form-group">
        <label for="intNumber">Número Interior
            <span class="tooltip-container">
                <i class="bi bi-question-circle tooltip-icon"></i>
                <span class="tooltip-text">Ingrese el número interior de la vivienda del paciente si aplica. Máximo 8 caracteres.</span>
            </span>
        </label>
        <input type="text" id="intNumber" value="{$patient.internal_number|default: ''}" name="intNumber" maxlength="8">
    </div>
    <div class="form-group">
        <label for="state">Estado
            <span class="tooltip-container">
                <i class="bi bi-question-circle tooltip-icon"></i>
                <span class="tooltip-text">Seleccione el estado donde vive el paciente.</span>
            </span>
        </label>
        <select name="state" id="state" required>
            <option value="">Seleccione...</option>
            {foreach from=$states item=state}
                <option value="{$state.id}">{$state.name}</option>
            {/foreach}
        </select>
    </div>
    <div class="form-group">
        <label for="municipality">Municipio
            <span class="tooltip-container">
                <i class="bi bi-question-circle tooltip-icon"></i>
                <span class="tooltip-text">Seleccione el municipio donde vive el paciente.</span>
            </span>
        </label>
        <select name="municipality" id="municipality" required>
            <option value="">Seleccione un estado primero...</option>
        </select>
    </div>
    <div class="form-group">
        <label for="locality">Localidad
            <span class="tooltip-container">
                <i class="bi bi-question-circle tooltip-icon"></i>
                <span class="tooltip-text">Seleccione la localidad donde vive el paciente.</span>
            </span>
        </label>
        <select name="locality" id="locality" required>
            <option value="">Seleccione un municipio primero...</option>
        </select>
    </div>

    <button type="button" class="prev-btn" onclick="prevStep(1)">Atrás</button>
    <button type="button" class="next-btn" onclick="nextStep(3)">Siguiente</button>
</div>
