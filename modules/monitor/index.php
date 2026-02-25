<?php
require_once __DIR__ . '/../../config/config.php';
$pageTitle = 'Seguimiento de Actualización BI';
Auth::requireRole('admin');
require_once INCLUDES_PATH . '/header.php';
?>
<link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/highcharts.css">
 <!--main content start-->
          <div id="content">
              <div role="tabpanel">
                        <ul class="nav nav-tabs" role="tablist">
                          <li class="nav-item" role="presentation">
                            <button class="nav-link active" style="background-color: #F74339; color: #FFFFFF;" data-bs-toggle="tab" data-bs-target="#mmf" type="button" role="tab">Mensfashion</button>
                          </li>
                          <li class="nav-item" role="presentation">
                            <button class="nav-link"  style="background-color: #4E4B48; color: #FFFFFF;" data-bs-toggle="tab" data-bs-target="#mlb" type="button" role="tab">Boggi Milano</button>
                          </li>
                          <li class="nav-item" role="presentation">
                            <button class="nav-link" style="background-color: #0F3054; color: #FFFFFF;" data-bs-toggle="tab" data-bs-target="#mrb" type="button" role="tab">Roberts</button>
                          </li>
                          <li class="nav-item" role="presentation">
                            <button class="nav-link" style="background-color: #000; color: #FFFFFF;" data-bs-toggle="tab" data-bs-target="#mhl" type="button" role="tab">Highlife</button>
                          </li>
                          <li class="nav-item" role="presentation">
                            <button class="nav-link" style="background-color: #3AA1E0; color: #000;" data-bs-toggle="tab" data-bs-target="#mcrm" type="button" role="tab">CRM</button>
                          </li>
                        </ul>

                        <div class="tab-content" id="datatabs">
                          <div class="tab-pane fade show active" id="mmf" role="tabpanel">
                            <div id="monitor_mensfashion">Contenido Mensfashion</div>
                            <hr>
                          </div>
                          <div class="tab-pane fade" id="mlb" role="tabpanel">
                            <div id="monitor_lamberti">Contenido Lamberti</div>
                            <hr>
                          </div>
                          <div class="tab-pane fade" id="mrb" role="tabpanel">
                            <div id="monitor_roberts">Contenido Roberts</div>
                            <hr>
                          </div>
                          <div class="tab-pane fade" id="mhl" role="tabpanel">
                            <div id="monitor_highlife">Contenido Highlife</div>
                            <hr>
                          </div>
                          <div class="tab-pane fade" id="mcrm" role="tabpanel">
                            <div id="monitor_crm">Contenido CRM</div>
                            <hr>
                          </div>
                        </div>
                      </div>
          </div>
          <hr>
<!--main content end-->

<?php 
require_once INCLUDES_PATH . '/footer.php';
?>

<!--common script for all pages-->
  <script src="<?php echo APP_URL; ?>/assets/js/highcharts-gantt.js"></script>
  <script src="<?php echo APP_URL; ?>/assets/js/highcharts-accessibility.js"></script>
  <script src="<?php echo APP_URL; ?>/assets/js/Monitor/engine.js"></script>
<!--script for this page-->