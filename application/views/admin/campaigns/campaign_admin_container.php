<div class = "row">
    <?php $actual_screen = "CAMPANHA_ASSOCIADOS"; ?>
    <?php
    require_once APPPATH . 'core/menu_helper.php';
    require_once renderMenu($permissions);
    ?>
    <script type="text/javascript" src="<?= $this->config->item('assets'); ?>js/select.box.iframe.js"></script>
    <div class="col-lg-12">
        <div class="row">
                <select class="report-select" name="report_select" id="report_select">
                    <?php if (in_array(SYSTEM_ADMIN, $permissions) || in_array(SECRETARY, $permissions)) { ?>
                        <option selected="selected" value="<?= $this->config->item('url_link'); ?>admin/manageCampaigns"> Cadastro de Campanha de Sócios </option>
                    <?php } ?>
                </select>
            </div>
        <hr class="footer-hr" />

        <div class="row">
            <iframe class="frame-section" src="<?= $this->config->item('url_link'); ?>admin/manageCampaigns" />
        </div>

    </div>

</div>