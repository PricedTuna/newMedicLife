<input type="hidden" name="patient_id" value="{$patient.id|default:''}">

<div class="form-step" id="step-1">
    <div class="form-group">
        <label for="lastName">Apellido Paterno</label>
        <input type="text" id="lastName" value="{$patient.last_name|default:''}" name="fatherLastName"" required>
    </div>
    <div class="form-group">
        <label for="motherLastName">Apellido Materno</label>
        <input type="text" id="motherLastName" value="{$patient.last_name2|default:''}" name="motherLastName" required>
    </div>
    <div class="form-group">
        <label for="firstName">Nombre</label>
        <input type="text" id="firstName" value="{$patient.names|default:''}" name="name" required>
    </div>
    <div class="form-group">
        <label for="phoneNumber">Número Telefónico</label>
        <input type="number" id="phoneNumber" value="{$patient.phone|default: ''}" name="phoneNumber" required>
    </div>
    <div class="form-group">
        <label for="email">Correo Electrónico</label>
        <input type="email" id="email" value="{$patient.email|default: ''}" name="email" required>
    </div>
    <div class="form-group">
        <label for="gender">Sexo</label>
        <select id="gender" name="gender" required>
            <option value="">Seleccione...</option>
            <option value="M" {if $patient.gender == 'M'}selected{/if}>Masculino</option>
            <option value="F" {if $patient.gender == 'F'}selected{/if}>Femenino</option>
        </select>
    </div>
    <div class="form-group">
        <label for="birthDate">Fecha de Nacimiento</label>
        <input type="date" id="birthDate" value="{$patient.birth_date|default: ''}" name="birthDate" required>
    </div>
    <div class="form-group">
        <label for="curp">CURP</label>
        <input type="text" id="curp" value="{$patient.CURP|default: ''}" name="curp" required>
    </div>
    <div class="form-group">
        <label for="rfc">RFC</label>
        <input type="text" id="rfc" value="{$patient.RFC|default: ''}" name="rfc" required>
    </div>
    <div class="form-group">
        <label for="affiliationNumber">Número de Afiliación</label>
        <input type="text" id="affiliationNumber" value="{$patient.insurance_number|default: ''}" name="affiliationNumber" required>
    </div>
    <button type="button" class="next-btn" onclick="nextStep(2)">Siguiente</button>
</div>