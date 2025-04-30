<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./solicitar-cita.styles.css">
    <title>Solicitar Cita</title>
</head>
<body>

    <div class="center-container">
        <div class="form-container">
            <h2>Solicitar Citas</h2>
            <form id="solicitarCita">
                <div class="form-group">
                    <label for="curp">CURP</label>
                    <input type="text" name="CURP" required>
                </div>
                
                <div class="form-group">
                    <label for="patientId">Número de identificación del paciente</label>
                    <input type="text" name="patientId" required>
                </div>
                
                <div class="form-group">
                    <label for="specialty">Especialidad</label>
                    <select name="specialty" required>
                        <option value="">Seleccione...</option>
                        <option value="Cardiología">Cardiología</option>
                        <option value="Dermatología">Dermatología</option>
                        <option value="Pediatría">Pediatría</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="doctor">Nombre del Médico</label>
                    <select name="doctor" required>
                        <option value="">Seleccione...</option>
                        <option value="Dr. Juan Pérez">Dr. Juan Pérez</option>
                        <option value="Dra. María López">Dra. María López</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="consultingRoom">Consultorio</label>
                    <input type="text" name="consultingRoom" required>
                </div>
                
                <div class="form-group">
                    <label for="appointmentDate">Hora y Fecha</label>
                    <input type="datetime-local" name="appointmentDate" required>
                </div>
                
                <button type="submit" class="submit-btn">Registrar Datos</button>
            </form>
        </div>
    </div>

</body>
</html>
