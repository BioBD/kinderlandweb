
<script>
    $(function () {
        $("#accordion").accordion({
            collapsible: true
        });
        $("#accordion").accordion('option', 'active', 2);

    });
</script>
<div class="col-lg-3  left-action-bar">
    <div id="accordion">
        <h3>Usuários</h3>
        <div>
        </div>
        <h3>Campanha de sócios</h3>
        <div>
        </div>
        <h3>Colônia</h3>
        <div>
        	<a href="<?= $this->config->item('url_link'); ?>admin/camp">
                Administração
            </a>
            <br />
            <a href="<?= $this->config->item('url_link'); ?>reports/camp_reports"> Relatórios</a>
            <br />
            <a href="<?= $this->config->item('url_link'); ?>summercamps/roomDisposal" target="_blank"> 
                Quartos
            </a> <br />
             <a href="<?= $this->config->item('url_link'); ?>summercamps/medicalFileStaff" target="_blank"> 
                Minha Ficha Médica
            </a>  
        </div>
    </div>
</div>