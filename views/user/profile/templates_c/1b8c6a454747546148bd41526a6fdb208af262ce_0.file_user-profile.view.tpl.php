<?php
/* Smarty version 5.4.5, created on 2025-05-30 08:24:08
  from 'file:user-profile.view.tpl' */

/* @var \Smarty\Template $_smarty_tpl */
if ($_smarty_tpl->getCompiled()->isFresh($_smarty_tpl, array (
  'version' => '5.4.5',
  'unifunc' => 'content_68396b28beae85_97179926',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '1b8c6a454747546148bd41526a6fdb208af262ce' => 
    array (
      0 => 'user-profile.view.tpl',
      1 => 1748593440,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:../../components/sidebar.tpl' => 1,
  ),
))) {
function content_68396b28beae85_97179926 (\Smarty\Template $_smarty_tpl) {
$_smarty_current_dir = '/var/www/html/views/user/profile';
?><!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo '<script'; ?>
 src="https://cdn.jsdelivr.net/npm/sweetalert2@11"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 src="/views/components/sidebar.app.js" defer><?php echo '</script'; ?>
>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/views/user/profile/user-profile.styles.css">
    <link rel="stylesheet" href="/views/dashboard/dashboard.styles.css">
    <link rel="stylesheet" href="/views/components/sidebar.styles.css">
    <title>Perfil de usuario | Medic Life</title>
    <?php echo '<script'; ?>
>
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('doctor-photo-profile');
            const imagePreview = document.getElementById('image-preview');
            const previewPlaceholder = document.getElementById('preview-placeholder');

            if (fileInput) {
                fileInput.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        const reader = new FileReader();

                        reader.onload = function(e) {
                            imagePreview.src = e.target.result;
                            imagePreview.style.display = 'block';
                            previewPlaceholder.style.display = 'none';
                        }

                        reader.readAsDataURL(this.files[0]);
                    } else {
                        imagePreview.style.display = 'none';
                        previewPlaceholder.style.display = 'flex';
                    }
                });
            }
        });
    <?php echo '</script'; ?>
>
</head>

