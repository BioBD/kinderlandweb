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

    <script>
        $(function () {
            $("#sortable-table").tablesorter({widgets: ['zebra']});
            $(".datepicker").datepicker();
        });
        $(document).ready(function () {
            $("[name='my-checkbox']").bootstrapSwitch();
            $("[name='my-checkbox']").each(function (index) {
                if ($(this).attr("checkedInDatabase") != undefined)
                    $(this).bootstrapSwitch('state', true, true);
            });
            $('input[name="my-checkbox"]').on('switchChange.bootstrapSwitch', function (event, state) {
                var string = "<?= $this->config->item("url_link") ?>admin/toggleDocumentPayed/".concat($(this).attr("id"));
                var recarrega = "<?= $this->config->item("url_link") ?>admin/manage_documents/";
                $.post(string).done(function (data) {
                    if (data == 1)
                        alert("Estado do documento modificado com sucesso");
                    else {
                        alert("Problema ao modificar o estado do documento");
                        window.location = recarrega;
                    }
                });
            });
        });
        function post(path, params, method) {
            method = method || "post"; // Set method to post by default if not specified.

            // The rest of this code assumes you are not using a library.
            // It can be made less wordy if you use one.
            var form = document.createElement("form");
            form.setAttribute("method", method);
            form.setAttribute("action", path);
            for (var key in params) {
                if (params.hasOwnProperty(key)) {
                    var hiddenField = document.createElement("input");
                    hiddenField.setAttribute("type", "hidden");
                    hiddenField.setAttribute("name", key);
                    hiddenField.setAttribute("value", params[key]);
                    form.appendChild(hiddenField);
                }
            }
            document.body.appendChild(form);
            form.submit();
        }

        function sendInfoToModal(documentExpenseId, dateNow) {
            $("#documentexpenseId").html(documentExpenseId);
            $("#dateNow").html(dateNow);
        }

        function formaPagamento() {

            var documentexpenseId = document.getElementById("documentexpenseId").textContent;
            alert(documentexpenseId);
            var postingType = document.getElementById("postingType").value;
			alert(postingType);  
			
			if(postingType ==  "Crédito"){
				var accountName = document.getElementById("accountNameCredito").value;
				alert(accountName);
				var portions = document.getElementById("postingPortionsCredito").value;
        		alert(portions);                                  
            	var postingValue = document.getElementById("postingValueCredito").value;
            	alert(postingValue);
           		var postingDate = document.getElementById("postingDateCredito").value;
           		alert(postingDate);
           		$.post('<?= $this->config->item('url_link'); ?>admin/postingExpense',
                        {documentexpenseId: documentexpenseId, postingDate: postingDate, postingValue: postingValue, postingType: postingType, accountName: accountName, portions: portions },
                        function (data) {
                            if (data == "true") {
                                alert("Pagamento cadastrado com sucesso!");
                                location.reload();
                            } else if (data == "false") {
                                alert("Não foi possível cadastrar o pagamento!");
                                location.reload();
                            }
                        }
                );
			}

			
			if(postingType ==  "Débito"){                       
				var accountName = document.getElementById("accountNameDebito").value;
				alert(accountName);
            	var postingValue = document.getElementById("postingValueDebito").value;
            	alert(postingValue);
           		var postingDate = document.getElementById("postingDateDebito").value;
           		alert(postingDate);
           		$.post('<?= $this->config->item('url_link'); ?>admin/postingExpense',
                        {documentexpenseId: documentexpenseId, postingDate: postingDate, postingValue: postingValue, postingType: postingType, accountName: accountName },
                        function (data) {
                            if (data == "true") {
                                alert("Pagamento cadastrado com sucesso!");
                                location.reload();
                            } else if (data == "false") {
                                alert("Não foi possível cadastrar o pagamento!");
                                location.reload();
                            }
                        }
                );
			}

			if(postingType ==  "Dinheiro"){  
				var accountName = document.getElementById("accountNameDinheiro").value;
				alert(accountName);                     
            	var postingValue = document.getElementById("postingValueDinheiro").value;
            	alert(postingValue);
           		var postingDate = document.getElementById("postingDateDinheiro").value;
           		alert(postingDate);
           		$.post('<?= $this->config->item('url_link'); ?>admin/postingExpense',
                        {documentexpenseId: documentexpenseId, postingDate: postingDate, postingValue: postingValue, postingType: postingType, accountName: accountName },
                        function (data) {
                            if (data == "true") {
                                alert("Pagamento cadastrado com sucesso!");
                                location.reload();
                            } else if (data == "false") {
                                alert("Não foi possível cadastrar o pagamento!");
                                location.reload();
                            }
                        }
                );
			}
			if(postingType ==  "Cheque"){
				var accountName = document.getElementById("accountNameCheque").value;
				alert(accountName);
				var numberCheque = document.getElementById("postingNumberCheque").value;
        		alert(numberCheque);                                  
            	var postingValue = document.getElementById("postingValueCheque").value;
            	alert(postingValue);
           		var postingDate = document.getElementById("postingDateCheque").value;
           		alert(postingDate);
           		$.post('<?= $this->config->item('url_link'); ?>admin/postingExpense',
                        {documentexpenseId: documentexpenseId, postingDate: postingDate, postingValue: postingValue, postingType: postingType, accountName: accountName, numberCheque: numberCheque },
                        function (data) {
                            if (data == "true") {
                                alert("Pagamento cadastrado com sucesso!");
                                location.reload();
                            } else if (data == "false") {
                                alert("Não foi possível cadastrar o pagamento!");
                                location.reload();
                            }
                        }
                );
			}
			if(postingType ==  "Transferência"){
				var accountName = document.getElementById("accountNameTransferencia").value;
				alert(accountName);
				var postingValue = document.getElementById("postingValueTransferencia").value;
        		alert(postingValue);                                  
            	var postingDate = document.getElementById("postingDateTransferencia").value;
            	alert(postingDate);
            	var bankNumber = document.getElementById("postingBankNumberTransferencia").value;
            	var bankAgency = document.getElementById("postingAgencyTransferencia").value;
            	var accounNumber = document.getElementById("postingAccountTransferencia").value;
            	$.post('<?= $this->config->item('url_link'); ?>admin/postingExpense',
                        {documentexpenseId: documentexpenseId, postingDate: postingDate, postingValue: postingValue, postingType: postingType, accountName: accountName },
                        function (data) {
                            if (data == "true") {
                                alert("Pagamento cadastrado com sucesso!");
                                location.reload();
                            } else if (data == "false") {
                                alert("Não foi possível cadastrar o pagamento!");
                                location.reload();
                            }
                        }
                );            	
			}
			if(postingType ==  "Boleto"){
				var portions = document.getElementById("postingPortionsBoleto").value;
				var postingValue = "";
				var postingDate = "";
				alert(portions);
				alert(postingValue);
				for (var i = 1; i <= portions; i++){
					
					postingValue = postingValue.concat(document.getElementById("postingValueBoleto".concat(i)).value).concat("/");                   
            		postingDate = postingDate.concat(document.getElementById("postingDateBoleto".concat(i)).value).concat("/");
				}
				$.post('<?= $this->config->item('url_link'); ?>admin/postingExpense',
                        {documentexpenseId: documentexpenseId, postingDate: postingDate, postingValue: postingValue, postingType: postingType, accountName: accountName, portions: portions },
                        function (data) {
                            if (data == "true") {
                                alert("Pagamento cadastrado com sucesso!");
                                location.reload();
                            } else if (data == "false") {
                                alert("Não foi possível cadastrar o pagamento!");
                                location.reload();
                            }
                        }
                );

			}
        }
            


        function paymentType() {
            var type = document.getElementById("postingType").value;
            
            if(type == "Boleto"){
            	document.getElementById("Cheque").style.display = "none";
            	document.getElementById("Crédito").style.display = "none";
            	document.getElementById("Débito").style.display = "none";
            	document.getElementById("Dinheiro").style.display = "none";
            	document.getElementById("Transferência").style.display = "none";
            	
            	document.getElementById("Boleto").style.display = ""; 
            	                                   	
            }else if(type == "Cheque"){
            	document.getElementById("Boleto").style.display = "none";
            	document.getElementById("Crédito").style.display = "none";
            	document.getElementById("Débito").style.display = "none";
            	document.getElementById("Dinheiro").style.display = "none";
            	document.getElementById("Transferência").style.display = "none";
            	
            	document.getElementById("Cheque").style.display = ""; 
            	
            }else if(type == "Crédito"){
            	document.getElementById("Boleto").style.display = "none";
            	document.getElementById("Cheque").style.display = "none";
            	document.getElementById("Débito").style.display = "none";
            	document.getElementById("Dinheiro").style.display = "none";
            	document.getElementById("Transferência").style.display = "none";
            	
            	document.getElementById("Crédito").style.display = ""; 
            	
            }else if(type == "Débito"){
            	document.getElementById("Boleto").style.display = "none";
            	document.getElementById("Cheque").style.display = "none";
            	document.getElementById("Crédito").style.display = "none";
            	document.getElementById("Dinheiro").style.display = "none";
            	document.getElementById("Transferência").style.display = "none";
            	
            	document.getElementById("Débito").style.display = ""; 
            	
            }else if(type == "Dinheiro"){
            	document.getElementById("Boleto").style.display = "none";
            	document.getElementById("Cheque").style.display = "none";
            	document.getElementById("Crédito").style.display = "none";
            	document.getElementById("Débito").style.display = "none";
            	document.getElementById("Transferência").style.display = "none";
            	
            	document.getElementById("Dinheiro").style.display = ""; 
            	
            }else if(type == "Transferência"){
            	document.getElementById("Boleto").style.display = "none";
            	document.getElementById("Cheque").style.display = "none";
            	document.getElementById("Crédito").style.display = "none";
            	document.getElementById("Débito").style.display = "none";
            	document.getElementById("Dinheiro").style.display = "none";
            	
            	document.getElementById("Transferência").style.display = ""; 
            	
            }
            
        }

		function postingPortions(){
			var portions = document.getElementById("postingPortionsBoleto").value;
			for(var i = 1; i <= 10; i++){
				if(i <= portions){
					document.getElementById("Boleto".concat(i)).style.display = "";
				}
				else{
					document.getElementById("Boleto".concat(i)).style.display = "none";
				}
			}
			
		}

		function accountUpdate(id,date,value){
			var account_name = document.getElementById("accounts_".concat(id)).value;

			var names = document.getElementById("accountsNames").value;
			names = names.split("/");

			var ok = 0;

			for(var i = 0; i < names.length; i++){
				if(account_name.localeCompare(names[i]) == 0){
					ok = 1;
					break;
				}
			}	

			if(ok == 1){
				$.post('<?= $this->config->item('url_link'); ?>admin/updateAccountName',
                        {id: id, date: date, value: value, account_name: account_name},
                        function (data) {
                            if (data == "true") {
                                alert("Nome de Conta atribuído com sucesso!");
                                location.reload();
                            } else if (data == "false") {
                                alert("Ocorreu um erro na atribuição de Nome de Conta!");
                                location.reload();
                            }
                        }
                );
			}else{
				alert("Nome de conta inválido. Insira um nome existente!");
			}		
		}

		$(function() {
		    var availableTags = document.getElementById("accountsNames").value;
		    availableTags = availableTags.split("/");

		    $(".accounts").autocomplete({
			      source: availableTags
			});
		  });		
        
    </script>

    <body>
        <div class="scroll">
        		<form method="GET">
                <input type="hidden" name="option" value="<?= $option ?>"/>
                Ano: <select name="year" onchange="this.form.submit()" id="year">
                    <?php
                    foreach ($years as $y) {
                        $selected = "";
                        if ($y == $year)
                            $selected = "selected";
                        echo "<option $selected value='$y'>$y</option>";
                    }
                    ?>
                </select>

                Mês: <select name="month" onchange="this.form.submit()" id="month">
                    <option value="0" >Todos</option>
                    <?php

                    function getMonthName($m) {
                        switch ($m) {
                            case 1: return "Janeiro";
                            case 2: return "Fevereiro";
                            case 3: return "Março";
                            case 4: return "Abril";
                            case 5: return "Maio";
                            case 6: return "Junho";
                            case 7: return "Julho";
                            case 8: return "Agosto";
                            case 9: return "Setembro";
                            case 10: return "Outubro";
                            case 11: return "Novembro";
                            case 12: return "Dezembro";
                        }
                    }

                    for ($m = 1; $m <= 12; $m++) {
                        $selected = "";
                        if ($m == $month)
                            $selected = "selected";
                        echo "<option $selected value='$m'>" . getMonthName($m) . "</option>";
                    }
                    ?>
                </select>
            </form>
            <div class="row">
                <?php // require_once APPPATH.'views/include/common_user_left_menu.php'  ?>
                <div class="col-lg-12 middle-content">

                    <a href="<?= $this->config->item("url_link") ?>admin/createDocument" >
                    <input style="display:none" id="accountsNames" value="<?php echo $accountNames;?>">

                        <button id="create" class="btn btn-primary"  value="Criar novo documento" >Novo lançamento</button>
                    </a>

             
                    <br /><br />
                    <?php
                    if (isset($documents)) {
                        ?>
                        <table class="table table-bordered table-striped table-min-td-size" style="width:1200px" id="sortable-table">
                        	<tr>
                        		<th style="width:70px; text-align: center">Data</th>
                        		<th style="width:20px; text-align: center">Tipo</th>
                        		<th style="width:70px; text-align: center">Valor</th>
                        		<th style="width:140px; text-align: center">Descrição</th>
                        		<th style="width:70px; text-align: center">Imagem</th>
                        		<th style="width:100px; text-align:center">Forma de Pagamento</th>
                        		<th style="width:70px; text-align: center">Pago</th>
                        		<th colspan=2 style="width:300px; text-align: center">Nome de Conta</th>
                        	</tr>
                        	<tr>
                        		<th></th>
                        		<th></th>
                        		<th></th>
                        		<th></th>
                        		<th></th>
                        		<th></th>
                        		<th></th>
                        		<th style="width:200px; text-align: center">Nome</th>
                        		<th style="width:100px; text-align: center">Ação</th>
                        	</tr>
             	          <?php
                            foreach ($documents as $document) {
                                ?>

                                <tr>
                                    <td>
                                        <?= date_format(date_create($document->document_date), 'd/m/y'); ?></td>

                                    <td>
                                            <?php switch($document->document_type){
                                            	case "nota fiscal":
                                            		echo "NF";
                                            		break;
                                            	case "cupom fiscal":
                                            		echo "CF";
                                            		break;
                                            	case "recibo":
                                            		echo "rec";
                                            		break;
                                            	case "boleto":
                                            		echo "bol";
                                            		break; 
                                            }                                         
                                            
                                            ?> </td>
                                    <td><?php echo $document->document_value; ?> </td>
                                    <td><a href="<?php echo $this->config->item("url_link"); ?>admin/editDocument/<?php echo $document->document_expense_id ?>"><?php echo $document -> document_description;?></a></td>
                                    <td><a href="<?php echo $this->config->item("url_link"); ?>admin/viewDocumentUpload?document_id=<?php echo $document->document_expense_id;?>">
                                        <button <?php
                                        if ($document->document_expense_upload_id) {
                                            echo "class='btn btn-success'>Atualizar";
                                        } else {
                                            echo "class='btn btn-danger'>Upload";
                                        }
                                        ?> 
                                        </button>
                                        </a>
                                    </td>
                                    <?php if ($document->posting_value == "" && $document->posting_date == "") { ?>
                                        <td><button class="btn btn-danger" onclick="sendInfoToModal('<?= $document->document_expense_id ?>', '<?= date('Y-m-d') ?>')" data-toggle="modal" data-target="#myModal">Tipo</button></td>
                                    <?php } else { ?>
                                        <td><button class="btn btn-success">Tipo</button> </td>
                                    <?php } ?>
                                    <td><input type="checkbox" data-inverse="true" name="my-checkbox" data-size="mini" id="<?=$document->document_expense_id?>" 
        							<?php if($document->payed == "t") echo "checkedInDatabase='true'"; if ($document->posting_value == "" && $document->posting_date == "") echo "disabled";?> /> </td>
                                	<td><input <?php if ($document->posting_value == "" && $document->posting_date == "") echo "disabled" ?> type="text" class="accounts" id="accounts_<?= $document->document_expense_id ?>" value="<?php if($document->account_name) echo $document->account_name; else echo ""; ?>">
									</td>
									<?php if ($document->account_name == "") { ?>
                                        <td><button <?php if ($document->posting_value == "" && $document->posting_date == "") echo "disabled" ?> class="btn btn-danger" onclick="accountUpdate('<?= $document->document_expense_id ?>', '<?= $document->posting_date ?>','<?= $document->posting_value ?>')">Salvar</button></td>
                                    <?php } else { ?>
                                        <td><button <?php if ($document->posting_value == "" && $document->posting_date == "") echo "disabled" ?> class="btn btn-success" onclick="accountUpdate('<?= $document->document_expense_id ?>', '<?= $document->posting_date ?>','<?= $document->posting_value ?>')">Atualizar</button> </td>
                                    <?php } ?>
                                </tr>
                                
                                <?php
                            }
                            ?> </table>
                            <?php
                    } else {
                        ?>
                        <h3>
                            Nenhum documento registrado.
                        </h3>
                        <?php
                    }
                    ?>


                </div>
            </div>
            
                <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="solicitar-convite" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="modal_title">Forma de Pagamento</h4>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-lg-12 middle-content">
                                        	                                         
                                            <div class="row">
                                                <div class="form-group">
                                                    <div class="col-lg-12">
                                                    	<label for="postingType" style="width: 170px; padding-left:0px; margin-bottom:0px; margin-top:7px;" class="col-lg-1 control-label"> Tipo de Pagamento: </label>
                                                          <div style="width: 210px; padding-left:0px" class="col-lg-2 control-label">    
                                                              <select style="width: 190px" class="form-control" name="postingType" id="postingType" onchange="paymentType()" >
                                                                <option> - Selecione - </option>
                                                                <option value="Boleto">Boleto</option> 
                                                                <option value="Cheque">Cheque</option>
                                                                <option value="Crédito" >Crédito</option>
                                                                <option value="Débito">Débito</option>  
                                                                <option value="Dinheiro" >Dinheiro</option>  
                                                                <option value="Transferência">Transferência</option>

                                                               </select>
                                                             </div>
                                                     </div>
                                                  </div>
                                               </div>          
                                                           

                                                            <input type="hidden" id="documentexpenseId" name="documentexpenseId" value="" />
                                                            <input type="hidden" id="dateNow" name="dateNow" value="" />	
                                                            <input type="hidden" id="postingType" name="postingType" value="" >		
                                                            <br/>
                                                            
                                                            										
                                                            <div id = "Boleto" style="display: none">

                                                            		
	                                                            	<tr>
	                                                            	<label for="postingPortionsBoleto" style="width: 170px; padding-left:0px; margin-bottom:0px; margin-top:7px;" class="col-lg-1 control-label"> Número de parcelas: </label>
		                                                            <div style="width: 350px; padding-left:0px" class="col-lg-2 control-label">
	                                                            		<select style="width: 190px" class="form-control" name="postingPortionsBoleto" id="postingPortionsBoleto" onchange="postingPortions()" >
                                                                			<option> - Selecione - </option>
                                                               				 <?php for ($i = 1; $i <= 10; $i++){?>
                                                                			<option value="<?= $i ?>"> <?php echo $i ?> </option>
                                                               				 <?php }?>
                                                              		   </select>
                                                               		</div>
                                                               		</tr> <br/><br/><br/>
                                                               		<tr>
                                                               		<td><b> Nome da conta: </b></td>
                                                               		<input type="text" id="accountNameBoleto" name="accountNameBoleto" ></input>
                                                               		</tr>
                                                               		<br/><br/><br/>
                                                               <?php for($i = 1; $i <= 10; $i++){?>
                                                               <tr>
                                                               <div class="col-lg-12 control_label" id="Boleto<?php echo $i ?>" style="display: none; padding-left:0px" >
                                                               		<div class="row">
                                                               			<div class="col-lg-12">
		                                                                <label for="postingValueBoleto<?php echo $i?>" style="width: 50px; padding-left:0px; margin-bottom:0px; margin-top:7px;" class="col-lg-1 control-label"> Valor: </label>
		                                                            	<div style="width: 210px; padding-left:0px" class="col-lg-2 control-label">
		                                                            	<input style="width: 200px" class="form-control" type="text" id="postingValueBoleto<?php echo $i?>" name="postingValueBoleto<?php echo $i?>" ></input> 
	                                                            		</div>
	                                                            		 <label for="postingDateBoleto<?php echo $i ?>" style="width: 50px; padding-left:0px; margin-bottom:0px; margin-top:7px;" class="col-lg-1 control-label">Data </label>
	                                                            		<div style="width: 210px; padding-left:0px" class="col-lg-2 control-label">
		                                                            	<input style="width: 200x" class="form-control" type="text" id="postingDateBoleto<?php echo $i ?>" name="postingDateBoleto<?php echo $i ?>" ></input> 
		                                                            	<br/><br/>
		                                                        </div>
		                                                        </div>
		                                                        </div>
		                                                        </div>
		                                                        </tr>
		                                                        
		                                                        <?php } ?>
		                                                        <br>
		                                                        	<tr>
		                                                        		<div class="row">
		                                                        		<div class="col-lg-7 control_label">
		                                                            	<button class="btn btn-primary" onClick="formaPagamento()">Salvar</button>  
		                                                            	<button class="btn btn-danger" data-dismiss="modal">Fechar</button> 
		                                                            	</div>  
		                                                            	</div>
		                                                            	                                                      	
	                                                            	</tr>
	                                                            </div>

                                                           
                                                            
                                                            <div id = "Cheque" style="display: none">
                                                            	<tr> 
	                                                                <td> Número do Cheque: </td> <br>
	                                                            	<input style="width: 200px" class="form-control" type="text" id="postingNumberCheque" name="postingNumberCheque" ></input> <br><br>
                                                            	</tr>
                                                            	<tr>
                                                               		<td> Nome da conta: </td> <br>
                                                               		<input style="width: 200px" class="form-control" type="text" id="accountNameCheque" name="accountNameCheque" ></input><br><br>
                                                               		</tr>
                                                            	<tr> 
	                                                                <td> Valor: </td> <br>
	                                                            	<input style="width: 200px" class="form-control" type="text" id="postingValueCheque" name="postingValueCheque" ></input> <br><br>
                                                            	</tr>
                                                            	<tr>
                                                            		<td> Data: </td><br>
	                                                            	<input style="width: 200px" class="form-control" type="text" id="postingDateCheque" name="postingDateCheque" ></input> <br><br>
                                                            		<button class="btn btn-primary" onClick="formaPagamento()">Salvar</button> 
                                                            		<button class="btn btn-danger" data-dismiss="modal">Fechar</button>
                                                            	</tr>

                                                            </div>
                                                            
                                                            <div id = "Crédito" style="display: none">
                                                            	<tr>
                                                               		<td> Nome da conta: </td><br>
                                                               		<input style="width: 200px" class="form-control" type="text" id="accountNameCredito" name="accountNameCredito" ></input><br><br>
                                                               		</tr>
                                                            	<tr> 
	                                                                <td> Valor: </td><br> 
	                                                            	<input style="width: 200px" class="form-control" type="text" id="postingValueCredito" name="postingValueCredito" ></input> <br><br>
                                                            	</tr>
                                                            	<tr> 
	                                                                <td> Número de Parcelas: </td><br>
	                                                            	<input style="width: 200px" class="form-control" type="text" id="postingPortionsCredito" name="postingPortionsCredito" ></input> <br><br>
                                                            	</tr>
                                                            	<tr>
                                                            		<td> Data: </td> <br>
	                                                            	<input style="width: 200px" class="form-control" type="text" id="postingDateCredito" name="postingDateCredito" ></input> <br><br>
                                                            		<button class="btn btn-primary" onClick="formaPagamento()">Salvar</button> 
                                                            		<button class="btn btn-danger" data-dismiss="modal">Fechar</button>
                                                            	</tr>

                                                            </div>
                                                            
                                                            <div id = "Débito" style="display: none">
                                                            	<tr>
                                                               		<td> Nome da conta: </td><br>
                                                               		<input style="width: 200px" class="form-control" type="text" id="accountNameDebito" name="accountNameDebito" ></input><br><br>
                                                               		</tr>
                                                            	<tr> 
	                                                                <td> Valor: </td><br>
	                                                            	<input style="width: 200px" class="form-control" type="text" id="postingValueDebito" name="postingValueDebito" ></input> <br><br>
                                                            	</tr>
                                                            	<tr>
                                                            		<td> Data: </td> <br>
	                                                            	<input style="width: 200px" class="form-control" type="text" id="postingDateDebito" name="postingDateDebito" ></input> <br><br>
                                                            		<button class="btn btn-primary" onClick="formaPagamento()">Salvar</button> 
                                                            		<button class="btn btn-danger" data-dismiss="modal">Fechar</button>
                                                            	</tr>

                                                            </div>
                                                            
                                                            <div id = "Dinheiro" style="display: none"> 
                                                            	<tr>
                                                               		<td>Nome da conta: </td><br>
                                                               		<input style="width: 200px" class="form-control" type="text" id="accountNameDinheiro" name="accountNameDinheiro" ></input><br><br>
                                                               		</tr>
                                                            	<tr> 
	                                                                <td> Valor: </td><br>
	                                                            	<input style="width: 200px" class="form-control" type="text" id="postingValueDinheiro" name="postingValueDinheiro" ></input> <br><br>
                                                            	</tr>
                                                            	<tr>
                                                            		<td> Data: </td><br>
	                                                            	<input style="width: 200px" class="form-control" type="text" id="postingDateDinheiro" name="postingDateDinheiro" ></input> <br><br>
                                                            		<button class="btn btn-primary" onClick="formaPagamento()">Salvar</button> 
                                                            		<button class="btn btn-danger" data-dismiss="modal">Fechar</button>
                                                            	</tr>

                                                            </div>
                                                            
                                                            <div id = "Transferência" style="display: none"> 
                                                            	<tr>
                                                               		<td>Nome da conta: </td><br>
                                                               		<input style="width: 200px" class="form-control" type="text" id="accountNameTransferencia" name="accountNameTransferencia" ></input><br><br>
                                                               		</tr>
                                                            	<tr> 
	                                                                <td> Valor: </td> <br>
	                                                            	<input style="width: 200px" class="form-control" type="text" id="postingValueTransferencia" name="postingValueTransferencia" ></input> <br><br>
                                                            	</tr>
                                                            	<tr>
                                                            		<td> Data: </td><br>
	                                                            	<input style="width: 200px" class="form-control" type="text" id="postingDateTransferencia" name="postingDateTransferencia" ></input> <br><br>
                                                            	
                                                            	</tr>
                                                            	<tr> 
	                                                                <td> Banco: </td> <br>
	                                                            	<input style="width: 200px" class="form-control" type="text" id="postingBankNumberTransferencia" name="postingBankNumberTransferencia" ></input> <br><br>
                                                            	</tr>
                                                            	<tr> 
	                                                                <td> Agência: </td> <br>
	                                                            	<input style="width: 200px" class="form-control" type="text" id="postingAgencyTransferencia" name="postingAgencyTransferencia" ></input> <br><br>
                                                            	</tr>
                                                            	<tr> 
	                                                                <td> Conta: </td> <br>
	                                                            	<input style="width: 200px" class="form-control" type="text" id="postingAccountTransferencia" name="postingAccountTransferencia" ></input> <br><br>
                                                            		<button class="btn btn-primary" onClick="formaPagamento()">Salvar</button> 
                                                            		<button class="btn btn-danger" data-dismiss="modal">Fechar</button>
                                                            	</tr>
                                                            	

                                                            </div>

                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </body>
</div>
</div>
</div>
</body>
</html>