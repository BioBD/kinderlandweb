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

		</script>
	</head>
	<style>
	
	div.scroll{
    	
    	width:100%;
    	height:100%;
    	overflow-x:hidden;
    
    }
    div.pad{
    	padding-left: 9%;
    	}
	
	</style>
	<body>
	<div class="scroll">
		<div class="main-container-report">
		            <div class = "row">
		                <div class="col-lg-12">
		                     <form id="form_selection" method="GET">
		                   Ano: <select name="ano_f" onchange="this.form.submit()" id="anos">
		                
		                        <?php
		                        foreach ( $years as $year ) {
		                            $selected = "";
		                            if ($ano_escolhido == $year)
		                                $selected = "selected";
		                            echo "<option $selected value='$year'>$year</option>";
		                        }
		                        ?>
		                    </select>
		                    Evento: <select name="evento_f" onchange="this.form.submit()" id="events">
		                
		                        <?php
		                        foreach ( $events as $event ) {
		                            $selected = "";
		                            if ($evento_escolhido == $event)
		                                $selected = "selected";
		                            echo "<option $selected value='$event'>$event</option>";
		                        }
		                        ?>
		                    </select>
		                    </form>
		                  </div>
		                </div>
		                <?php if($event !== null){?>
		                <div class="pad">
		                <table class="table table-bordered table-striped table-min-td-size"
					style="max-width: 800px;">
							<tr>
								<th style="width: 150px;"></th>
								<th style="width: 250px;">Quantidade de Convites</th>
								<th>Pagos</th>
								<th style="width: 250px;">Aguardando Pagamento</th>
								<th>Disponíveis</th>
							</tr>
							<tr>
								<th>Masculino </th>
								<td><?php echo $male_eventSubscribed; ?></td>
								<td><?php echo $male_paid;?></td>
								<td><?php echo $male_eventSubscribed - $male_paid;?></td>
								<td><?php echo $capacity_male;?></td>
							</tr>
							<tr>
								<th>Feminino</th>
								<td><?php echo $female_eventSubscribed; ?></td>
								<td><?php echo $female_paid;?></td>
								<td><?php echo $female_eventSubscribed - $female_paid;?></td>
								<td><?php echo $capacity_female;?></td>
							</tr>
							<tr>
								<th>Sem pernoite</th>
								<td><?php echo $nonsleeper_eventSubscribed; ?></td>
								<td><?php echo $nonsleeper_paid;?></td>
								<td><?php echo $nonsleeper_eventSubscribed - $nonsleeper_paid;?></td>
								<td><?php echo $capacity_nonsleeper;?></td>
							</tr>
							<tr>
								<th>Total</th>
								<td><?php echo $male_eventSubscribed+$female_eventSubscribed+$nonsleeper_eventSubscribed; ?></td>
								<td><?php echo $male_paid+$female_paid+$nonsleeper_paid;?></td>
								<td><?php echo ($male_eventSubscribed+$female_eventSubscribed+$nonsleeper_eventSubscribed) - ($male_paid+$female_paid+$nonsleeper_paid);?></td>
								<td><?php echo $capacity_male+$capacity_female+$capacity_nonsleeper;?></td>
							</tr>
						</table>
						</div>
						<?php }?>
		 </div>
		 </div>
	</body>
</html>