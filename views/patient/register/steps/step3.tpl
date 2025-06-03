<div class="form-step" id="step-3" style="display: none;">
    <div class="form-group">
        <label for="bloodType">Tipo de Sangre <span class="required">*</span></label>
        <select id="bloodType" name="blood_type" required>
            <option value="">Seleccione...</option>
            <option value="A+" {if isset($patient) && $patient.blood_type == 'A+'}selected{/if}>A+</option>
            <option value="A-" {if isset($patient) && $patient.blood_type == 'A-'}selected{/if}>A-</option>
            <option value="B+" {if isset($patient) && $patient.blood_type == 'B+'}selected{/if}>B+</option>
            <option value="B-" {if isset($patient) && $patient.blood_type == 'B-'}selected{/if}>B-</option>
            <option value="AB+" {if isset($patient) && $patient.blood_type == 'AB+'}selected{/if}>AB+</option>
            <option value="AB-" {if isset($patient) && $patient.blood_type == 'AB-'}selected{/if}>AB-</option>
            <option value="O+" {if isset($patient) && $patient.blood_type == 'O+'}selected{/if}>O+</option>
            <option value="O-" {if isset($patient) && $patient.blood_type == 'O-'}selected{/if}>O-</option>
        </select>
    </div>
    <div class="form-group">
        <label for="maritalStatus">Estado Civil <span class="required">*</span></label>
        <select id="maritalStatus" name="marital_status" required>
            <option value="">Seleccione...</option>
            <option value="Soltero(a)" {if isset($patient) && $patient.marital_status == "Soltero(a)"}selected{/if}>Soltero(a)</option>
            <option value="Casado(a)" {if isset($patient) && $patient.marital_status == "Casado(a)"}selected{/if}>Casado(a)</option>
            <option value="Viudo(a)" {if isset($patient) && $patient.marital_status == "Viudo(a)"}selected{/if}>Viudo(a)</option>
            <option value="Unión libre" {if isset($patient) && $patient.marital_status == "Unión libre"}selected{/if}>Unión libre</option>
        </select>
    </div>


    <div class="form-group">
        <label for="weight">Peso (kg) <span class="required">*</span></label>
        <input type="number" id="weight" value="{$patient.weight|default: ''}" name="weight" required step="0.01" min="0" max="999.99" oninput="validateWeight(this);">
    </div>
    <div class="form-group">
        <label for="height">Altura (cm) <span class="required">*</span></label>
        <input type="number" id="height" value="{$patient.height|default: ''}" name="height" required step="0.01" min="0" max="300" oninput="validateHeight(this);">
    </div>
    <div class="form-group">
        <label for="ethnicGroup">Grupo Étnico
            <span class="info-tooltip">
                <i class="bi bi-info-circle"></i>
                <span class="tooltip-text">Información sobre el grupo étnico al que pertenece el paciente. Este dato es importante para considerar factores de riesgo específicos.</span>
            </span>
        </label>
        <input type="text" id="ethnicGroup" value="{$patient.ethnic_group|default: ''}" name="ethnic_group" maxlength="50">
    </div>
    <div class="form-group">
        <label for="religion">Religión
            <span class="info-tooltip">
                <i class="bi bi-info-circle"></i>
                <span class="tooltip-text">Información sobre la religión del paciente. Este dato puede ser relevante para ciertas decisiones médicas y consideraciones de tratamiento.</span>
            </span>
        </label>
        <input type="text" id="religion" value="{$patient.religion|default: ''}" name="religion" maxlength="50">
    </div>
    <button type="button" class="prev-btn" onclick="prevStep(2)">Atrás</button>
    <button type="button" class="next-btn" onclick="nextStep(4)">Siguiente</button>
</div>

<style>
    .required {
        color: red;
        margin-left: 2px;
    }
    .info-tooltip {
        position: relative;
        display: inline-block;
        margin-left: 5px;
        cursor: help;
    }
    .info-tooltip i {
        color: #007bff;
    }
    .info-tooltip .tooltip-text {
        visibility: hidden;
        width: 200px;
        background-color: #555;
        color: #fff;
        text-align: center;
        border-radius: 6px;
        padding: 5px;
        position: absolute;
        z-index: 1;
        bottom: 125%;
        left: 50%;
        margin-left: -100px;
        opacity: 0;
        transition: opacity 0.3s;
    }
    .info-tooltip .tooltip-text::after {
        content: "";
        position: absolute;
        top: 100%;
        left: 50%;
        margin-left: -5px;
        border-width: 5px;
        border-style: solid;
        border-color: #555 transparent transparent transparent;
    }
    .info-tooltip:hover .tooltip-text {
        visibility: visible;
        opacity: 1;
    }
</style>
