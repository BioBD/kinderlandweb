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



    <div class = "row">
        <div class="col-lg-10">
            <h4>Se algum documento já foi enviado, um novo envio de documento substituirá o anterior. 
                Apenas o último documento enviado será considerado para validação</h4>
            <form enctype="multipart/form-data" action="<?= $this->config->item('url_link'); ?>summercamps/saveDocument" method="POST">
                <br>
                <input type="hidden" name="MAX_FILE_SIZE" value="2000000" />
                Escolha um arquivo para enviar, aceitamos apenas arquivos .pdf, jpg, .jpeg e .png de até 2MB.
                <br>
                <input  type="file" name="uploadedfile" class="btn btn-primary"/> 
                <br />
                <input type="submit" value="Enviar documento" class="btn btn-primary"/> 

            </form>
            <br>
            <br>

            <?php if ($upload_id) { ?>
                <a target="_blank" href="<?= $this->config->item('url_link'); ?>admin/verifyDocumentExpense?upload_id=<?php echo $upload_id->document_expense_upload_id; ?>">
                    <button class="btn btn-primary">
                        Visualizar último documento enviado
                    </button> </a>

            <?php } ?> 






        </div>
    </div>
</html>
