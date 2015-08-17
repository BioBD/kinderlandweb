<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Colônia Kinderland</title>

<link href="<?= $this->config->item('assets'); ?>css/basic.css"
	rel="stylesheet" />
<!--<link href="<?= $this->config->item('assets'); ?>css/old/screen.css" rel="stylesheet" />-->
<link href="<?= $this->config->item('assets'); ?>css/bootstrap.min.css"
	rel="stylesheet" />
<link rel="stylesheet"
	href="<?= $this->config->item('assets'); ?>css/themes/base/jquery-ui.css" />
<link rel="stylesheet"
	href="<?= $this->config->item('assets'); ?>css/bootstrap-switch.min.css">
</script>
<link rel="stylesheet"
	href="<?= $this->config->item('assets'); ?>css/theme.default.css" />
<script type="text/javascript"
	src="<?= $this->config->item('assets'); ?>js/jquery-2.0.3.min.js"></script>
<script type="text/javascript"
	src="<?= $this->config->item('assets'); ?>js/ui/jquery-ui.js"></script>
<script type="text/javascript"
	src="<?= $this->config->item('assets'); ?>js/bootstrap.min.js"></script>
<script type="text/javascript"
	src="<?= $this->config->item('assets'); ?>js/jquerysettings.js"></script>
<script type="text/javascript"
	src="<?= $this->config->item('assets'); ?>js/jquery/jquery.redirect.js"></script>
<script type="text/javascript"
	src="<?= $this->config->item('assets'); ?>js/formValidationFunctions.js"></script>
<script type="text/javascript"
	src="<?= $this->config->item('assets'); ?>js/bootstrap-switch.min.js"></script>
<script type="text/javascript"
	src="<?= $this->config->item('assets'); ?>js/jquery/jquery.mask.js"></script>
<script type="text/javascript"
	src="<?= $this->config->item('assets'); ?>js/jquery.tablesorter.js"></script>

