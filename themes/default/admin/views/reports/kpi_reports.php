<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php
$v = "";

if ($this->input->post('supplier')) {
    $v .= "&supplier=" . $this->input->post('supplier');
}
if ($this->input->post('start_date')) {
    $v .= "&start_date=" . $this->input->post('start_date');
}
if ($this->input->post('end_date')) {
    $v .= "&end_date=" . $this->input->post('end_date');
}
?>
<style>
    #KpiRData td:nth-child(3) { text-align: center; }
    #KpiRData td:nth-child(4) { text-align: center; }
    #KpiRData td:nth-child(5) { text-align: center; }
    #KpiRData td:nth-child(6) { font-weight: bold; text-align: center; }
</style>
<script>
    $(document).ready(function () {
        /*
        oTable = $('#PrRDataEND').dataTable({
            "aaSorting": [[0, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= admin_url('reports/getBrandsReport/?v=1' . $v) ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": [null, {"mRender": decimalFormat, "bSearchable": false}, {"mRender": decimalFormat, "bSearchable": false}, {"mRender": currencyFormat, "bSearchable": false}, {"mRender": currencyFormat, "bSearchable": false}, {"mRender": currencyFormat, "bSearchable": false}],
            "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                var pQty = 0, sQty = 0, pAmt = 0, sAmt = 0, pl = 0;
                for (var i = 0; i < aaData.length; i++) {
                    pQty += parseFloat(aaData[aiDisplay[i]][1]);
                    sQty += parseFloat(aaData[aiDisplay[i]][2]);
                    pAmt += parseFloat(aaData[aiDisplay[i]][3]);
                    sAmt += parseFloat(aaData[aiDisplay[i]][4]);
                    pl += parseFloat(aaData[aiDisplay[i]][5]);
                }
                var nCells = nRow.getElementsByTagName('th');
                nCells[1].innerHTML = decimalFormat(parseFloat(pQty));
                nCells[2].innerHTML = decimalFormat(parseFloat(sQty));
                nCells[3].innerHTML = currencyFormat(parseFloat(pAmt));
                nCells[4].innerHTML = currencyFormat(parseFloat(sAmt));
                nCells[5].innerHTML = currencyFormat(parseFloat(pl));
            }
        }).fnSetFilteringDelay().dtFilter([
            {column_number: 0, filter_default_label: "[<?=lang('brand');?>]", filter_type: "text", data: []},
        ], "footer");
        */
    });
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#form').hide();
        $('.toggle_down').click(function () {
            $("#form").slideDown();
            return false;
        });
        $('.toggle_up').click(function () {
            $("#form").slideUp();
            return false;
        });

    });
</script>

<div class="box">
    <div class="box-header">
        <h2 class="blue">
            <i class="fa-fw fa fa-cubes"></i><?= lang('KPI_Report'); ?> <?php
            if ($this->input->post('start_date')) {
                echo "From " . $this->input->post('start_date') . " to " . $this->input->post('end_date');
            }
            ?>
        </h2>

        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a href="#" class="toggle_up tip" title="<?= lang('hide_form') ?>">
                        <i class="icon fa fa-toggle-up"></i>
                    </a>
                </li>
                <li class="dropdown">
                    <a href="#" class="toggle_down tip" title="<?= lang('show_form') ?>">
                        <i class="icon fa fa-toggle-down"></i>
                    </a>
                </li>
            </ul>
        </div>
        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a href="#" id="xls" class="tip" title="<?= lang('download_xls') ?>">
                        <i class="icon fa fa-file-excel-o"></i>
                    </a>
                </li>
                <li class="dropdown">
                    <a href="#" id="image" class="tip" title="<?= lang('save_image') ?>">
                        <i class="icon fa fa-file-picture-o"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?= lang('customize_report'); ?></p>

                <div id="form">

                    <?php echo admin_form_open("kpireports"); ?>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("Supplier", "posupplier"); ?>
                                <?php
                                $wh[''] = '';
                                $wh[''] = 'Select Supplier';
                                foreach ($suppliers as $supplier) {
                                    $wh[$supplier->supplier_id] = $supplier->supplier;
                                }
                                echo form_dropdown('supplier', $wh, (isset($_POST['supplier']) ? $_POST['supplier'] : ''), 'id="posupplier" class="form-control input-tip select" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("supplier") . '" style="width:100%;" ');
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
                    <table id="KpiRData"
                           class="table table-striped table-bordered table-condensed table-hover dfTable reports-table"
                           style="margin-bottom:5px;">
                        <thead>
                        <tr class="active">
                            <th><?= lang("Supplier_Name"); ?></th>
                            <th><?= lang("PO_Ref_No"); ?></th>
                            <?php
                            $all_kpi_list = $this->kpi_model->getAllKpi();
                            foreach ($all_kpi_list as $kpi) {
                                ?>
                                <th><?= lang("$kpi->name"); ?></th>
                            <?php } ?>
                            <th><?= lang("Average_Rating"); ?></th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php
                        if ($all_kpi_reports) {
                            foreach ($all_kpi_reports as $kpi_report) {
                                ?>
                                <tr>
                                    <td><?= $kpi_report->supplier ?></td>
                                    <td><?= $kpi_report->reference_no ?></td>
                                    <?php
                                    $all_kpi_list = $this->kpi_model->getAllKpi();
                                    foreach ($all_kpi_list as $kpi) {
                                        $kpi_by_id = $this->kpi_model->getKpiValueByID($kpi_report->tbl_purchase_id, $kpi->id);
                                        ?>
                                        <td><?= $kpi_by_id->value ?></td>
                                    <?php } ?>
                                    <td><?= $kpi_report->avg_kpi ?></td>
                                </tr>
                            <?php }
                        } else { ?>
                        <?php } ?>
                        </tbody>
                        <tfoot class="dtFilter">
                        <tr class="active">
                            <th></th>
                            <th></th>
                            <?php
                            $all_kpi_list = $this->kpi_model->getAllKpi();
                            foreach ($all_kpi_list as $kpi) {
                                ?>
                                <th><?= lang("$kpi->name"); ?></th>
                            <?php } ?>
                            <th><?= lang("Average_Rating"); ?></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="<?= $assets ?>js/html2canvas.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#pdf').click(function (event) {
            event.preventDefault();
            window.location.href = "<?=admin_url('reports/getBrandsReport/pdf/?v=1' . $v)?>";
            return false;
        });
        $('#xls').click(function (event) {
            event.preventDefault();
            window.location.href = "<?=admin_url('reports/getBrandsReport/0/xls/?v=1' . $v)?>";
            return false;
        });
        $('#image').click(function (event) {
            event.preventDefault();
            html2canvas($('.box'), {
                onrendered: function (canvas) {
                    openImg(canvas.toDataURL());
                }
            });
            return false;
        });
    });
</script>
<script>
    $(document).ready(function () {
        oTable = $('#KpiRData').dataTable({
            "aaSorting": [[0, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            "bAutoWidth": false,
            "aoColumns": [
                { sWidth: '30%' },
                { sWidth: '5%' },
                { sWidth: '10%' },
                { sWidth: '10%' },
                { sWidth: '10%' },
                { sWidth: '10%' },
            ],
            "defaultContent": "-",
        }).fnSetFilteringDelay().dtFilter([
            {column_number: 0, filter_default_label: "[<?=lang('Supplier_Name');?>]", filter_type: "text", data: []},
            {column_number: 1, filter_default_label: "[<?=lang('PO_Ref_No');?>]", filter_type: "text", data: []},
        ], "footer");
    });
</script>