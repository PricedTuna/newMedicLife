<input type="hidden" name="doctor_id" value="{$doctor.id|default:''}">

<div class="form-step" id="step-1">

    <div class="form-group">
        <label for="lastName">Apellido Paterno</label>
        <input type="text" id="lastName" name="fatherLastName" value="{$doctor.last_name|default:''}" required>
    </div>
    <div class="form-group">
        <label for="motherLastName">Apellido Materno</label>
        <input type="text" id="motherLastName" name="motherLastName" value="{$doctor.last_name2|default:''}" required>
    </div>
    <div class="form-group">
        <label for="firstName">Nombre</label>
        <input type="text" id="firstName" name="name" value="{$doctor.names|default:''}" required>
    </div>
    <div class="form-group">
        <label for="phoneNumber">Número Telefónico</label>
        <input type="number" id="phoneNumber" name="phoneNumber" value="{$doctor.phone|default:''}" required>
    </div>
    <div class="form-group">
        <label for="email">Correo Electrónico</label>
        <input type="email" id="email" name="email" value="{$doctor.email|default:''}" required>
    </div>
    <div class="form-group">
        <label for="gender">Sexo</label>
        <select id="gender" name="gender" required>
            <option value="">Seleccione...</option>
            <option value="M" {if isset($doctor.gender) && $doctor.gender == 'M'}selected{/if}>Masculino</option>
            <option value="F" {if isset($doctor.gender) && $doctor.gender == 'F'}selected{/if}>Femenino</option>

        </select>
    </div>
    <div class="form-group">
        <label for="birthDate">Fecha de Nacimiento</label>
        <input type="date" id="birthDate" name="birthDate" value="{$doctor.birth_date|default:''}" required>
    </div>
    <button type="button" class="next-btn" onclick="nextStep(2)">Siguiente</button>
</div>