<body>
    <?php $_smarty_tpl->renderSubTemplate("file:../../components/sidebar.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), (int) 0, $_smarty_current_dir);
?>

    <main>
        <div class="main-content">
            <div class="profile-header">
                <h1>Perfil de usuario</h1>
            </div>

            <?php if ((true && ($_smarty_tpl->hasVariable('error') && null !== ($_smarty_tpl->getValue('error') ?? null)))) {?>
                <div class="error-message">
                    <?php echo htmlspecialchars((string)$_smarty_tpl->getValue('error'), ENT_QUOTES, 'UTF-8', true);?>

                </div>
            <?php }?>

            <?php if ((true && ($_smarty_tpl->hasVariable('success') && null !== ($_smarty_tpl->getValue('success') ?? null)))) {?>
                <div class="success-message">
                    <?php echo htmlspecialchars((string)$_smarty_tpl->getValue('success'), ENT_QUOTES, 'UTF-8', true);?>

                </div>
            <?php }?>

            <?php if ((true && ($_smarty_tpl->hasVariable('user') && null !== ($_smarty_tpl->getValue('user') ?? null)))) {?>
                <div class="profile-container">
                    <div class="profile-section">
                        <h2>Información del Usuario</h2>
                        <div class="profile-info">
                            <div class="info-item">
                                <span class="label">ID:</span>
                                <span class="value"><?php echo $_smarty_tpl->getValue('user')['id'];?>
</span>
                            </div>
                            <div class="info-item">
                                <span class="label">Nombre:</span>
                                <span class="value"><?php echo $_smarty_tpl->getValue('user')['name'];?>
</span>
                            </div>
                            <div class="info-item">
                                <span class="label">Correo Electrónico:</span>
                                <span class="value"><?php echo $_smarty_tpl->getValue('user')['email'];?>
</span>
                            </div>
                            <div class="info-item">
                                <span class="label">Rol:</span>
                                <span class="value">
                                    <?php if ($_smarty_tpl->getValue('user')['role'] == 'S') {?>Administración
                                    <?php } elseif ($_smarty_tpl->getValue('user')['role'] == 'A') {?>Administrador
                                    <?php } elseif ($_smarty_tpl->getValue('user')['role'] == 'D') {?>Doctor
                                    <?php } else {
echo $_smarty_tpl->getValue('user')['role'];?>

                                    <?php }?>
                                </span>
                            </div>
                            <div class="info-item">
                                <span class="label">Fecha de Creación:</span>
                                <span class="value"><?php echo $_smarty_tpl->getValue('user')['created_at'];?>
</span>
                            </div>
                            <div class="info-item">
                                <span class="label">Estado:</span>
                                <span class="value">
                                    <?php if ($_smarty_tpl->getValue('user')['status'] == 'AC') {?>Activo
                                    <?php } else {
echo $_smarty_tpl->getValue('user')['status'];?>

                                    <?php }?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <?php if ($_smarty_tpl->getValue('doctorData')) {?>
                        <div class="profile-section doctor-section">
                            <h2>Información del Doctor</h2>
                            <div class="profile-info">
                                <div class="doctor-photo-profile">
                                    <?php if ($_smarty_tpl->getValue('doctorData')['photo']) {?>
                                        <img src="/controllers/doctor/mostrar_foto.php?id=<?php echo $_smarty_tpl->getValue('doctorData')['id'];?>
" alt="Foto del doctor">
                                    <?php } else { ?>
                                        <div class="no-photo">Sin foto</div>
                                    <?php }?>
                                    <button type="button" class="update-photo-btn" onclick="document.getElementById('photo-upload-form').style.display='block'">
                                      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                                        <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325"/>
                                      </svg>
                                    </button>
                                </div>

                                <div id="photo-upload-form" class="photo-upload-form" style="display: none;">
                                    <form action="/controllers/doctor/update_photo.php" method="POST" enctype="multipart/form-data">
                                        <input type="hidden" name="doctor_id" value="<?php echo $_smarty_tpl->getValue('doctorData')['id'];?>
">
                                        <div class="form-group">
                                            <label for="doctor-photo-profile" class="custom-file-upload">
                                                <i class="bi bi-cloud-arrow-up"></i> Seleccionar nueva foto
                                            </label>
                                            <input type="file" id="doctor-photo-profile" name="doctor_photo" accept="image/*" required>
                                            <div id="image-preview-container" class="image-preview-container">
                                                <img id="image-preview" class="image-preview" src="" alt="Vista previa" style="display: none;">
                                                <div id="preview-placeholder" class="preview-placeholder">
                                                    <i class="bi bi-image"></i>
                                                    <span>Vista previa de la imagen</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-actions">
                                            <button type="submit" class="save-photo-btn">Guardar</button>
                                            <button type="button" class="cancel-btn" onclick="document.getElementById('photo-upload-form').style.display='none'; document.getElementById('image-preview').style.display='none'; document.getElementById('preview-placeholder').style.display='flex';">Cancelar</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="info-item">
                                    <span class="label">Nombre Completo:</span>
                                    <span class="value"><?php echo $_smarty_tpl->getValue('doctorData')['names'];?>
 <?php echo $_smarty_tpl->getValue('doctorData')['last_name'];?>
 <?php echo $_smarty_tpl->getValue('doctorData')['last_name2'];?>
</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">CURP:</span>
                                    <span class="value"><?php echo $_smarty_tpl->getValue('doctorData')['CURP'];?>
</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">RFC:</span>
                                    <span class="value"><?php echo $_smarty_tpl->getValue('doctorData')['RFC'];?>
</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Fecha de Nacimiento:</span>
                                    <span class="value"><?php echo $_smarty_tpl->getValue('doctorData')['birth_date'];?>
</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Género:</span>
                                    <span class="value"><?php echo $_smarty_tpl->getValue('doctorData')['gender'];?>
</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Teléfono:</span>
                                    <span class="value"><?php echo $_smarty_tpl->getValue('doctorData')['phone'];?>
</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Correo Electrónico:</span>
                                    <span class="value"><?php echo $_smarty_tpl->getValue('doctorData')['email'];?>
</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Número de Afiliación:</span>
                                    <span class="value"><?php echo $_smarty_tpl->getValue('doctorData')['insurance_number'];?>
</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Cédula Profesional:</span>
                                    <span class="value"><?php echo $_smarty_tpl->getValue('doctorData')['professional_id'];?>
</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Dirección:</span>
                                    <span class="value">
                                        <?php echo $_smarty_tpl->getValue('doctorData')['street'];?>
 <?php echo $_smarty_tpl->getValue('doctorData')['external_number'];?>

                                        <?php if ($_smarty_tpl->getValue('doctorData')['internal_number']) {?>, Int. <?php echo $_smarty_tpl->getValue('doctorData')['internal_number'];
}?>,
                                        Col. <?php echo $_smarty_tpl->getValue('doctorData')['neighborhood'];?>
, CP <?php echo $_smarty_tpl->getValue('doctorData')['CP'];?>

                                    </span>
                                </div>
                                <?php if ((true && (true && null !== ($_smarty_tpl->getValue('doctorData')['medical_areas'] ?? null))) && $_smarty_tpl->getSmarty()->getModifierCallback('count')($_smarty_tpl->getValue('doctorData')['medical_areas']) > 0) {?>
                                    <div class="info-item">
                                        <span class="label">Áreas Médicas:</span>
                                        <span class="value">
                                            <ul class="medical-areas-list">
                                                <?php
$_from = $_smarty_tpl->getSmarty()->getRuntime('Foreach')->init($_smarty_tpl, $_smarty_tpl->getValue('doctorData')['medical_areas'], 'area');
$foreach0DoElse = true;
foreach ($_from ?? [] as $_smarty_tpl->getVariable('area')->value) {
$foreach0DoElse = false;
?>
                                                    <li><?php echo $_smarty_tpl->getValue('area');?>
</li>
                                                <?php
}
$_smarty_tpl->getSmarty()->getRuntime('Foreach')->restore($_smarty_tpl, 1);?>
                                            </ul>
                                        </span>
                                    </div>
                                <?php }?>
                            </div>
                        </div>
                    <?php }?>
                </div>
            <?php }?>

            <!-- Nueva sección de Configuraciones -->
            <div class="profile-section settings-section">
                <h2>Configuraciones</h2>
                <div class="settings-container">
                    <div class="setting-item">
                        <span class="setting-label">Asistente de voz:</span>
                        <button id="voiceToggleBtn" aria-label="Asistente de voz" title="Asistente de voz" class="voice-toggle-btn">
                            🔈
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
<?php }
}
