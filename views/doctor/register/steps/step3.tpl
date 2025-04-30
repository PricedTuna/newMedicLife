<!-- Paso 3 -->
<div class="form-step" id="step-3" style="display: none;">
    <div class="form-group">
        <label for="curp">CURP</label>
        <input type="text" id="curp" name="curp" value="{$doctor.CURP|default:''}" required>
    </div>
    <div class="form-group">
        <label for="rfc">RFC</label>
        <input type="text" id="rfc" name="rfc" value="{$doctor.RFC|default:''}" required>
    </div>
    <div class="form-group">
        <label for="affiliationNumber">Número de Afiliación</label>
        <input type="text" id="affiliationNumber" name="affiliationNumber" value="{$doctor.insurance_number|default:''}" required>
    </div>
    <div class="form-group">
        <label for="professionalLicense">Cédula Profesional</label>
        <input type="text" id="professionalLicense" name="professionalLicense" value="{$doctor.professional_id|default:''}" required>
    </div>

    <div class="form-group">
        <label for="speciality">Especialidad</label>
        <select name="medical_area" id="speciality" required>
            {foreach from=$medical_areas item=medical_area}
                <option value="{$medical_area.id}" {if $doctor.medical_area == $medical_area.id}selected{/if}>
                    {$medical_area.name}
                </option>
            {/foreach}
        </select>
    </div>

    <div class="form-group">
        <label for="photo" class="file-label" id="photo-label">Subir Foto</label>
        <input type="file" id="photo" name="photo" accept="image/*" {if !$doctor}required{/if}>
    </div>

    <button type="button" class="prev-btn" onclick="prevStep(2)">Atrás</button>
    <button type="submit" class="submit-btn">
        {if $doctor}Actualizar{else}Registrar{/if}
    </button>
    <input type="hidden" name="id" value="{$doctor.id|default:''}">
</div>
