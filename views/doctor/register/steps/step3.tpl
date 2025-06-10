<div class="form-step" id="step-3" style="display: none;">
<style>
    .required {
        color: red;
        margin-left: 2px;
    }
</style>
    <div class="form-group">
        <label for="curp">CURP *
            <span class="tooltip-container">
                <i class="bi bi-question-circle tooltip-icon"></i>
                <span class="tooltip-text">Ingrese la Clave Única de Registro de Población del médico. Debe tener 18 caracteres.</span>
            </span>
        </label>
        <input type="text" id="curp" name="curp" value="{$doctor.CURP|default:''}" required maxlength="18">
    </div>
    <div class="form-group">
        <label for="rfc">RFC *
            <span class="tooltip-container">
                <i class="bi bi-question-circle tooltip-icon"></i>
                <span class="tooltip-text">Ingrese el Registro Federal de Contribuyentes del médico. Debe tener 13 caracteres.</span>
            </span>
        </label>
        <input type="text" id="rfc" name="rfc" value="{$doctor.RFC|default:''}" required maxlength="13">
    </div>
    <div class="form-group">
        <label for="affiliationNumber">Número de Afiliación *
            <span class="tooltip-container">
                <i class="bi bi-question-circle tooltip-icon"></i>
                <span class="tooltip-text">Ingrese el número de afiliación al seguro social del médico. Máximo 20 caracteres.</span>
            </span>
        </label>
        <input type="text" id="affiliationNumber" name="affiliationNumber" value="{$doctor.insurance_number|default:''}"
            required maxlength="20">
    </div>
    <div class="form-group">
        <label for="professionalLicense">Cédula Profesional *
            <span class="tooltip-container">
                <i class="bi bi-question-circle tooltip-icon"></i>
                <span class="tooltip-text">Ingrese el número de cédula profesional del médico. Máximo 15 caracteres.</span>
            </span>
        </label>
        <input type="text" id="professionalLicense" name="professionalLicense"
            value="{$doctor.professional_id|default:''}" required maxlength="15">
        <div id="professionalLicense-error" class="error-message" style="color: red; display: none;"></div>
    </div>

    <div class="form-group">
        <label for="speciality">Especialidad *
            <span class="tooltip-container">
                <i class="bi bi-question-circle tooltip-icon"></i>
                <span class="tooltip-text">Seleccione la especialidad médica del doctor.</span>
            </span>
        </label>
        <select name="medical_area" id="speciality" required>
            {foreach from=$medical_areas item=medical_area}
                <option value="{$medical_area.id}"
                    {if isset($doctor.id_medical_area) && $doctor.id_medical_area == $medical_area.id}selected{/if}>
                    {$medical_area.area_name}
                </option>
            {/foreach}
        </select>
    </div>

    <h3 class="section-title">Horarios de Atención *</h3>
    <div style="margin-bottom: 10px; font-size: 0.9em;">
        <small>* Debe ingresar al menos un día con horario válido</small>
    </div>

    {assign var=days value=["Monday" => "Lunes", "Tuesday" => "Martes", "Wednesday" => "Miércoles", "Thursday" => "Jueves", "Friday" => "Viernes", "Saturday" => "Sábado", "Sunday" => "Domingo"]}

    <div class="schedule-container">
        {foreach from=$days key=key item=label}
            <div class="schedule-row">
                <label class="day-label">
                    <input type="checkbox" class="day-active-checkbox" name="schedule[{$key}][active]" id="{$key}_active"
                        data-day="{$key}" {if isset($schedules[$key])}checked{/if}>
                    {$label}
                </label>

                <div class="schedule-time-inputs">
                    <div class="time-field">
                        <label for="{$key}_start">Inicio</label>
                        <input type="time" class="start-time" name="schedule[{$key}][start_time]" id="{$key}_start"
                            data-day="{$key}" value="{if isset($schedules[$key])}{$schedules[$key].start_time}{/if}">
                    </div>

                    <div class="time-field">
                        <label for="{$key}_end">Fin</label>
                        <input type="time" class="end-time" name="schedule[{$key}][end_time]" id="{$key}_end"
                            data-day="{$key}" value="{if isset($schedules[$key])}{$schedules[$key].end_time}{/if}">
                    </div>
                </div>

                <div class="error-message" id="error_{$key}" style="color: red; font-size: 0.9em; display: none;"></div>
            </div>
        {/foreach}

        <div id="schedule-error-message" style="color: red; font-size: 0.95em; margin-top: 10px; display: none;">
            Debe ingresar al menos un horario con hora de inicio y fin válida.
        </div>
    </div>




    <div class="form-group">
        <label for="photo" class="file-label" id="photo-label">
            <i class="bi bi-cloud-arrow-up"></i> Subir Foto *
            <span class="tooltip-container">
                <i class="bi bi-question-circle tooltip-icon"></i>
                <span class="tooltip-text">Suba una fotografía del médico para su perfil. Formatos aceptados: JPG, PNG, GIF.</span>
            </span>
        </label>
        <input type="file" id="photo" name="photo" accept="image/*" {if !$doctor}required{/if} style="display: none;">
        <div class="photo-requirements">
            <small>* La foto de perfil es obligatoria</small>
        </div>
        <div id="doctor-image-preview-container" class="image-preview-container">
            <img id="doctor-image-preview" class="image-preview"
                src="{if $doctor}/controllers/doctor/mostrar_foto.php?id={$doctor.id}{/if}" alt="Vista previa"
                style="display: {if $doctor}block{else}none{/if};">
            <div id="doctor-preview-placeholder" class="preview-placeholder"
                style="display: {if $doctor}none{else}flex{/if};">
                <i class="bi bi-image"></i>
                <span>Vista previa de la imagen</span>
            </div>
        </div>
    </div>




    <button type="button" class="prev-btn" onclick="prevStep(2)">Atrás</button>
    <button type="button" class="submit-btn" id="submit-doctor-btn">
        {if $doctor}Actualizar{else}Registrar{/if}
    </button>
    <input type="hidden" name="id" value="{$doctor.id|default:''}">
</div>
