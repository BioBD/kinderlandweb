<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title>Colônia Kinderland</title>

        <link href="<?= $this->config->item('assets'); ?>css/basic.css" rel="stylesheet" />
        <link href="<?= $this->config->item('assets'); ?>css/bootstrap.min.css" rel="stylesheet" />
        <link rel="stylesheet" href="<?= $this->config->item('assets'); ?>css/themes/base/jquery-ui.css" />
        <link rel="stylesheet" href="<?= $this->config->item('assets'); ?>css/bootstrap-switch.min.css">
        <link rel="stylesheet" href="<?= $this->config->item('assets'); ?>css/theme.default.css" />
        <script type="text/javascript" src="<?= $this->config->item('assets'); ?>js/jquery-2.0.3.min.js"></script>
        <script type="text/javascript" src="<?= $this->config->item('assets'); ?>js/ui/jquery-ui.js"></script>
        <script type="text/javascript" src="<?= $this->config->item('assets'); ?>js/bootstrap.min.js"></script>
        <script type="text/javascript" src="<?= $this->config->item('assets'); ?>js/jquerysettings.js"></script>
        <script type="text/javascript" src="<?= $this->config->item('assets'); ?>js/jquery/jquery.redirect.js"></script>
        <script type="text/javascript" src="<?= $this->config->item('assets'); ?>js/formValidationFunctions.js"></script>
        <script type="text/javascript" src="<?= $this->config->item('assets'); ?>js/bootstrap-switch.min.js"></script>
        <script type="text/javascript" src="<?= $this->config->item('assets'); ?>js/jquery/jquery.mask.js"></script>
        <script type="text/javascript" src="<?= $this->config->item('assets'); ?>js/jquery.tablesorter.js"></script>
        <script type="text/javascript" src="<?= $this->config->item('assets'); ?>datatable/js/datatable.min.js"></script>
        <link rel="stylesheet" href="<?= $this->config->item('assets'); ?>datatable/css/datatable-bootstrap.min.css" />
		
		<script>

		function sortLowerCase(l, r) {
			return l.toLowerCase().localeCompare(r.toLowerCase());
		}

		function showCounter(currentPage, totalPage, firstRow, lastRow, totalRow, totalRowUnfiltered) {
			return '';
		}

		function myFunction(a, b) {
		    return points.sort(function(a, b){return a-b});
		}

		</script>

    </head>
    <body>
        <script>
        $(document).ready(function() {
			$('#sortable-table').datatable({
				pageSize : Number.MAX_VALUE,
				sort : [sortLowerCase, true, true],
				counterText : showCounter
			});
		});
        </script>
        <div class="main-container-report">
            <div class = "row">
                <div class="col-lg-12">
                	<form method="GET">
						<select name="ano_f" onchange="this.form.submit()" id="anos">
					
							<?php
							foreach ( $years as $year ) {
								$selected = "";
								if ($ano_escolhido == $year)
									$selected = "selected";
								echo "<option $selected value='$year'>$year</option>";
							}
							?>
						</select>
						<select name="colonia_f" onchange="this.form.submit()" id="colonia">
							<?php if (!(isset($discountsT) && isset($discountsI) && !is_null($discountsT) && !is_null($discountsI))){ ?>
							<option value="0" <?php if(!isset($colonia_escolhida)) echo "selected"; ?>>-- Selecionar --</option>
							<?php }
							foreach ( $camps as $camp ) {
								$selected = "";
								if ($colonia_escolhida == $camp)
									$selected = "selected";
								echo "<option $selected value='$camp'>$camp</option>";
							}
							?>
						</select>
						<select name="genero_f" onchange="this.form.submit()" id="genero">
					
							<?php
							foreach ( $genders as $gender ) {
								$selected = "";
								if ($genero_escolhido == $gender)
									$selected = "selected";
								echo "<option $selected value='$gender'>$gender</option>";
							}
							?>
						</select>
					</form>
					<div class="counter"></div> <br>
					<?php if (isset($colonia_escolhida) && isset($colonists)){ ?>
                    <table class="table table-bordered table-striped table-min-td-size" style="max-width: 800px; font-size:15px" id="sortable-table">
                        <thead>
                            <tr>
                                <th>Colonista</th>
                                <th>Idade (Anos)</th>
                                <th>Ano Escolar</th>
                                
                            </tr>
                        </thead>
                        <tbody id="tablebody">
                            <?php
                            foreach ($colonists as $colonist) {
                                ?>
                                <tr>
                                    <td><a id="<?= $colonist->colonist_name ?>" target="_blank" href="<?= $this -> config -> item('url_link') ?>admin/viewColonistInfo?colonistId=<?= $colonist -> colonist_id ?>&summerCampId=<?= $colonist -> camp_id ?>"><?= $colonist -> colonist_name ?></a></td>
                                    <td><?php $age = preg_split('/y/', $colonist -> age); echo $age[0]; ?></td>
                                    <td><?= $colonist -> school_year; ?></td>
                                 </tr>
	                                <?php
	                            }
	                            ?>    
                        </tbody>
                    </table>
                    <?php } ?>
                </div>
            </div>
        </div>
    </body>
</html>