<div class="form-step" id="step-3" style="display: none;">
    <div class="form-group">
        <label for="bloodType">Tipo de Sangre</label>
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
        <label for="maritalStatus">Estado Civil</label>
        <select id="maritalStatus" name="marital_status" required>
            <option value="">Seleccione...</option>
            <option value="Soltero(a)" {if $patient.marital_status == "Soltero(a)"}selected{/if}>Soltero(a)</option>
            <option value="Casado(a)" {if $patient.marital_status == "Casado(a)"}selected{/if}>Casado(a)</option>
            <option value="Viudo(a)" {if $patient.marital_status == "Viudo(a)"}selected{/if}>Viudo(a)</option>
            <option value="Unión libre" {if $patient.marital_status == "Unión libre"}selected{/if}>Unión libre</option>
        </select>
    </div>


    <div class="form-group">
        <label for="weight">Peso (kg)</label>
        <input type="number" id="weight" value="{$patient.weight|default: ''}" name="weight" required>
    </div>
    <div class="form-group">
        <label for="height">Altura (cm)</label>
        <input type="number" id="height" value="{$patient.height|default: ''}" name="height" required>
    </div>
    <div class="form-group">
        <label for="ethnicGroup">Grupo Étnico</label>
        <input type="text" id="ethnicGroup" value="{$patient.ethnic_group|default: ''}" name="ethnic_group">
    </div>
    <div class="form-group">
        <label for="religion">Religión</label>
        <input type="text" id="religion" value="{$patient.religion|default: ''}" name="religion">
    </div>
    <button type="button" class="prev-btn" onclick="prevStep(2)">Atrás</button>
    <button type="button" class="next-btn" onclick="nextStep(4)">Siguiente</button>
</div>