<div class="form-step" id="step-2" style="display: none;">
    <div class="form-group">
        <label for="street">Calle</label>
        <input type="text" id="street" value="{$patient.street|default: ''}" name="street" required maxlength="30">
    </div>
    <div class="form-group">
        <label for="neighborhood">Colonia</label>
        <input type="text" id="neighborhood" value="{$patient.neighborhood|default: ''}" name="neighborhood" required maxlength="30">
    </div>
    <div class="form-group">
        <label for="postalCode">Código Postal</label>
        <input type="number" id="postalCode" value="{$patient.CP|default: ''}" name="postalCode" required maxlength="5" oninput="if(this.value.length > 5) this.value = this.value.slice(0, 5);">
    </div>
    <div class="form-group">
        <label for="extNumber">Número Exterior</label>
        <input type="text" id="extNumber" value="{$patient.external_number|default: ''}" name="extNumber" required maxlength="10">
    </div>
    <div class="form-group">
        <label for="intNumber">Número Interior</label>
        <input type="text" id="intNumber" value="{$patient.internal_number|default: ''}" name="intNumber" maxlength="8">
    </div>
    <div class="form-group">
        <label for="state">Estado</label>
        <select name="state" id="state" required>
            <option value="">Seleccione...</option>
            {foreach from=$states item=state}
                <option value="{$state.id}">{$state.name}</option>
            {/foreach}
        </select>
    </div>
    <div class="form-group">
        <label for="municipality">Municipio</label>
        <select name="municipality" id="municipality" required>
            <option value="">Seleccione un estado primero...</option>
        </select>
    </div>
    <div class="form-group">
        <label for="locality">Localidad</label>
        <select name="locality" id="locality" required>
            <option value="">Seleccione un municipio primero...</option>
        </select>
    </div>

    <button type="button" class="prev-btn" onclick="prevStep(1)">Atrás</button>
    <button type="button" class="next-btn" onclick="nextStep(3)">Siguiente</button>
</div>
