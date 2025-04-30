<div class="form-step" id="step-1">
    <div class="form-group">
        <label for="lastName">Apellido Paterno</label>
        <input type="text" id="lastName" name="last_name" required>
    </div>
    <div class="form-group">
        <label for="motherLastName">Apellido Materno</label>
        <input type="text" id="motherLastName" name="last_name2" required>
    </div>
    <div class="form-group">
        <label for="firstName">Nombre</label>
        <input type="text" id="firstName" name="names" required>
    </div>
    <div class="form-group">
        <label for="phoneNumber">Número Telefónico</label>
        <input type="number" id="phoneNumber" name="phone" required>
    </div>
    <div class="form-group">
        <label for="email">Correo Electrónico</label>
        <input type="email" id="email" name="email" required>
    </div>
    <div class="form-group">
        <label for="gender">Sexo</label>
        <select id="gender" name="gender" required>
            <option value="">Seleccione...</option>
            <option value="M">Masculino</option>
            <option value="F">Femenino</option>
        </select>
    </div>
    <div class="form-group">
        <label for="birthDate">Fecha de Nacimiento</label>
        <input type="date" id="birthDate" name="birth_date" required>
    </div>
    <div class="form-group">
        <label for="curp">CURP</label>
        <input type="text" id="curp" name="CURP" required>
    </div>
    <div class="form-group">
        <label for="rfc">RFC</label>
        <input type="text" id="rfc" name="RFC" required>
    </div>
    <div class="form-group">
        <label for="affiliationNumber">Número de Afiliación</label>
        <input type="text" id="affiliationNumber" name="insurance_number" required>
    </div>
    <button type="button" class="next-btn" onclick="nextStep(2)">Siguiente</button>
</div>
