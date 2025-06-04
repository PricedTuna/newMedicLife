<div class="form-step" id="step-2" style="display: none;">
    <div class="form-group">
        <label for="street">Calle</label>
        <input type="text" id="street" name="street" value="{$doctor.street|default:''}" required maxlength="30">
    </div>
    <div class="form-group">
        <label for="neighborhood">Colonia</label>
        <input type="text" id="neighborhood" name="neighborhood" value="{$doctor.neighborhood|default:''}" required maxlength="30">
    </div>
    <div class="form-group">
        <label for="postalCode">Código Postal</label>
        <input type="number" id="postalCode" name="postalCode" value="{$doctor.CP|default:''}" required maxlength="5" oninput="if(this.value.length > 5) this.value = this.value.slice(0, 5);">
    </div>
    <div class="form-group">
        <label for="extNumber">Número Exterior</label>
        <input type="text" id="extNumber" name="extNumber" value="{$doctor.external_number|default:''}" required maxlength="8">
    </div>
    <div class="form-group">
        <label for="intNumber">Número Interior (Opcional)</label>
        <input type="text" id="intNumber" name="intNumber" value="{$doctor.internal_number|default:''}" maxlength="8">
    </div>
    <div class="form-group">
        <label for="state">Estado</label>
        <select name="state" id="state" required>
            <option value="">Seleccione...</option>
            {foreach from=$states item=state}
                <option value="{$state.id}" {if isset($doctor.state) && $doctor.state == $state.id}selected{/if}>
                    {$state.name}
                </option>
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