</head>
<body>
	
	<div class="main-container-report">
		<div class="row">
			<div class="col-lg-10" bgcolor="red">
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
							<option value="0" <?php if(!isset($colonia_escolhida)) echo "selected"; ?>>Todas</option>
							<?php
							foreach ( $camps as $camp ) {
								$selected = "";
								if ($colonia_escolhida == $camp)
									$selected = "selected";
								echo "<option $selected value='$camp'>$camp</option>";
							}
							?>
						</select>
				</form>
				<table class="table table-bordered table-striped table-min-td-size"
					style="max-width: 600px;">
					
						<tr>
							<th align="right"></th>
							<th align="right">Feminino</th>
							<th align="right">Masculino</th>
							<th align="right">Total</th>
					    <tr>
							<th align="right">Inscritos</th>
							<td align='right'> <?php echo $countsF->inscrito; ?> </td>
							<td align='right'> <?php echo $countsM->inscrito; ?> </td>
							<td align='right'> <?php echo $countsT->inscrito; ?> </td>
						</tr>
						<tr>
							<th align="right">Pré-inscrições em elaboração</th>
							<td align='right'> <?php echo $countsF->elaboracao; ?> </td>
							<td align='right'> <?php echo $countsM->elaboracao; ?> </td>
							<td align='right'> <?php echo $countsT->elaboracao; ?> </td>
						</tr>
						<tr>
							<th align="right">Pré-inscrições aguardando validação</th>
							<td align='right'> <?php echo $countsF->aguardando_validacao; ?> </td>
							<td align='right'> <?php echo $countsM->aguardando_validacao; ?> </td>
							<td align='right'> <?php echo $countsT->aguardando_validacao; ?> </td>
						</tr>
						<tr>
							<th align="right" width='200px'>Pré-inscrições não validadas</th>
							<td align='right'> <?php echo $countsF->nao_validada; ?> </td>
							<td align='right'> <?php echo $countsM->nao_validada; ?> </td>
							<td align='right'> <?php echo $countsT->nao_validada; ?> </td>
						</tr>
						<tr>
							<th align="right" width='200px'>Pré-inscrições validadas</th>
							<td align='right'> <?php echo $countsF->validada; ?> </td>
							<td align='right'> <?php echo $countsM->validada; ?> </td>
							<td align='right'> <?php echo $countsT->validada; ?> </td>
						</tr>
						<tr>
							<th align="right" width='200px'>Pré-inscrições na fila de espera</th>
							<td align='right'> <?php echo $countsF->fila_espera; ?> </td>
							<td align='right'> <?php echo $countsM->fila_espera; ?> </td>
							<td align='right'> <?php echo $countsT->fila_espera; ?> </td>
						</tr>
						<tr>
							<th align="right" width='200px'>Pré-inscrições aguardando
								pagamento</th>
							<td align='right'> <?php echo $countsF->aguardando_pagamento; ?> </td>
							<td align='right'> <?php echo $countsM->aguardando_pagamento; ?> </td>
							<td align='right'> <?php echo $countsT->aguardando_pagamento; ?> </td>
						</tr>
						
						<tr>
							<th align="right" width='200px'>Total</th>
							<td align='right'> <?php echo $countsF->inscrito + $countsF->aguardando_pagamento + $countsF->fila_espera 
	                                + $countsF->validada + $countsF->nao_validada + $countsF->aguardando_validacao 
	                                + $countsF->elaboracao; ?> 
	                                </td>
							<td align='right'> <?php echo $countsM->inscrito + $countsM->aguardando_pagamento + $countsM->fila_espera 
	                                + $countsM->validada + $countsM->nao_validada + $countsM->aguardando_validacao 
	                                + $countsM->elaboracao; ?> 
	                                </td>
							<td width="60px" align='right'>
	                                <?php echo $countsT->inscrito + $countsT->aguardando_pagamento + $countsT->fila_espera 
	                                + $countsT->validada + $countsT->nao_validada + $countsT->aguardando_validacao 
	                                + $countsT->elaboracao; ?>
	                            </td>
						</tr>
						<tr>
							<th	align="right" width='200px'>Porcentagem de Inscritos</th>
							<td align='right'> <?php 
									$countTotalF = $countsF->inscrito + $countsF->aguardando_pagamento + $countsF->fila_espera 
	                                + $countsF->validada + $countsF->nao_validada + $countsF->aguardando_validacao 
	                                + $countsF->elaboracao;
									
									if($countTotalF) echo number_format(($countsF->inscrito/$countTotalF)*100,1); 
									else echo "0.0";
									echo "%"; ?> 
	                                </td>
							<td align='right'> <?php 
									$countTotalM = $countsM->inscrito + $countsM->aguardando_pagamento + $countsM->fila_espera 
	                                + $countsM->validada + $countsM->nao_validada + $countsM->aguardando_validacao 
	                                + $countsM->elaboracao;
							
									if($countTotalM) echo number_format(($countsM->inscrito/$countTotalM)*100,1);
									else echo "0.0";
									echo "%"; ?> 
	                                </td>
							<td width="60px" align='right'>
	                                <?php 
	                                $countTotalT = $countsT->inscrito + $countsT->aguardando_pagamento + $countsT->fila_espera 
	                                + $countsT->validada + $countsT->nao_validada + $countsT->aguardando_validacao 
	                                + $countsT->elaboracao;
	                                
	                                if($countTotalT) echo number_format(($countsT->inscrito/$countTotalT)*100,1);
	                                else echo "0.0";
									echo "%"; ?>
	                            </td>
	                    </tr>
				</table>
				<table class="table table-bordered table-striped table-min-td-size"
					style="max-width: 600px;">
					<tr>
						<th align="right">Potencial de Inscritos por Colônia</th>
						<td width="60px" align='right'> <?php echo $potInscritos = $countsT->aguardando_pagamento 
						+ $countsT->inscrito; ?></td>
						<th align="right">Porcentagem de Inscritos por Colônia</th>
						<td width="60px" align='right'><?php 
						if($potInscritos)
							echo number_format(($countsT->inscrito/$potInscritos)*100,1);
						 else echo "0.0";
						 echo "%"; ?>  </td>				
					</tr>
				</table>
				<table class="table table-bordered table-striped table-min-td-size"
					style="max-width: 600px;">
					<tr>
							<th align="right"></th>
							<th align="right">Feminino</th>
							<th align="right">Masculino</th>
							<th align="right">Total</th>
					    <tr>
						<tr>
								<th align="right" width='200px'>Cancelados</th>
								<td align='right'> <?php echo $countsF->cancelado; ?> </td>
								<td align='right'> <?php echo $countsM->cancelado; ?> </td>
								<td align='right'> <?php echo $countsT->cancelado; ?> </td>
							</tr>
							<tr>
								<th align="right" width='200px'>Desistentes</th>
								<td align='right'> <?php echo $countsF->desistente; ?> </td>
								<td align='right'> <?php echo $countsM->desistente; ?> </td>
								<td align='right'> <?php echo $countsT->desistente; ?> </td>
							</tr>
							<tr>
								<th align="right" width='200px'>Excluidos</th>
								<td align='right'> <?php echo $countsF->excluido; ?> </td>
								<td align='right'> <?php echo $countsM->excluido; ?> </td>
								<td align='right'> <?php echo $countsT->excluido; ?> </td>
							</tr>
						<tr>
							<th align="right" width='200px'>Total</th>
							<td align='right'> <?php echo $countsF->excluido + $countsF->desistente + $countsF->cancelado; ?> 
	                                </td>
							<td align='right'> <?php echo $countsM->excluido + $countsM->desistente + $countsM->cancelado; ?> 
	                                </td>
							<td width="60px" align='right'>
	                                <?php echo $countsT->excluido + $countsT->desistente + $countsT->cancelado; ?>
	                            </td>
						</tr>
				</table>
				
				<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="solicitar-convite" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="modal_title">Detalhes das Inscrições</h4>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-lg-12 middle-content">			
								<div class="row">
									<div class="form-group">
										<div class="col-lg-12">
											<?php
                            foreach ($colonists as $colonist) {
                                ?>
                                <tr>
                                    <td><a id="<?= $colonist->fullname ?>" target="_blank" href="<?= $this -> config -> item('url_link') ?>admin/viewColonistInfo?colonistId=<?= $colonist -> colonist_id ?>&summerCampId=<?= $colonist -> summer_camp_id ?>"><?= $colonist -> colonist_name ?></a></td>
                                    <td><?= $colonist->camp_name ?></td>
                                    <td><a id="<?= $colonist -> fullname ?>" target="_blank" href="<?= $this -> config -> item('url_link') ?>user/details?id=<?= $colonist -> person_user_id ?>"><?= $colonist -> user_name ?></a></td>
                                    <td><?= $colonist->email ?></td>
                                    <td id="colonist_situation_<?=$colonist->colonist_id?>_<?=$colonist->summer_camp_id?>"><font color="
                                <?php
                                    switch ($colonist->situation) {
                                        case SUMMER_CAMP_SUBSCRIPTION_STATUS_WAITING_VALIDATION: echo "#061B91"; break;
                                        case SUMMER_CAMP_SUBSCRIPTION_STATUS_VALIDATED: echo "#017D50"; break;
                                        case SUMMER_CAMP_SUBSCRIPTION_STATUS_VALIDATED_WITH_ERRORS: echo "#FF0000"; break;
                                        case SUMMER_CAMP_SUBSCRIPTION_STATUS_FILLING_IN: echo "#555555"; break;
                                        case SUMMER_CAMP_SUBSCRIPTION_STATUS_CANCELLED: echo "#FF0000"; break;
                                        case SUMMER_CAMP_SUBSCRIPTION_STATUS_EXCLUDED: echo "#FF0000"; break;
                                        case SUMMER_CAMP_SUBSCRIPTION_STATUS_GIVEN_UP: echo "#FF0000"; break;
                                        case SUMMER_CAMP_SUBSCRIPTION_STATUS_QUEUE: echo "#555555"; break;
                                        case SUMMER_CAMP_SUBSCRIPTION_STATUS_PENDING_PAYMENT: echo "#061B91"; break;
                                        case SUMMER_CAMP_SUBSCRIPTION_STATUS_SUBSCRIBED: echo "#017D50"; break;
                                    }
                                ?>"><?= $colonist -> situation_description ?></td>
                                </tr>
                                <?php
                            }
                            ?>

										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
					</div>
				</div>
			</div>
		</div>
		
			</div>
		</div>
	</div>
</body>
</html>