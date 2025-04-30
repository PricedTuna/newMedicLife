<!-- Paso 2 -->
<div class="form-step" id="step-2" style="display: none;">
    <div class="form-group">
        <label for="street">Calle</label>
        <input type="text" id="street" name="street" value="{$doctor.street|default:''}" required>
    </div>
    <div class="form-group">
        <label for="neighborhood">Colonia</label>
        <input type="text" id="neighborhood" name="neighborhood" value="{$doctor.neighborhood|default:''}" required>
    </div>
    <div class="form-group">
        <label for="postalCode">Código Postal</label>
        <input type="number" id="postalCode" name="postalCode" value="{$doctor.CP|default:''}" required>
    </div>
    <div class="form-group">
        <label for="extNumber">Número Exterior</label>
        <input type="text" id="extNumber" name="extNumber" value="{$doctor.external_number|default:''}" required>
    </div>
    <div class="form-group">
        <label for="intNumber">Número Interior (Opcional)</label>
        <input type="text" id="intNumber" name="intNumber" value="{$doctor.internal_number|default:''}">
    </div>
    <div class="form-group">
        <label for="state">Estado</label>
        <select name="state" id="state" required>
            <option value="">Seleccione...</option>
            {foreach from=$states item=state}
                <option value="{$state.id}" {if $doctor.state == $state.id}selected{/if}>
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
