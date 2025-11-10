<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        {if $formType == 'machine'}
            {if $machineData}Editar Máquina{else}Agregar Máquina{/if}
        {elseif $formType == 'type'}
            {if $typeData}Editar Tipo de Máquina{else}Agregar Tipo{/if}
        {/if} | Medic Life
    </title>

    <!-- 🧩 Estilos comunes -->
    <link rel="stylesheet" href="/assets/css/common.css">
    <link rel="stylesheet" href="/assets/css/forms.css">
    <link rel="stylesheet" href="/views/components/sidebar.styles.css">
    <link rel="stylesheet" href="/assets/css/usability-improvements.css">

    <!-- 🧩 Estilos específicos -->
    <link rel="stylesheet" href="/views/machines/create/create-machine.styles.css">

    <link rel="icon" href="/views/dashboard/icons/Untitled-design-_1_.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="/views/components/sidebar.app.js" defer></script>
    <script src="/views/machines/create/create-machine.app.js" defer></script>
</head>

<body>
    {include file=$sidebarPath}

    <main>
        <div class="main-content">
            <div class="page-header">
                <h1>
                    {if $formType == 'machine'}
                        {if $machineData}Editar Máquina{else}Agregar Máquina{/if}
                    {elseif $formType == 'type'}
                        {if $typeData}Editar Tipo de Máquina{else}Agregar Tipo{/if}
                    {/if}
                </h1>
            </div>

            {if isset($form_result)}
                {if $form_result.success}
                    <script>
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: '{$form_result.message|escape}',
                            confirmButtonText: 'Aceptar'
                        }).then(() => {
                            window.location.href = '/views/machines/list/list-machines.view.php';
                        });
                    </script>
                {else}
                    <script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            html: '{$form_result.message|escape}'
                        });
                    </script>
                {/if}
            {/if}

            {if $formType == 'machine'}
                <!-- ===================== FORMULARIO DE MÁQUINA ===================== -->
                <div class="form-container">
                    <form action="/controllers/machines/create-machine.controller.php" method="POST">
                        {if $machineData}
                            <input type="hidden" name="id" value="{$machineData.id|default:''}">
                            <input type="hidden" name="action" value="edit">
                        {else}
                            <input type="hidden" name="action" value="create">
                        {/if}

                        <div class="form-group">
                            <label for="name">Nombre de la Máquina <span class="required">*</span></label>
                            <input type="text" id="name" name="name" required maxlength="100"
                                placeholder="Ej. Monitor cardíaco" value="{$machineData.name|default:''}">
                        </div>

                        <div class="form-group">
                            <label for="model">Modelo <span class="required">*</span></label>
                            <input type="text" id="model" name="model" required maxlength="50"
                                placeholder="Ej. Philips M2601A" value="{$machineData.model|default:''}">
                        </div>

                        <div class="form-group">
                            <label for="id_machine_type">Tipo de Máquina <span class="required">*</span></label>
                            <select id="id_machine_type" name="id_machine_type" required>
                                <option value="">Seleccione un tipo</option>
                                {foreach from=$machine_types item=type}
                                    <option value="{$type.id}"
                                        {if $machineData && $machineData.id_machine_type == $type.id}selected{/if}>
                                        {$type.name|escape}
                                    </option>
                                {/foreach}
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="id_medical_area">Área Médica <span class="required">*</span></label>
                            <select id="id_medical_area" name="id_medical_area" required>
                                <option value="">Seleccione un área</option>
                                {foreach from=$medical_areas item=area}
                                    <option value="{$area.id}"
                                        {if $machineData && $machineData.id_medical_area == $area.id}selected{/if}>
                                        {$area.name|escape}
                                    </option>
                                {/foreach}
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="operational_status">Estado Operativo <span class="required">*</span></label>
                            <select id="operational_status" name="operational_status" required>
                                <option value="">Seleccione un estado</option>
                                <option value="Operativa"
                                    {if $machineData && $machineData.operational_status == 'Operativa'}selected{/if}>
                                    Operativa</option>
                                <option value="Mantenimiento"
                                    {if $machineData && $machineData.operational_status == 'Mantenimiento'}selected{/if}>
                                    Mantenimiento</option>
                                <option value="Fuera de servicio"
                                    {if $machineData && $machineData.operational_status == 'Fuera de servicio'}selected{/if}>
                                    Fuera de servicio</option>
                            </select>
                        </div>

                        <!-- Ventanas operativas -->
                        <div class="form-group">
                            <label>Ventanas Operativas</label>
                            <div class="default-windows-container"
                                style="display:flex; align-items:center; gap:10px; margin-bottom:10px;">
                                <label>Inicio:</label>
                                <input type="time" id="default-window-start" value="08:00" style="width:120px;">
                                <label>Fin:</label>
                                <input type="time" id="default-window-end" value="17:00" style="width:120px;">
                                <button type="button" id="default-windows-btn">⏰ Establecer horarios por defecto</button>
                            </div>

                            <div id="operational-windows-container">
                                {assign var="days" value=["Lunes","Martes","Miércoles","Jueves","Viernes","Sábado","Domingo"]}
                                {if isset($operationalWindows) && $operationalWindows|@count > 0}
                                    {foreach from=$operationalWindows item=win key=index}
                                        <div class="operational-window" data-id="{$win.id}">
                                            <select name="operational_windows[{$index}][day_of_week]" required>
                                                {foreach from=$days item=day}
                                                    <option value="{$day}" {if $win.day_of_week == $day}selected{/if}>{$day}</option>
                                                {/foreach}
                                            </select>
                                            <input type="time" name="operational_windows[{$index}][start_time]"
                                                value="{$win.start_time}" required>
                                            <input type="time" name="operational_windows[{$index}][end_time]"
                                                value="{$win.end_time}" required>
                                            <button type="button" class="remove-window-btn">❌</button>
                                        </div>
                                    {/foreach}
                                {else}
                                    <div class="operational-window">
                                        <select name="operational_windows[0][day_of_week]" required>
                                            {foreach from=$days item=day}<option value="{$day}">{$day}</option>{/foreach}
                                        </select>
                                        <input type="time" name="operational_windows[0][start_time]" required>
                                        <input type="time" name="operational_windows[0][end_time]" required>
                                        <button type="button" class="remove-window-btn">❌</button>
                                    </div>
                                {/if}
                            </div>

                            <div class="operational-windows-buttons" style="margin-top:10px;">
                                <button type="button" id="add-window-btn">➕ Agregar ventana</button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Descripción</label>
                            <textarea id="description" name="description"
                                placeholder="Agregue detalles o notas sobre la máquina">{$machineData.description|default:''}</textarea>
                        </div>

                        <div class="btn-container">
                            <a href="/views/machines/list/list-machines.view.php" class="btn-secondary">Cancelar</a>
                            <button type="submit" class="btn-primary">{if $machineData}Guardar Cambios
                            {else}Guardar
                                Máquina{/if}</button>
                        </div>
                    </form>
                </div>

            {elseif $formType == 'type'}
                <!-- ===================== FORMULARIO DE TIPO DE MÁQUINA ===================== -->
                <div class="form-container">
                    <form action="/controllers/machines/create-machine.controller.php" method="POST">
                        {if $typeData}
                            <input type="hidden" name="id_type" value="{$typeData.id}">
                            <input type="hidden" name="action" value="edit_type">
                        {else}
                            <input type="hidden" name="action" value="create_type">
                        {/if}

                        <div class="form-group">
                            <label for="type_name">Nombre del Tipo <span class="required">*</span></label>
                            <input type="text" id="type_name" name="name" required maxlength="50"
                                placeholder="Ej. Equipos de diagnóstico" value="{$typeData.name|default:''}">
                        </div>

                        <div class="form-group">
                            <label for="type_description">Descripción</label>
                            <textarea id="type_description" name="description"
                                placeholder="Descripción del tipo de máquina (opcional)">{$typeData.description|default:''}</textarea>
                        </div>

                        <div class="btn-container" style="justify-content: center;">
                            <button type="submit" class="btn-primary">
                                {if $typeData}Guardar Cambios{else}Guardar Tipo{/if}
                            </button>
                            <a href="/views/machines/list/list-machines.view.php" class="btn-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            {/if}


        </div>
    </main>
</body>

</html>