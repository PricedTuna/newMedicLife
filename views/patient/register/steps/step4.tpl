<input type="hidden" name="emergency_contacts_id" value="{$emergencyContacts.id|default:''}">

<div class="form-step" id="step-4">
<style>
    .required {
        color: red;
        margin-left: 2px;
    }
</style>
    <h2 class="formSubtitle">Contacto de Emergencia</h2>
    <p class="form-description">Por favor, proporcione la información de una persona a quien podamos contactar en caso de emergencia.</p>
    <div class="form-group">
        <label for="contactFirstName">Nombre del Contacto <span class="required">*</span>
            <span class="tooltip-container">
                <i class="bi bi-question-circle tooltip-icon"></i>
                <span class="tooltip-text">Ingrese el nombre completo de la persona a contactar en caso de emergencia. Máximo 50 caracteres.</span>
            </span>
        </label>
        <input type="text" id="contactFirstName"  value="{$emergencyContacts.names|default:''}" name="ec_name" required maxlength="50">
    </div>
    <div class="form-group">
        <label for="contactLastName">Apellido Paterno <span class="required">*</span>
            <span class="tooltip-container">
                <i class="bi bi-question-circle tooltip-icon"></i>
                <span class="tooltip-text">Ingrese el apellido paterno del contacto de emergencia. Máximo 50 caracteres.</span>
            </span>
        </label>
        <input type="text" id="contactLastName"  value="{$emergencyContacts.last_name|default:''}" name="ec_fatherLastName" required maxlength="50">
    </div>
    <div class="form-group">
        <label for="contactMotherLastName">Apellido Materno <span class="required">*</span>
            <span class="tooltip-container">
                <i class="bi bi-question-circle tooltip-icon"></i>
                <span class="tooltip-text">Ingrese el apellido materno del contacto de emergencia. Máximo 50 caracteres.</span>
            </span>
        </label>
        <input type="text" id="contactMotherLastName"  value="{$emergencyContacts.last_name2|default:''}" name="ec_motherLastName" required maxlength="50">
    </div>
    <div class="form-group">
        <label for="contactPhone">Número Telefónico <span class="required">*</span>
            <span class="tooltip-container">
                <i class="bi bi-question-circle tooltip-icon"></i>
                <span class="tooltip-text">Ingrese el número telefónico del contacto de emergencia. Debe tener 10 dígitos.</span>
            </span>
        </label>
        <input type="number" id="contactPhone"  value="{$emergencyContacts.phone|default:''}" name="ec_phoneNumber" required maxlength="10" oninput="if(this.value.length > 10) this.value = this.value.slice(0, 10);">
    </div>
    <div class="form-group">
        <label for="contactRelation">Relación con el Paciente <span class="required">*</span>
            <span class="tooltip-container">
                <i class="bi bi-question-circle tooltip-icon"></i>
                <span class="tooltip-text">Ingrese la relación del contacto con el paciente (ej. Padre, Madre, Hermano/a, Cónyuge). Máximo 50 caracteres.</span>
            </span>
        </label>
        <input type="text" id="contactRelation"  value="{$emergencyContacts.relationship|default:''}" name="ec_relationship" required maxlength="50">
    </div>
    <button type="button" class="prev-btn" onclick="prevStep(3)">Atrás</button>
    <button type="submit" class="submit-btn">Registrar</button>
</div>
