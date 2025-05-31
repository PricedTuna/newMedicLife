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
        <input type="text" id="affiliationNumber" name="affiliationNumber" value="{$doctor.insurance_number|default:''}"
            required>
    </div>
    <div class="form-group">
        <label for="professionalLicense">Cédula Profesional</label>
        <input type="text" id="professionalLicense" name="professionalLicense"
            value="{$doctor.professional_id|default:''}" required>
    </div>

    <div class="form-group">
        <label for="speciality">Especialidad</label>
        <select name="medical_area" id="speciality" required>
            {foreach from=$medical_areas item=medical_area}
                <option value="{$medical_area.id}"
                    {if isset($doctor.id_medical_area) && $doctor.id_medical_area == $medical_area.id}selected{/if}>
                    {$medical_area.area_name}
                </option>
            {/foreach}
        </select>
    </div>

    <h3 class="section-title">Horarios de Atención</h3>

    {assign var=days value=["Monday" => "Lunes", "Tuesday" => "Martes", "Wednesday" => "Miércoles", "Thursday" => "Jueves", "Friday" => "Viernes", "Saturday" => "Sábado", "Sunday" => "Domingo"]}

    <div class="schedule-container">
        {foreach from=$days key=key item=label}
            <div class="schedule-row">
                <label class="day-label">
                    <input type="checkbox" name="schedule[{$key}][active]" id="{$key}_active"
                        {if isset($schedules[$key])}checked{/if}>
                    {$label}
                </label>
                <div class="schedule-time-inputs">
                    <div class="time-field">
                        <label for="{$key}_start">Inicio</label>
                        <input type="time" name="schedule[{$key}][start_time]" id="{$key}_start"
                            value="{if isset($schedules[$key])}{$schedules[$key].start_time}{/if}">
                    </div>
                    <div class="time-field">
                        <label for="{$key}_end">Fin</label>
                        <input type="time" name="schedule[{$key}][end_time]" id="{$key}_end"
                            value="{if isset($schedules[$key])}{$schedules[$key].end_time}{/if}">
                    </div>
                </div>
            </div>
        {/foreach}
    </div>


    <div class="form-group">
        <label for="photo" class="file-label" id="photo-label">
            <i class="bi bi-cloud-arrow-up"></i> Subir Foto
        </label>
        <input type="file" id="photo" name="photo" accept="image/*" {if !$doctor}required{/if}>
        <div id="doctor-image-preview-container" class="image-preview-container">
            <img id="doctor-image-preview" class="image-preview" src="{if $doctor}/controllers/doctor/mostrar_foto.php?id={$doctor.id}{/if}" alt="Vista previa" style="display: {if $doctor}block{else}none{/if};">
            <div id="doctor-preview-placeholder" class="preview-placeholder" style="display: {if $doctor}none{else}flex{/if};">
                <i class="bi bi-image"></i>
                <span>Vista previa de la imagen</span>
            </div>
        </div>
    </div>



    <button type="button" class="prev-btn" onclick="prevStep(2)">Atrás</button>
    <button type="submit" class="submit-btn">
        {if $doctor}Actualizar{else}Registrar{/if}
    </button>
    <input type="hidden" name="id" value="{$doctor.id|default:''}">
</div>
