<?php

if (file_exists(__DIR__.'/../../../core.php')) {
    include_once __DIR__.'/../../../core.php';
} else {
    include_once __DIR__.'/../../core.php';
}

$idriga = filter('idriga');

if (empty($idriga)) {
    $op = 'addriga';
    $button = '<i class="fa fa-plus"></i> '.tr('Aggiungi');

    // valori default
    $descrizione = '';
    $qta = 1;
    $um = 'ore';
    $idiva = setting('Iva predefinita');
    $prezzo_vendita = '0';
    $prezzo_acquisto = '0';
} else {
    $op = 'editriga';
    $button = '<i class="fa fa-edit"></i> '.tr('Modifica');

    // carico record da modificare
    $q = 'SELECT * FROM in_righe_tipiinterventi WHERE id='.prepare($idriga);
    $rsr = $dbo->fetchArray($q);

    $descrizione = $rsr[0]['descrizione'];
    $qta = $rsr[0]['qta'];
    $um = $rsr[0]['um'];
    $idiva = $rsr[0]['idiva'];
    $prezzo_vendita = $rsr[0]['prezzo_vendita'];
    $prezzo_acquisto = $rsr[0]['prezzo_acquisto'];

}

/*
    Form di inserimento
*/
echo '
<form id="add-righe" action="'.$rootdir.'/editor.php?id_module='.$id_module.'&id_record='.$id_record.'" method="post">
    <input type="hidden" name="op" value="'.$op.'">
    <input type="hidden" name="id_tipointervento" value="'.$id_record.'">
    <input type="hidden" name="idriga" value="'.$idriga.'">';

// Descrizione
echo '
    <div class="row">
        <div class="col-md-12">
            {[ "type": "textarea", "label": "'.tr('Descrizione').'", "id": "descrizione_riga", "name": "descrizione", "required": 1, "value": '.json_encode($descrizione).' ]}
        </div>
    </div>
    <br>';

// Quantità
echo '
    <div class="row">
        <div class="col-md-4">
            {[ "type": "number", "label": "'.tr('Q.tà').'", "name": "qta", "required": 1, "value": "'.$qta.'", "decimals": "qta" ]}
        </div>';

// Unità di misura
echo '
        <div class="col-md-4">
            {[ "type": "select", "label": "'.tr('Unità di misura').'", "icon-after": "add|'.Modules::get('Unità di misura')['id'].'", "name": "um", "value": "'.$um.'", "ajax-source": "misure" ]}
        </div>';

// Iva
echo '
        <div class="col-md-4">
            {[ "type": "select", "label": "'.tr('Iva').'", "name": "idiva", "required": 1, "value": "'.$idiva.'", "ajax-source": "iva" ]}
        </div>
    </div>';

// Prezzo di acquisto
echo '
    <div class="row">
        <div class="col-md-6">
            {[ "type": "number", "label": "'.tr('Prezzo di acquisto (un.)').'", "name": "prezzo_acquisto", "required": 1, "value": "'.$prezzo_acquisto.'", "icon-after": "&euro;" ]}
        </div>';

// Prezzo di vendita
echo '
        <div class="col-md-6">
            {[ "type": "number", "label": "'.tr('Prezzo di vendita (un.)').'", "name": "prezzo_vendita", "required": 1, "value": "'.$prezzo_vendita.'", "icon-after": "&euro;" ]}
        </div>
    </div>';

echo '
    <!-- PULSANTI -->
    <div class="row">
        <div class="col-md-12 text-right">
            <button type="submit" class="btn btn-primary pull-right">'.$button.'</button>
        </div>
    </div>
</form>';

?>

<script type="text/javascript">
    $(document).ready(function() {
	init();
        $('#add-righe').ajaxForm({
            success: function(){
                $('#modals > div').modal('hide');

                // Ricarico le righe
                $('#righe').load('<?php echo $module->fileurl('ajax_righe.php');?>?id_module=<?php echo $id_module; ?>&id_record=<?php echo $id_record; ?>');

                // Ricarico la tabella dei costi
                $('#costi').load('<?php echo $module->fileurl('ajax_righe.php');?>?id_module=<?php echo $id_module; ?>&id_record=<?php echo $id_record; ?>');
            }
        });
    });
</script>
