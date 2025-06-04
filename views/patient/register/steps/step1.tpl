<input type="hidden" name="patient_id" value="{$patient.id|default:''}">

<div class="form-step" id="step-1">
<style>
    .required {
        color: red;
        margin-left: 2px;
    }
</style>
    <div class="form-group">
        <label for="lastName">Apellido Paterno
            <span class="tooltip-container">
                <i class="bi bi-question-circle tooltip-icon"></i>
                <span class="tooltip-text">Ingrese el apellido paterno del paciente. Máximo 30 caracteres.</span>
            </span>
        </label>
        <input type="text" id="lastName" value="{$patient.last_name|default:''}" name="fatherLastName" required maxlength="30">
    </div>
    <div class="form-group">
        <label for="motherLastName">Apellido Materno
            <span class="tooltip-container">
                <i class="bi bi-question-circle tooltip-icon"></i>
                <span class="tooltip-text">Ingrese el apellido materno del paciente. Máximo 30 caracteres.</span>
            </span>
        </label>
        <input type="text" id="motherLastName" value="{$patient.last_name2|default:''}" name="motherLastName" required maxlength="30">
    </div>
    <div class="form-group">
        <label for="firstName">Nombre
            <span class="tooltip-container">
                <i class="bi bi-question-circle tooltip-icon"></i>
                <span class="tooltip-text">Ingrese el nombre del paciente. Máximo 30 caracteres.</span>
            </span>
        </label>
        <input type="text" id="firstName" value="{$patient.names|default:''}" name="name" required maxlength="30">
    </div>
    <div class="form-group">
        <label for="phoneNumber">Número Telefónico
            <span class="tooltip-container">
                <i class="bi bi-question-circle tooltip-icon"></i>
                <span class="tooltip-text">Ingrese el número telefónico del paciente. Debe tener 10 dígitos.</span>
            </span>
        </label>
        <input type="number" id="phoneNumber" value="{$patient.phone|default: ''}" name="phoneNumber" required maxlength="10" oninput="if(this.value.length > 10) this.value = this.value.slice(0, 10);">
    </div>
    <div class="form-group">
        <label for="email">Correo Electrónico
            <span class="tooltip-container">
                <i class="bi bi-question-circle tooltip-icon"></i>
                <span class="tooltip-text">Ingrese el correo electrónico del paciente. Debe tener un formato válido (ejemplo@dominio.com).</span>
            </span>
        </label>
        <input type="email" id="email" value="{$patient.email|default: ''}" name="email" required maxlength="100">
    </div>
    <div class="form-group">
        <label for="gender">Sexo
            <span class="tooltip-container">
                <i class="bi bi-question-circle tooltip-icon"></i>
                <span class="tooltip-text">Seleccione el sexo biológico del paciente.</span>
            </span>
        </label>
        <select id="gender" name="gender" required>
            <option value="">Seleccione...</option>
            <option value="M" {if isset($patient) && $patient.gender == 'M'}selected{/if}>Masculino</option>
            <option value="F" {if isset($patient) && $patient.gender == 'F'}selected{/if}>Femenino</option>
        </select>
    </div>
    <div class="form-group">
        <label for="birthDate">Fecha de Nacimiento
            <span class="tooltip-container">
                <i class="bi bi-question-circle tooltip-icon"></i>
                <span class="tooltip-text">Seleccione la fecha de nacimiento del paciente.</span>
            </span>
        </label>
        <input type="date" id="birthDate" value="{$patient.birth_date|default: ''}" name="birthDate" required>
    </div>
    <div class="form-group">
        <label for="curp">CURP
            <span class="tooltip-container">
                <i class="bi bi-question-circle tooltip-icon"></i>
                <span class="tooltip-text">Ingrese la Clave Única de Registro de Población del paciente. Debe tener 18 caracteres.</span>
            </span>
        </label>
        <input type="text" id="curp" value="{$patient.CURP|default: ''}" name="curp" required maxlength="18">
    </div>
    <div class="form-group">
        <label for="rfc">RFC
            <span class="tooltip-container">
                <i class="bi bi-question-circle tooltip-icon"></i>
                <span class="tooltip-text">Ingrese el Registro Federal de Contribuyentes del paciente. Debe tener 13 caracteres.</span>
            </span>
        </label>
        <input type="text" id="rfc" value="{$patient.RFC|default: ''}" name="rfc" required maxlength="13">
    </div>
    <div class="form-group">
        <label for="photo" class="file-label" id="photo-label">
            <i class="bi bi-cloud-arrow-up"></i> Subir Foto <span class="required">*</span>
            <span class="tooltip-container">
                <i class="bi bi-question-circle tooltip-icon"></i>
                <span class="tooltip-text">Suba una fotografía del paciente para su perfil. Formatos aceptados: JPG, PNG, GIF.</span>
            </span>
        </label>
        <input type="file" id="photo" name="photo" accept="image/*" {if !$patient}required{/if} style="display: none;">
        <div class="photo-requirements">
            <small><span class="required">*</span> La foto de perfil es obligatoria</small>
        </div>
        <div id="patient-image-preview-container" class="image-preview-container">
            <img id="patient-image-preview" class="image-preview" src="{if isset($patient) && $patient.id}/controllers/patient/mostrar_foto.php?id={$patient.id}{/if}" alt="Vista previa" style="{if !isset($patient) || !$patient.id}display: none;{else}display: block;{/if}">
            <div id="patient-preview-placeholder" class="preview-placeholder" style="{if isset($patient) && $patient.id}display: none;{else}display: flex;{/if}">
                <i class="bi bi-image"></i>
                <span>Vista previa de la imagen</span>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label for="affiliationNumber">Número de Afiliación
            <span class="tooltip-container">
                <i class="bi bi-question-circle tooltip-icon"></i>
                <span class="tooltip-text">Ingrese el número de afiliación al seguro social del paciente. Máximo 20 caracteres.</span>
            </span>
        </label>
        <input type="text" id="affiliationNumber" value="{$patient.insurance_number|default: ''}" name="affiliationNumber" required maxlength="20">
    </div>
    <button type="button" class="next-btn" onclick="nextStep(2)">Siguiente</button>
</div>
