<input type="hidden" name="doctor_id" value="{$doctor.id|default:''}">

<div class="form-step" id="step-1">

    <div class="form-group">
        <label for="lastName">Apellido Paterno *
            <span class="tooltip-container">
                <i class="bi bi-question-circle tooltip-icon"></i>
                <span class="tooltip-text">Ingrese el apellido paterno del médico. Máximo 30 caracteres.</span>
            </span>
        </label>
        <input type="text" id="lastName" name="fatherLastName" value="{$doctor.last_name|default:''}" required maxlength="30">
    </div>
    <div class="form-group">
        <label for="motherLastName">Apellido Materno *
            <span class="tooltip-container">
                <i class="bi bi-question-circle tooltip-icon"></i>
                <span class="tooltip-text">Ingrese el apellido materno del médico. Máximo 30 caracteres.</span>
            </span>
        </label>
        <input type="text" id="motherLastName" name="motherLastName" value="{$doctor.last_name2|default:''}" required maxlength="30">
    </div>
    <div class="form-group">
        <label for="firstName">Nombre *
            <span class="tooltip-container">
                <i class="bi bi-question-circle tooltip-icon"></i>
                <span class="tooltip-text">Ingrese el nombre del médico. Máximo 30 caracteres.</span>
            </span>
        </label>
        <input type="text" id="firstName" name="name" value="{$doctor.names|default:''}" required maxlength="30">
    </div>
    <div class="form-group">
        <label for="phoneNumber">Número Telefónico *
            <span class="tooltip-container">
                <i class="bi bi-question-circle tooltip-icon"></i>
                <span class="tooltip-text">Ingrese el número telefónico del médico. Debe tener 10 dígitos.</span>
            </span>
        </label>
        <input type="number" id="phoneNumber" name="phoneNumber" value="{$doctor.phone|default:''}" required maxlength="10" oninput="if(this.value.length > 10) this.value = this.value.slice(0, 10);">
    </div>
    <div class="form-group">
        <label for="email">Correo Electrónico *
            <span class="tooltip-container">
                <i class="bi bi-question-circle tooltip-icon"></i>
                <span class="tooltip-text">Ingrese el correo electrónico del médico. Debe tener un formato válido (ejemplo@dominio.com).</span>
            </span>
        </label>
        <input type="email" id="email" name="email" value="{$doctor.email|default:''}" required maxlength="100">
    </div>
    <div class="form-group">
        <label for="gender">Sexo *
            <span class="tooltip-container">
                <i class="bi bi-question-circle tooltip-icon"></i>
                <span class="tooltip-text">Seleccione el sexo biológico del médico.</span>
            </span>
        </label>
        <select id="gender" name="gender" required>
            <option value="">Seleccione...</option>
            <option value="M" {if isset($doctor.gender) && $doctor.gender == 'M'}selected{/if}>Masculino</option>
            <option value="F" {if isset($doctor.gender) && $doctor.gender == 'F'}selected{/if}>Femenino</option>

        </select>
    </div>
    <div class="form-group">
        <label for="birthDate">Fecha de Nacimiento *
            <span class="tooltip-container">
                <i class="bi bi-question-circle tooltip-icon"></i>
                <span class="tooltip-text">Seleccione la fecha de nacimiento del médico. Debe ser mayor de edad.</span>
            </span>
        </label>
        <input type="date" id="birthDate" name="birthDate" value="{$doctor.birth_date|default:''}" required>
    </div>
    <button type="button" class="next-btn" onclick="nextStep(2)">Siguiente</button>
</div>
