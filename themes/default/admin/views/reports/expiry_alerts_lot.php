<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
$v = "";

if ($this->input->post('warehouse')) {
    $v .= "&warehouse=" . $this->input->post('warehouse');
}
if ($this->input->post('start_date')) {
    $sdate=str_replace('/', '-', substr($this->input->post('start_date'), 0, -6));
    $v .= "&start_date=" . date('Y-m-d', strtotime($sdate));
}
if ($this->input->post('end_date')) {
    $edate=str_replace('/', '-', substr($this->input->post('end_date'), 0, -6));
    $v .= "&end_date=" . date('Y-m-d', strtotime($edate));
}

//echo "hello:".$v;
?>
<style type="text/css" media="screen">
    #PRData td:nth-child(7) {
        text-align: right;
    }
    <?php if($Owner || $Admin || $this->session->userdata('show_cost')) { ?>
    #PRData td:nth-child(9) {
        text-align: right;
    }
    <?php } if($Owner || $Admin || $this->session->userdata('show_price')) { ?>
    #PRData td:nth-child(8) {
        text-align: right;
    }
    <?php } ?>
</style>
<script>
    $(document).ready(function () {
        oTable = $('#CategoryTable').dataTable({
            "aaSorting": [[3, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('reports/getProductsExpiryfilter/?v=1'.$v) ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            }
        });
    });
</script>

<div class="box">
    <div class="box-header">
        <h2 class="blue"><i
                class="fa-fw fa fa-barcode"></i><?= lang('Expiry Products List'); ?>
                <?php
            if ($this->input->post('start_date')) {
                echo "From " . $this->input->post('start_date') . " to " . $this->input->post('end_date');
            }
            ?>
        </h2>

        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <i class="icon fa fa-tasks tip" data-placement="left" title="<?= lang("actions") ?>"></i>
                    </a>
                    <ul class="dropdown-menu pull-right tasks-menus" role="menu" aria-labelledby="dLabel">
                        
                        <li>
                            <a href="#" id="excel" data-action="export_excel">
                                <i class="fa fa-file-excel-o"></i> <?= lang('export_to_excel') ?>
                            </a>
                        </li>
                       
                    </ul>
                </li>
                
            </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
                <p class="introtext"><?= lang('list_results'); ?></p>

                <div id="form">

                    <?php echo admin_form_open("reports/expiry_alerts_lot"); ?>
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="warehouse"><?= lang("warehouse"); ?></label>
                                <?php
                                $wh[""] = lang('select').' '.lang('warehouse');
                                foreach ($warehouses as $warehouse) {
                                    $wh[$warehouse->id] = $warehouse->name;
                                }
                                echo form_dropdown('warehouse', $wh, (isset($_POST['warehouse']) ? $_POST['warehouse'] : ""), 'class="form-control" id="warehouse" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("warehouse") . '"');
                                ?>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang("start_date", "start_date"); ?>
                                <?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ""), 'class="form-control datetime" id="start_date"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang("end_date", "end_date"); ?>
                                <?php echo form_input('end_date', (isset($_POST['end_date']) ? $_POST['end_date'] : ""), 'class="form-control datetime" id="end_date"'); ?>
                            </div>
                        </div>                        

                    </div>
                    <div class="form-group">
                        <div
                            class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'class="btn btn-primary"'); ?> </div>
                    </div>
                    <?php echo form_close(); ?>

                </div>

                <div class="clearfix"></div>

                <div class="table-responsive">
                    <table id="CategoryTable" class="table table-bordered table-hover table-striped reports-table">
                        <thead>
                            <tr>
                                
                                <th>
                                    <?= lang("product_code"); ?>
                                </th>                                
                                <th><?= lang("product_name"); ?></th>
                                <th><?= lang("Lot_No"); ?></th>
                                <th><?= lang("warehouse"); ?></th>
                                <th><?= lang("expiry"); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="7" class="dataTables_empty">
                                    <?= lang('loading_data_from_server') ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
