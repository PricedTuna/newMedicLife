<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        {if $studyData}Editar Estudio{else}Agregar Estudio{/if} | Medic Life
    </title>

    <link rel="stylesheet" href="/assets/css/common.css">
    <link rel="stylesheet" href="/assets/css/forms.css">
    <link rel="stylesheet" href="/views/components/sidebar.styles.css">
    <link rel="stylesheet" href="/assets/css/usability-improvements.css">

    <link rel="stylesheet" href="/views/studies/create/create-studies.styles.css">
    <link rel="icon" href="/views/dashboard/icons/Untitled-design-_1_.ico" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="/views/components/sidebar.app.js" defer></script>
    <script src="/views/studies/create/create-studies.app.js" defer></script>
</head>

<body>
    {include file=$sidebarPath}

    <main>
        <div class="main-content">
            <div class="page-header">
                <h1>{if $studyData}Editar Estudio{else}Agregar Estudio{/if}</h1>
            </div>

            {if $success}
                <script>
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: '{$success|escape}',
                        confirmButtonText: 'Aceptar'
                    });
                </script>
            {/if}

            {if $error}
                <script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        html: '{$error|escape}'
                    });
                </script>
            {/if}

            <div class="form-container">
                <form action="/controllers/studies/studies.controller.php" method="POST">
                    {if $studyData}
                        <input type="hidden" name="id" value="{$studyData.id}">
                        <input type="hidden" name="action" value="edit">
                    {else}
                        <input type="hidden" name="action" value="create">
                    {/if}

                    <div class="form-group">
                        <label for="code">Código <span class="required">*</span></label>
                        <input type="text" id="code" name="code" required maxlength="20" placeholder="Ej. EST001"
                            value="{$studyData.code|default:''}">
                    </div>

                    <div class="form-group">
                        <label for="name">Nombre <span class="required">*</span></label>
                        <input type="text" id="name" name="name" required maxlength="100"
                            placeholder="Nombre del estudio" value="{$studyData.name|default:''}">
                    </div>

                    <div class="form-group">
                        <label for="description">Descripción</label>
                        <textarea id="description" name="description"
                            placeholder="Detalles adicionales">{$studyData.description|default:''}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="specialty">Especialidad <span class="required">*</span></label>
                        <input type="text" id="specialty" name="specialty" required maxlength="50"
                            placeholder="Ej. Cardiología" value="{$studyData.specialty|default:''}">
                    </div>

                    <div class="form-group">
                        <label for="default_duration">Duración por defecto (min) <span class="required">*</span></label>
                        <input type="number" id="default_duration" name="default_duration" required min="1"
                            value="{$studyData.default_duration|default:30}">
                    </div>

                    <div class="form-group">
                        <label for="preparation">Preparación</label>
                        <textarea id="preparation" name="preparation"
                            placeholder="Indicaciones previas">{$studyData.preparation|default:''}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="sla_days">Días SLA</label>
                        <input type="number" id="sla_days" name="sla_days" min="0"
                            value="{$studyData.sla_days|default:0}">
                    </div>

                    <div class="form-group">
                        <label for="price">Costo del estudio ($) <span class="required">*</span></label>
                        <input type="number" id="price" name="price" required min="0" step="0.01"
                            placeholder="Ej. 500.00" value="{$studyData.price|default:0.00}">
                    </div>


                    <div class="form-group">


                        <div class="form-group">
                            <label for="result_type">Tipo de resultado</label>
                            <select id="result_type" name="result_type">
                                <option value="texto"
                                    {if $studyData.result_type|default:'texto' == 'texto'}selected{/if}>
                                    Texto</option>
                                <option value="archivo"
                                    {if $studyData.result_type|default:'texto' == 'archivo'}selected{/if}>Archivo
                                </option>
                                <option value="imagen"
                                    {if $studyData.result_type|default:'texto' == 'imagen'}selected{/if}>
                                    Imagen</option>
                            </select>
                        </div>


                        <div class="form-group">
                            <div class="checkbox-group">
                                <span>Requiere técnico</span>
                                <input type="checkbox" id="requires_technician" name="requires_technician"
                                    {if $studyData.requires_technician|default:0}checked{/if}>
                                <label for="requires_technician"></label>
                            </div>

                            <div class="checkbox-group">
                                <span>Requiere máquina</span>
                                <input type="checkbox" id="requires_machine" name="requires_machine"
                                    {if $studyData.requires_machine|default:0}checked{/if}>
                                <label for="requires_machine"></label>
                            </div>

                            <div class="form-group" id="machine_select_group" style="display: none;">
                                <label for="machine_id">Seleccionar máquina</label>
                                <select id="machine_id" name="machine_id">
                                    <option value="">-- Seleccione una máquina --</option>
                                    {foreach from=$machines item=machine}
                                        <option value="{$machine.id}"
                                            {if $studyData.machine_id|default:0 == $machine.id}selected{/if}>
                                            {$machine.name} ({$machine.machine_type} - {$machine.medical_area})
                                        </option>
                                    {/foreach}
                                </select>
                            </div>



                            <div class="checkbox-group">
                                <span>Activo</span>
                                <input type="checkbox" id="active" name="active"
                                    {if $studyData.active|default:1}checked{/if}>
                                <label for="active"></label>
                            </div>
                        </div>


                        <div class="btn-container">
                            <a href="/views/studies/list/list-studies.view.php" class="btn-secondary">Cancelar</a>
                            <button type="submit" class="btn-primary">
                                {if $studyData}Guardar Cambios{else}Guardar Estudio{/if}
                            </button>
                        </div>

                </form>
            </div>
        </div>
    </main>
</body>

</html>