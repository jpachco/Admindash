<?php
require_once __DIR__ . '/../../config/config.php';

$pageTitle = 'Envío de PDFs - Gastos Operativos';

require_once INCLUDES_PATH . '/header.php';
?>

<div class="container mt-5">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="fas fa-file-pdf"></i> Generador de Reportes de Gastos</h4>
        </div>
        <div class="card-body">
            <form id="gastosForm" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="month"><b>Seleccione el Mes:</b></label>
                            <select id="month" name="month" class="form-control" required>
                                <option value="">-- Seleccione un mes --</option>
                                <?php
                                $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 
                                         'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                                $mesActual = (int)date('m');
                                
                                foreach ($meses as $num => $nombre) {
                                    $valor = $num + 1;
                                    $selected = $valor === $mesActual ? 'selected' : '';
                                    echo "<option value=\"$valor\" $selected>$nombre</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="year"><b>Seleccione el Año:</b></label>
                            <select id="year" name="year" class="form-control" required>
                                <option value="">-- Seleccione un año --</option>
                                <?php
                                $yearActual = (int)date('Y');
                                for ($y = $yearActual; $y >= $yearActual-1; $y--) {
                                    $selected = $y === $yearActual ? 'selected' : '';
                                    echo "<option value=\"$y\" $selected>$y</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="pdfFile"><b>Seleccione archivo de Gastos:</b></label>
                    <input type="file" class="form-control-file" id="pdfFile" accept=".csv,.xls,.xlsx" name="pdfFile" required>
                    <small class="form-text text-muted">
                        <strong>Formatos aceptados:</strong> CSV, XLS, XLSX (máximo 5MB)
                    </small>
                </div>

                <div class="form-group">
                    <button type="button" class="btn btn-secondary" id="btnPreview">
                        <i class="fas fa-eye"></i> Vista Previa
                    </button>
                    <button type="button" class="btn btn-success" id="btnSend" disabled>
                        <i class="fas fa-envelope"></i> Enviar PDFs
                    </button>
                    <div class="spinner-border text-primary d-none ms-2" id="spinner" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                </div>
            </form>

            <!-- Vista Previa -->
            <div id="previewContainer" class="mt-4 d-none">
                <h5 class="mb-3">Vista Previa - <span id="previewMonth"></span> <span id="previewYear"></span></h5>
                <div id="previewData" class="row"></div>
            </div>

            <!-- Mensajes -->
            <div id="alertContainer"></div>
        </div>
    </div>
</div>

<style>
    .store-card {
        background-color: #f8f9fa;
        border-left: 4px solid #18a04c;
        padding: 15px;
        margin-bottom: 15px;
        border-radius: 4px;
    }
    
    .store-card h6 {
        color: #18a04c;
        margin-bottom: 10px;
        font-weight: bold;
    }
    
    .store-card table {
        width: 100%;
        font-size: 0.9rem;
    }
    
    .store-card table th {
        background-color: #e9ecef;
        padding: 8px;
        border: 1px solid #dee2e6;
    }
    
    .store-card table td {
        padding: 8px;
        border: 1px solid #dee2e6;
    }
    
    .store-card table tr:hover {
        background-color: #fff3cd;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('gastosForm');
    const btnPreview = document.getElementById('btnPreview');
    const btnSend = document.getElementById('btnSend');
    const spinner = document.getElementById('spinner');
    const alertContainer = document.getElementById('alertContainer');
    const previewContainer = document.getElementById('previewContainer');
    const previewData = document.getElementById('previewData');
    let previewProcessed = false;

    btnPreview.addEventListener('click', function() {
        handleFormSubmit('preview');
    });

    btnSend.addEventListener('click', function() {
        if (!previewProcessed) {
            showAlert('Primero debes ver la vista previa', 'warning');
            return;
        }
        handleFormSubmit('send');
    });

    function handleFormSubmit(action) {
        if (!validateForm()) {
            return;
        }

        const formData = new FormData(form);
        formData.append('action', action);

        spinner.classList.remove('d-none');
        alertContainer.innerHTML = '';

        fetch('process_pdf.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            spinner.classList.add('d-none');
            
            if (data.success) {
                if (action === 'preview') {
                    displayPreview(data);
                    previewProcessed = true;
                    btnSend.disabled = false;
                    showAlert('Vista previa cargada correctamente', 'success');
                } else if (action === 'send') {
                    showAlert('PDFs enviados correctamente a ' + data.stores + ' tienda(s)', 'success');
                    form.reset();
                    previewContainer.classList.add('d-none');
                    previewProcessed = false;
                    btnSend.disabled = true;
                }
            } else {
                showAlert(data.error || 'Error desconocido', 'danger');
            }
        })
        .catch(error => {
            spinner.classList.add('d-none');
            showAlert('Error: ' + error.message, 'danger');
        });
    }

    function validateForm() {
        if (!document.getElementById('month').value) {
            showAlert('Por favor seleccione un mes', 'warning');
            return false;
        }
        if (!document.getElementById('year').value) {
            showAlert('Por favor seleccione un año', 'warning');
            return false;
        }
        if (!document.getElementById('pdfFile').value) {
            showAlert('Por favor cargue un archivo CSV', 'warning');
            return false;
        }
        return true;
    }

    function displayPreview(data) {
        document.getElementById('previewMonth').textContent = data.month;
        document.getElementById('previewYear').textContent = data.year;
        
        previewData.innerHTML = '';
        
        if (data.stores_preview) {
            Object.entries(data.stores_preview).forEach(([storeName, values]) => {
                const storeCard = document.createElement('div');
                storeCard.className = 'col-md-12 store-card';
                
                let tableHtml = '<table><thead><tr><th>Concepto</th><th>Monto ($)</th></tr></thead><tbody>';
                
                Object.entries(values).forEach(([concept, amount]) => {
                    const formattedAmount = new Intl.NumberFormat('es-MX', {
                        style: 'currency',
                        currency: 'MXN'
                    }).format(amount);
                    
                    tableHtml += `<tr><td>${concept}</td><td>${formattedAmount}</td></tr>`;
                });
                
                tableHtml += '</tbody></table>';
                
                storeCard.innerHTML = `
                    <h6><i class="fas fa-store"></i> ${storeName}</h6>
                    ${tableHtml}
                `;
                
                previewData.appendChild(storeCard);
            });
        }
        
        previewContainer.classList.remove('d-none');
    }

    function showAlert(message, type) {
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show`;
        alert.role = 'alert';
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        alertContainer.innerHTML = '';
        alertContainer.appendChild(alert);
    }
});
</script>

<?php
require_once INCLUDES_PATH . '/footer.php';
?>
