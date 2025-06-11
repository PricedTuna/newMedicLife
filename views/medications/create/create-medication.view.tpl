<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Medicamento | Medic Life</title>

    <link rel="stylesheet" href="/assets/css/common.css">
    <link rel="stylesheet" href="/assets/css/forms.css">
    <link rel="stylesheet" href="/views/components/sidebar.styles.css">
    <link rel="stylesheet" href="/assets/css/usability-improvements.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="/views/components/sidebar.app.js" defer></script>

    <link rel="icon" href="/views/dashboard/icons/Untitled-design-_1_.ico" type="image/x-icon">

    <style>
        .form-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .required {
            color: red;
            margin-left: 2px;
        }

        .tooltip-icon {
            margin-left: 5px;
            color: #007bff;
            cursor: help;
            font-size: 14px;
        }

        .tooltip-text {
            visibility: hidden;
            width: 200px;
            background-color: #555;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            padding: 5px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            margin-left: -100px;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .tooltip-container {
            position: relative;
            display: inline-block;
        }

        .tooltip-container:hover .tooltip-text {
            visibility: visible;
            opacity: 1;
        }

        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }

        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }

        .btn-container {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        .btn-primary {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .btn-secondary {
            background-color: #f0f0f0;
            color: #333;
            border: 1px solid #ddd;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
        }

        .btn-primary:hover {
            background-color: #45a049;
        }

        .btn-secondary:hover {
            background-color: #e0e0e0;
        }

        .btn-cancel {
            background-color: #e74c3c;
            color: white;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-cancel:hover {
            background-color: #c0392b;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .error-message {
            color: #ff0000;
            margin-top: 5px;
            font-size: 14px;
        }

        .success-message {
            color: #4CAF50;
            margin-top: 5px;
            font-size: 14px;
        }

        .tabs {
            display: flex;
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
        }

        .tab {
            padding: 10px 20px;
            cursor: pointer;
            border: 1px solid transparent;
            border-bottom: none;
            margin-right: 5px;
            border-radius: 5px 5px 0 0;
        }

        .tab.active {
            background-color: #f0f0f0;
            border-color: #ddd;
            border-bottom-color: white;
            margin-bottom: -1px;
            font-weight: bold;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }
    </style>
</head>
<body>
    {include file=$sidebarPath}

    <main>
        <div class="main-content">
            <div class="page-header">
                <h1>Agregar Medicamento</h1>
            </div>

            {if isset($form_result)}
                {if $form_result.success}
                    <div class="alert alert-success">
                        {$form_result.message|escape}
                    </div>
                    <script>
                        setTimeout(function() {
                            window.location.href = '/views/medications/list/list-medications.view.php';
                        }, 2000);
                    </script>
                {else}
                    <div class="alert alert-danger">
                        {$form_result.message|escape}
                        {if !empty($form_result.errors)}
                            <ul>
                                {foreach from=$form_result.errors item=error}
                                    <li>{$error|escape}</li>
                                {/foreach}
                            </ul>
                        {/if}
                    </div>
                {/if}
            {/if}

            <div class="tabs">
                <div class="tab active" data-tab="medication">Medicamento</div>
                <div class="tab" data-tab="type">Tipo de Medicamento</div>
            </div>

            <div id="medication-tab" class="tab-content active">
                <div class="form-container">
                    <form action="/controllers/medications/create-medication.controller.php" method="POST">
                        <div class="form-group">
                            <label for="name">Nombre del Medicamento <span class="required">*</span>
                                <span class="tooltip-container">
                                    <i class="bi bi-question-circle tooltip-icon"></i>
                                    <span class="tooltip-text">Ingrese el nombre del medicamento. Máximo 50 caracteres.</span>
                                </span>
                            </label>
                            <input type="text" id="name" name="name" required maxlength="50" placeholder="Nombre del medicamento">
                            <div id="name-error" class="error-message" style="color: red; display: none;"></div>
                        </div>

                        <div class="form-group">
                            <label for="id_medicine_type">Tipo de Medicamento <span class="required">*</span>
                                <span class="tooltip-container">
                                    <i class="bi bi-question-circle tooltip-icon"></i>
                                    <span class="tooltip-text">Seleccione el tipo de medicamento.</span>
                                </span>
                            </label>
                            <select id="id_medicine_type" name="id_medicine_type" required>
                                <option value="">Seleccione un tipo</option>
                                {foreach from=$medication_types item=type}
                                    <option value="{$type.id}">{$type.name|escape}</option>
                                {/foreach}
                            </select>
                            <div id="type-error" class="error-message" style="color: red; display: none;"></div>
                        </div>

                        <div class="form-group">
                            <label for="stock">Stock <span class="required">*</span>
                                <span class="tooltip-container">
                                    <i class="bi bi-question-circle tooltip-icon"></i>
                                    <span class="tooltip-text">Ingrese la cantidad disponible del medicamento.</span>
                                </span>
                            </label>
                            <input type="number" id="stock" name="stock" required min="0" placeholder="Cantidad disponible">
                            <div id="stock-error" class="error-message" style="color: red; display: none;"></div>
                        </div>

                        <div class="form-group">
                            <label for="price_purchase">Precio de Compra <span class="required">*</span>
                                <span class="tooltip-container">
                                    <i class="bi bi-question-circle tooltip-icon"></i>
                                    <span class="tooltip-text">Ingrese el precio de compra del medicamento.</span>
                                </span>
                            </label>
                            <input type="number" id="price_purchase" name="price_purchase" required min="0" placeholder="Precio de compra del medicamento">
                            <div id="price-purchase-error" class="error-message" style="color: red; display: none;"></div>
                        </div>

                        <div class="form-group">
                            <label for="price_sale">Precio de Venta <span class="required">*</span>
                                <span class="tooltip-container">
                                    <i class="bi bi-question-circle tooltip-icon"></i>
                                    <span class="tooltip-text">Ingrese el precio de venta del medicamento.</span>
                                </span>
                            </label>
                            <input type="number" id="price_sale" name="price_sale" required min="0" step="0.01" placeholder="Precio de venta del medicamento">
                            <div id="price-sale-error" class="error-message" style="color: red; display: none;"></div>
                        </div>

                        <div class="form-group">
                            <label for="status">Estado <span class="required">*</span>
                                <span class="tooltip-container">
                                    <i class="bi bi-question-circle tooltip-icon"></i>
                                    <span class="tooltip-text">Seleccione el estado del medicamento.</span>
                                </span>
                            </label>
                            <select id="status" name="status" required>
                                <option value="A">Activo</option>
                                <option value="I">Inactivo</option>
                            </select>
                            <div id="status-error" class="error-message" style="color: red; display: none;"></div>
                        </div>

                        <div class="btn-container">
                            <a href="/views/medications/list/list-medications.view.php" class="btn-secondary btn-cancel">Cancelar</a>
                            <button type="submit" class="btn-primary small-purple-btn">Guardar Medicamento</button>
                        </div>
                    </form>
                </div>
            </div>

            <div id="type-tab" class="tab-content">
                <div class="form-container">
                    <form action="/controllers/medications/create-medication.controller.php" method="POST">
                        <input type="hidden" name="action" value="create_type">

                        <div class="form-group">
                            <label for="type_name">Nombre del Tipo <span class="required">*</span>
                                <span class="tooltip-container">
                                    <i class="bi bi-question-circle tooltip-icon"></i>
                                    <span class="tooltip-text">Ingrese el nombre del tipo de medicamento. Máximo 50 caracteres.</span>
                                </span>
                            </label>
                            <input type="text" id="type_name" name="name" required maxlength="50" placeholder="Nombre del tipo">
                            <div id="type-name-error" class="error-message" style="color: red; display: none;"></div>
                        </div>

                        <div class="form-group">
                            <label for="type_description">Descripción
                                <span class="tooltip-container">
                                    <i class="bi bi-question-circle tooltip-icon"></i>
                                    <span class="tooltip-text">Ingrese una descripción del tipo de medicamento (opcional).</span>
                                </span>
                            </label>
                            <textarea id="type_description" name="description" placeholder="Descripción del tipo"></textarea>
                        </div>

                        <div class="btn-container" style="justify-content: center;">
                            <button type="submit" class="btn-primary small-purple-btn">Guardar Tipo</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab functionality
            const tabs = document.querySelectorAll('.tab');
            const tabContents = document.querySelectorAll('.tab-content');

            // Function to set active tab
            const setActiveTab = (tabId) => {
                // Remove active class from all tabs and contents
                tabs.forEach(t => t.classList.remove('active'));
                tabContents.forEach(c => c.classList.remove('active'));

                // Add active class to clicked tab and corresponding content
                const tab = document.querySelector('.tab[data-tab="' + tabId + '"]');
                if (tab) {
                    tab.classList.add('active');
                    document.getElementById(tabId + '-tab').classList.add('active');
                }
            };

            // Check if there's a hash in the URL
            if (window.location.hash) {
                const tabId = window.location.hash.substring(1);
                setActiveTab(tabId);
            }

            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    const tabId = tab.getAttribute('data-tab');
                    setActiveTab(tabId);
                    // Update URL hash
                    window.location.hash = tabId;
                });
            });

            // Form validation for medication
            const medicationForm = document.querySelector('#medication-tab form');
            if (medicationForm) {
                const nameInput = medicationForm.querySelector('#name');
                const typeSelect = medicationForm.querySelector('#id_medicine_type');
                const stockInput = medicationForm.querySelector('#stock');
                const pricePurchaseInput = medicationForm.querySelector('#price_purchase');
                const priceSaleInput = medicationForm.querySelector('#price_sale');
                const statusSelect = medicationForm.querySelector('#status');
                const nameError = medicationForm.querySelector('#name-error');
                const typeError = medicationForm.querySelector('#type-error');
                const stockError = medicationForm.querySelector('#stock-error');
                const pricePurchaseError = medicationForm.querySelector('#price-purchase-error');
                const priceSaleError = medicationForm.querySelector('#price-sale-error');
                const statusError = medicationForm.querySelector('#status-error');

                // Validate name field
                nameInput.addEventListener('blur', function() {
                    if (this.value.trim() === '') {
                        nameError.textContent = 'El nombre del medicamento es obligatorio';
                        nameError.style.display = 'block';
                    } else if (this.value.length > 50) {
                        nameError.textContent = 'El nombre del medicamento no puede exceder los 50 caracteres';
                        nameError.style.display = 'block';
                    } else {
                        nameError.style.display = 'none';
                    }
                });

                // Validate type field
                typeSelect.addEventListener('change', function() {
                    if (this.value === '') {
                        typeError.textContent = 'Debe seleccionar un tipo de medicamento';
                        typeError.style.display = 'block';
                    } else {
                        typeError.style.display = 'none';
                    }
                });

                // Validate stock field
                stockInput.addEventListener('blur', function() {
                    if (this.value.trim() === '') {
                        stockError.textContent = 'El stock es obligatorio';
                        stockError.style.display = 'block';
                    } else if (isNaN(this.value) || parseInt(this.value) < 0) {
                        stockError.textContent = 'El stock debe ser un número positivo';
                        stockError.style.display = 'block';
                    } else {
                        stockError.style.display = 'none';
                    }
                });

                // Validate price_purchase field
                pricePurchaseInput.addEventListener('blur', function() {
                    if (this.value.trim() === '') {
                        pricePurchaseError.textContent = 'El precio de compra es obligatorio';
                        pricePurchaseError.style.display = 'block';
                    } else if (isNaN(this.value) || parseInt(this.value) < 0) {
                        pricePurchaseError.textContent = 'El precio de compra debe ser un número positivo';
                        pricePurchaseError.style.display = 'block';
                    } else {
                        pricePurchaseError.style.display = 'none';
                    }
                });

                // Validate price_sale field
                priceSaleInput.addEventListener('blur', function() {
                    if (this.value.trim() === '') {
                        priceSaleError.textContent = 'El precio de venta es obligatorio';
                        priceSaleError.style.display = 'block';
                    } else if (isNaN(this.value) || parseFloat(this.value) < 0) {
                        priceSaleError.textContent = 'El precio de venta debe ser un número positivo';
                        priceSaleError.style.display = 'block';
                    } else {
                        priceSaleError.style.display = 'none';
                    }
                });

                // Validate status field
                statusSelect.addEventListener('change', function() {
                    if (this.value === '') {
                        statusError.textContent = 'Debe seleccionar un estado';
                        statusError.style.display = 'block';
                    } else {
                        statusError.style.display = 'none';
                    }
                });

                // Form submission
                medicationForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    // Validate fields
                    let isValid = true;

                    if (nameInput.value.trim() === '') {
                        nameError.textContent = 'El nombre del medicamento es obligatorio';
                        nameError.style.display = 'block';
                        isValid = false;
                    }

                    if (typeSelect.value === '') {
                        typeError.textContent = 'Debe seleccionar un tipo de medicamento';
                        typeError.style.display = 'block';
                        isValid = false;
                    }

                    if (stockInput.value.trim() === '') {
                        stockError.textContent = 'El stock es obligatorio';
                        stockError.style.display = 'block';
                        isValid = false;
                    } else if (isNaN(stockInput.value) || parseInt(stockInput.value) < 0) {
                        stockError.textContent = 'El stock debe ser un número positivo';
                        stockError.style.display = 'block';
                        isValid = false;
                    }

                    if (pricePurchaseInput.value.trim() === '') {
                        pricePurchaseError.textContent = 'El precio de compra es obligatorio';
                        pricePurchaseError.style.display = 'block';
                        isValid = false;
                    } else if (isNaN(pricePurchaseInput.value) || parseInt(pricePurchaseInput.value) < 0) {
                        pricePurchaseError.textContent = 'El precio de compra debe ser un número positivo';
                        pricePurchaseError.style.display = 'block';
                        isValid = false;
                    }

                    if (priceSaleInput.value.trim() === '') {
                        priceSaleError.textContent = 'El precio de venta es obligatorio';
                        priceSaleError.style.display = 'block';
                        isValid = false;
                    } else if (isNaN(priceSaleInput.value) || parseFloat(priceSaleInput.value) < 0) {
                        priceSaleError.textContent = 'El precio de venta debe ser un número positivo';
                        priceSaleError.style.display = 'block';
                        isValid = false;
                    }

                    if (statusSelect.value === '') {
                        statusError.textContent = 'Debe seleccionar un estado';
                        statusError.style.display = 'block';
                        isValid = false;
                    }

                    if (isValid) {
                        // Show confirmation dialog
                        Swal.fire({
                            title: '¿Está seguro?',
                            text: '¿Desea guardar este medicamento?',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonText: 'Sí, guardar',
                            cancelButtonText: 'Cancelar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                this.submit();
                            }
                        });
                    }
                });
            }

            // Form validation for medication type
            const typeForm = document.querySelector('#type-tab form');
            if (typeForm) {
                const typeNameInput = typeForm.querySelector('#type_name');
                const typeNameError = typeForm.querySelector('#type-name-error');

                // Validate name field
                typeNameInput.addEventListener('blur', function() {
                    if (this.value.trim() === '') {
                        typeNameError.textContent = 'El nombre del tipo es obligatorio';
                        typeNameError.style.display = 'block';
                    } else if (this.value.length > 50) {
                        typeNameError.textContent = 'El nombre del tipo no puede exceder los 50 caracteres';
                        typeNameError.style.display = 'block';
                    } else {
                        typeNameError.style.display = 'none';
                    }
                });

                // Form submission
                typeForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    // Validate fields
                    let isValid = true;

                    if (typeNameInput.value.trim() === '') {
                        typeNameError.textContent = 'El nombre del tipo es obligatorio';
                        typeNameError.style.display = 'block';
                        isValid = false;
                    }

                    if (isValid) {
                        // Show confirmation dialog
                        Swal.fire({
                            title: '¿Está seguro?',
                            text: '¿Desea guardar este tipo de medicamento?',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonText: 'Sí, guardar',
                            cancelButtonText: 'Cancelar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                this.submit();
                            }
                        });
                    }
                });
            }



            // Add cancel button functionality to all cancel buttons
            document.querySelectorAll('.btn-cancel').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: '¿Está seguro?',
                        text: 'Se perderán los cambios no guardados',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#e74c3c',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Sí, cancelar',
                        cancelButtonText: 'No, continuar editando'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '/views/medications/list/list-medications.view.php';
                        }
                    });
                });
            });

            // Show success message if form was submitted successfully
            {if isset($form_result) && $form_result.success}
                Swal.fire({
                    title: '¡Éxito!',
                    text: '{$form_result.message|escape}',
                    icon: 'success',
                    confirmButtonText: 'Aceptar'
                }).then(() => {
                    window.location.href = '/views/medications/list/list-medications.view.php';
                });
            {/if}
        });
    </script>
</body>
</html>
