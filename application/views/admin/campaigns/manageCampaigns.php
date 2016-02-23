
<html lang = "pt-br">
    <head>
        <meta charset = "UTF-8">
        <title>Colônia Kinderland</title>

        <link href = "<?= $this->config->item('assets'); ?>css/basic.css"
              rel = "stylesheet" />
              <!--<link href = "<?= $this->config->item('assets'); ?>css/old/screen.css" rel = "stylesheet" /> -->
        <link href = "<?= $this->config->item('assets'); ?>css/bootstrap.min.css"
              rel = "stylesheet" />
        <link rel = "stylesheet"
              href = "<?= $this->config->item('assets'); ?>css/themes/base/jquery-ui.css" />
        <link rel = "stylesheet"
              href = "<?= $this->config->item('assets'); ?>css/bootstrap-switch.min.css">
        <link rel = "stylesheet"
              href = "<?= $this->config->item('assets'); ?>css/theme.default.css" />
        <script type = "text/javascript"
        src = "<?= $this->config->item('assets'); ?>js/jquery-2.0.3.min.js"></script>
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
        <script>$(document).ready(function () {
                $("[name='my-checkbox']").bootstrapSwitch();
                $("[name='my-checkbox']").each(function (index) {
                    if ($(this).attr("checkedInDatabase") != undefined)
                        $(this).bootstrapSwitch('state', true, true);
                });
                $('input[name="my-checkbox"]').on('switchChange.bootstrapSwitch', function (event, state) {
                    var string = "<?= $this->config->item("url_link") ?>events/toggleEnable/".concat($(this).attr("id"));
                    var recarrega = "<?= $this->config->item("url_link") ?>admin/manageEvents/";
                    $.post(string).done(function (data) {
                        if (data == 1)
                            alert("Campanha modificada com sucesso");
                        else {
                            alert("Problema ao modificar o estado da campanha");
                            window.location = recarrega;
                        }
                    });
                });
            });
        </script>
        <style>

            div.scroll{

                width:100%;
                height:100%;
                overflow-x:hidden;
                padding-left:6%

            }

        </style>
    <body>
        <div class="scroll">
            <div class="row">
                <?php // require_once APPPATH.'views/include/common_user_left_menu.php'  ?>
                <div class="col-lg-10 middle-content">
                    <body onunload="window.opener.location.reload();"><a target='_blank' onclick="window.open('<?= $this->config->item("url_link") ?>admin/campaignCreate', 'dd');
                            return false;" href=""><button id="create" class="btn btn-primary"  value="Criar nova campanha" >Criar nova campanha</button></a>
                        <br /><br />
                        <?php
                        if (isset($campaigns) && count($campaigns) > 0) {
                            ?>
                            <table class="table"><tr><th>Ano</th><th>Data Inicio</th><th>Data Fim</th></tr>
                                        <?php
                                        foreach ($campaigns as $campaign) {
                                            ?><tr>
                                    <body onunload="window.opener.location.reload();"><td><a target='_blank'  onclick="window.open('<?php echo $this->config->item("url_link"); ?>admin/editCampaign/<?php echo $campaign->getCampaignId() ?>', 'dd'); return false;" href="">
                                            <?php echo $campaign->getCampaignYear(); ?></a></td>

                                    <td><?= date_format(date_create($campaign->getDateStart()), 'd/m/y'); ?> </td>
                                    <td><?= date_format(date_create($campaign->getDateFinish()), 'd/m/y'); ?> </td>
                                    </tr>
                                    <?php
                                }
                                ?> </table>
                            <?php
                        } else {
                            ?>
                            <h3>
                                Nenhuma campanha registrada para acontecer nos próximos dias.
                                <br />Continue acompanhando a Colônia Kinderland pelo nosso website.
                            </h3>
                            <?php
                        }
                        ?>
                </div>
                </body>
            </div>
        </div>
    </div>
</body>
</html>

