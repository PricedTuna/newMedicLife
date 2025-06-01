<input type="hidden" name="emergency_contacts_id" value="{$emergencyContacts.id|default:''}">

<div class="form-step" id="step-4">
    <h2 class="formSubtitle">Contacto de emergencía</h1>
    <div class="form-group">
        <label for="contactFirstName">Nombre del Contacto</label>
        <input type="text" id="contactFirstName"  value="{$emergencyContacts.names|default:''}" name="ec_name" required>
    </div>
    <div class="form-group">
        <label for="contactLastName">Apellido Paterno</label>
        <input type="text" id="contactLastName"  value="{$emergencyContacts.last_name|default:''}" name="ec_fatherLastName" required>
    </div>
    <div class="form-group">
        <label for="contactMotherLastName">Apellido Materno</label>
        <input type="text" id="contactMotherLastName"  value="{$emergencyContacts.last_name2|default:''}" name="ec_motherLastName" required>
    </div>
    <div class="form-group">
        <label for="contactPhone">Número Telefónico</label>
        <input type="number" id="contactPhone"  value="{$emergencyContacts.phone|default:''}" name="ec_phoneNumber" required>
    </div>
    <div class="form-group">
        <label for="contactRelation">Relación con el Paciente</label>
        <input type="text" id="contactRelation"  value="{$emergencyContacts.relationship|default:''}" name="ec_relationship" required>
    </div>
    <button type="button" class="prev-btn" onclick="prevStep(3)">Atrás</button>
    <button type="submit" class="submit-btn">Registrar</button>
</div>
