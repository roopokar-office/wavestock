<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog modal-lg no-modal-header">
    <div class="modal-content">
        <div class="modal-body">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                <i class="fa fa-2x">&times;</i>
            </button>
            <button type="button" class="btn btn-xs btn-default no-print pull-right" style="margin-right:15px;" onclick="window.print();">
                <i class="fa fa-print"></i> <?= lang('print'); ?>
            </button>
            <button type="button" class="btn btn-xs btn-default no-print pull-right" id="table-export" data-filename="Sales_Details" style="margin-right:15px;">
                <i class="fa fa-file-excel-o"></i> <?= lang('excel'); ?>
            </button>
            <br/>
            <br/>
            <?php if ($logo) { ?>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="text-right" style="margin-bottom:20px;">
                            <img src="<?= base_url() . 'assets/uploads/logos/' . $Settings->logo; ?>"
                                 alt="<?= $Settings->site_name; ?>">
                        </div>
                    </div>
                </div>
            <?php } ?>
            <div class="row">
                <div class="col-xs-12">
                    <h1 class="text-uppercase">Bill</h1>
                </div>
            </div>
            <style>
                .bordered-box {
                    border: 1px solid;
                    padding: 5px;
                    min-height: 160px;
                }
                .company {
                    position: fixed;
                    bottom: 55px;
                }
                .buttons {
                    margin-top: 55px;
                }
                @media print {
                    div {
                        display: block;
                    }
                    .table thead tr th {
                        background-color: #000 !important;
                        color: #fff !important;
                        border-top: 1px solid #000 !important;
                    }
                    .table-bordered tbody tr td {
                        border: solid #000 !important;
                        border-width: 1px 1px 1px 1px !important;
                    }
                    .table-bordered th,
                    .table-bordered td {
                        border-collapse: unset !important;
                        border: 1px solid #000 !important;
                    }
                    .company {
                        display: contents;
                        position: fixed;
                        height: 100px;
                        bottom: 0;
                        width: 100%;
                        margin-top: 50px;
                    }
                }
            </style>

            <div class="row">
                <div class="col-xs-6">
                    <div class="bordered-box">
                        <h2 style="margin-top:10px;"><?= $customer->company && $customer->company != '-' ? $customer->company : $customer->name; ?></h2>
                        <?= $customer->company && $customer->company != '-' ? "" : "Attn: " . $customer->name ?>
                        <?php
                        echo $customer->address . "<br>"
                            . $customer->city . " " . $customer->postal_code . " " . $customer->state . "<br>"
                            . $customer->country . "<br />"
                            . $customer->phone . "<br />"
                            . $customer->email;

                        echo "<p>";

                        if ($customer->vat_no != "-" && $customer->vat_no != "") {
                            echo "<br>" . lang("vat_no") . ": " . $customer->vat_no;
                        }
                        if ($customer->gst_no != "-" && $customer->gst_no != "") {
                            echo "<br>" . lang("gst_no") . ": " . $customer->gst_no;
                        }
                        if ($customer->cf1 != "-" && $customer->cf1 != "") {
                            echo "<br>" . lang("ccf1") . ": " . $customer->cf1;
                        }
                        if ($customer->cf2 != "-" && $customer->cf2 != "") {
                            echo "<br>" . lang("ccf2") . ": " . $customer->cf2;
                        }
                        if ($customer->cf3 != "-" && $customer->cf3 != "") {
                            echo "<br>" . lang("ccf3") . ": " . $customer->cf3;
                        }
                        if ($customer->cf4 != "-" && $customer->cf4 != "") {
                            echo "<br>" . lang("ccf4") . ": " . $customer->cf4;
                        }
                        if ($customer->cf5 != "-" && $customer->cf5 != "") {
                            echo "<br>" . lang("ccf5") . ": " . $customer->cf5;
                        }
                        if ($customer->cf6 != "-" && $customer->cf6 != "") {
                            echo "<br>" . lang("ccf6") . ": " . $customer->cf6;
                        }

                        echo "</p>";
                        ?>
                    </div>
                </div>
                <div class="col-xs-6">
                    <div class="bordered-box">
                        <?= lang("Order_Number"); ?>: <?= rand(); ?><br>
                        <?= lang("Order_Date"); ?>: <?= $this->sma->hrld($inv->date); ?><br>
                        <?= lang("ref"); ?>: <?= $inv->reference_no; ?><br>
                        <?php if (!empty($inv->return_purchase_ref)) {
                            echo lang("return_ref") . ': ' . $inv->return_purchase_ref;
                            if ($inv->return_id) {
                                echo ' <a data-target="#myModal2" data-toggle="modal" href="' . admin_url('purchases/modal_view/' . $inv->return_id) . '"><i class="fa fa-external-link no-print"></i></a><br>';
                            } else {
                                echo '<br>';
                            }
                        } ?>
                    </div>
                </div>
            </div>
            <br/>
            <br/>
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped print-table order-table" id="tblData">

                    <thead>

                    <tr>
                        <th><?= lang("no."); ?></th>
                        <th><?= lang("description"); ?></th>
                        <?php if ($Settings->indian_gst) { ?>
                            <th><?= lang("hsn_code"); ?></th>
                        <?php } ?>
                        <th><?= lang("quantity"); ?></th>
                        <th><?= lang("unit_price"); ?></th>
                        <?php
                        if ($Settings->tax1 && $inv->product_tax > 0) {
                            echo '<th>' . lang("tax") . '</th>';
                        }
                        if ($Settings->product_discount && $inv->product_discount != 0) {
                            echo '<th>' . lang("discount") . '</th>';
                        }
                        ?>
                        <th><?= lang("subtotal"); ?></th>
                    </tr>

                    </thead>

                    <tbody>

                    <?php
                    $CI =& get_instance();
                    $r = 1;
                    foreach ($rows as $row):
                        ?>
                        <tr>
                            <td style="text-align:center; width:40px; vertical-align:middle;"><?= $r; ?></td>
                            <td style="vertical-align:middle;">
                                <?= $CI->get_brand_name($row->product_id).' '.$row->product_name;
                                if($row->product_type=='combo') {
                                    $CI->get_combopoduct_name($row->product_id) ;
                                }
                                echo ($row->variant ? ' (' . $row->variant . ')' : ''); ?>
                                <?= $row->second_name ? '<br>' . $row->second_name : ''; ?>
                                <?= $row->details ? '<br>' . $row->details : ''; ?>
                                <?= $row->serial_no ? '<br>' . $row->serial_no : ''; ?>
                            </td>
                            <?php if ($Settings->indian_gst) { ?>
                                <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $row->hsn_code; ?></td>
                            <?php } ?>
                            <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $this->sma->formatQuantity($row->unit_quantity).' '.$row->product_unit_code; ?></td>
                            <td style="text-align:right; width:100px;"><?= $this->sma->formatMoney($row->unit_price); ?>
                                <?php
                                if ($row->discount != 0) {
                                    echo '<br><small>(Original price: '.($row->item_discount+$row->unit_price).')</small>';
                                }
                                ?>
                            </td>
                            <?php
                            if ($Settings->tax1 && $inv->product_tax > 0) {
                                echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . ($row->item_tax != 0 ? '<small>('.($Settings->indian_gst ? $row->tax : $row->tax_code).')</small>' : '') . ' ' . $this->sma->formatMoney($row->item_tax) . '</td>';
                            }
                            if ($Settings->product_discount && $inv->product_discount != 0) {
                                echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . ($row->discount != 0 ? '<small>(' . $row->discount . ')</small> ' : '') . $this->sma->formatMoney($row->item_discount) . '</td>';
                            }
                            ?>
                            <td style="text-align:right; width:120px;"><?= $this->sma->formatMoney($row->subtotal); ?></td>
                        </tr>
                        <?php
                        $r++;
                    endforeach;
                    if ($return_rows) {
                        echo '<tr class="warning"><td colspan="100%" class="no-border"><strong>'.lang('returned_items').'</strong></td></tr>';
                        foreach ($return_rows as $row):
                            ?>
                            <tr class="warning">
                                <td style="text-align:center; width:40px; vertical-align:middle;"><?= $r; ?></td>
                                <td style="vertical-align:middle;">
                                    <?= $row->product_name . ($row->variant ? ' (' . $row->variant . ')' : ''); ?>
                                    <?= $row->second_name ? '<br>' . $row->second_name : ''; ?>
                                    <?= $row->details ? '<br>' . $row->details : ''; ?>
                                    <?= $row->serial_no ? '<br>' . $row->serial_no : ''; ?>
                                </td>
                                <?php if ($Settings->indian_gst) { ?>
                                    <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $row->hsn_code; ?></td>
                                <?php } ?>
                                <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $this->sma->formatQuantity($row->quantity).' '.$row->product_unit_code; ?></td>
                                <td style="text-align:right; width:120px; padding-right:10px;"><?= $this->sma->formatMoney($row->unit_price); ?>
                                    <?php
                                    if ($row->discount != 0) {
                                        echo '<br><small>(Original price: '.($row->item_discount+$row->unit_price).')</small>';
                                    }
                                    ?>
                                </td>
                                <?php
                                if ($Settings->tax1 && $inv->product_tax > 0) {
                                    echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . ($row->item_tax != 0 ? '<small>('.($Settings->indian_gst ? $row->tax : $row->tax_code).')</small>' : '') . ' ' . $this->sma->formatMoney($row->item_tax) . '</td>';
                                }
                                if ($Settings->product_discount && $inv->product_discount != 0) {
                                    echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . ($row->discount != 0 ? '<small>(' . $row->discount . ')</small> ' : '') . $this->sma->formatMoney($row->item_discount) . '</td>';
                                }
                                ?>
                                <td style="text-align:right; width:120px;"><?= $this->sma->formatMoney($row->subtotal); ?></td>
                            </tr>
                            <?php
                            $r++;
                        endforeach;
                    }
                    ?>
                    </tbody>
                    <tfoot>
                    <?php
                    $col = $Settings->indian_gst ? 5 : 4;
                    if ($Settings->product_discount && $inv->product_discount != 0) {
                        $col++;
                    }
                    if ($Settings->tax1 && $inv->product_tax > 0) {
                        $col++;
                    }
                    if ($Settings->product_discount && $inv->product_discount != 0 && $Settings->tax1 && $inv->product_tax > 0) {
                        $tcol = $col - 2;
                    } elseif ($Settings->product_discount && $inv->product_discount != 0) {
                        $tcol = $col - 1;
                    } elseif ($Settings->tax1 && $inv->product_tax > 0) {
                        $tcol = $col - 1;
                    } else {
                        $tcol = $col;
                    }
                    ?>
                    <?php if ($inv->grand_total != $inv->total) { ?>
                        <tr>
                            <td colspan="<?= $tcol; ?>"
                                style="text-align:right; padding-right:10px;"><?= lang("total"); ?>
                                (<?= $default_currency->code; ?>)
                            </td>
                            <?php
                            if ($Settings->tax1 && $inv->product_tax > 0) {
                                echo '<td style="text-align:right;">' . $this->sma->formatMoney($return_sale ? ($inv->product_tax+$return_sale->product_tax) : $inv->product_tax) . '</td>';
                            }
                            if ($Settings->product_discount && $inv->product_discount != 0) {
                                echo '<td style="text-align:right;">' . $this->sma->formatMoney($return_sale ? ($inv->product_discount+$return_sale->product_discount) : $inv->product_discount) . '</td>';
                            }
                            ?>
                            <td style="text-align:right; padding-right:10px;"><?= $this->sma->formatMoney($return_sale ? (($inv->total + $inv->product_tax)+($return_sale->total + $return_sale->product_tax)) : ($inv->total + $inv->product_tax)); ?></td>
                        </tr>
                    <?php } ?>
                    <?php
                    if ($return_sale) {
                        echo '<tr><td colspan="' . $col . '" style="text-align:right; padding-right:10px;;">' . lang("return_total") . ' (' . $default_currency->code . ')</td><td style="text-align:right; padding-right:10px;">' . $this->sma->formatMoney($return_sale->grand_total) . '</td></tr>';
                    }
                    if ($inv->surcharge != 0) {
                        echo '<tr><td colspan="' . $col . '" style="text-align:right; padding-right:10px;;">' . lang("return_surcharge") . ' (' . $default_currency->code . ')</td><td style="text-align:right; padding-right:10px;">' . $this->sma->formatMoney($inv->surcharge) . '</td></tr>';
                    }
                    ?>

                    <?php if ($Settings->indian_gst) {
                        if ($inv->cgst > 0) {
                            $cgst = $return_sale ? $inv->cgst + $return_sale->cgst : $inv->cgst;
                            echo '<tr><td colspan="' . $col . '" class="text-right">' . lang('cgst') . ' (' . $default_currency->code . ')</td><td class="text-right">' . ( $Settings->format_gst ? $this->sma->formatMoney($cgst) : $cgst) . '</td></tr>';
                        }
                        if ($inv->sgst > 0) {
                            $sgst = $return_sale ? $inv->sgst + $return_sale->sgst : $inv->sgst;
                            echo '<tr><td colspan="' . $col . '" class="text-right">' . lang('sgst') . ' (' . $default_currency->code . ')</td><td class="text-right">' . ( $Settings->format_gst ? $this->sma->formatMoney($sgst) : $sgst) . '</td></tr>';
                        }
                        if ($inv->igst > 0) {
                            $igst = $return_sale ? $inv->igst + $return_sale->igst : $inv->igst;
                            echo '<tr><td colspan="' . $col . '" class="text-right">' . lang('igst') . ' (' . $default_currency->code . ')</td><td class="text-right">' . ( $Settings->format_gst ? $this->sma->formatMoney($igst) : $igst) . '</td></tr>';
                        }
                    } ?>

                    <?php if ($inv->order_discount != 0) {
                        echo '<tr><td colspan="' . $col . '" style="text-align:right; padding-right:10px;;">' . lang("order_discount") . ' (' . $default_currency->code . ')</td><td style="text-align:right; padding-right:10px;">'.($inv->order_discount_id ? '<small>('.$inv->order_discount_id.')</small> ' : '') . $this->sma->formatMoney($return_sale ? ($inv->order_discount+$return_sale->order_discount) : $inv->order_discount) . '</td></tr>';
                    }
                    ?>
                    <?php if ($Settings->tax2 && $inv->order_tax != 0) {
                        echo '<tr><td colspan="' . $col . '" style="text-align:right; padding-right:10px;">' . lang("order_tax") . ' (' . $default_currency->code . ')</td><td style="text-align:right; padding-right:10px;">' . $this->sma->formatMoney($return_sale ? ($inv->order_tax+$return_sale->order_tax) : $inv->order_tax) . '</td></tr>';
                    }
                    ?>
                    <?php if ($inv->shipping != 0) {
                        echo '<tr><td colspan="' . $col . '" style="text-align:right; padding-right:10px;;">' . lang("shipping") . ' (' . $default_currency->code . ')</td><td style="text-align:right; padding-right:10px;">' . $this->sma->formatMoney($inv->shipping) . '</td></tr>';
                    }
                    ?>
                    <tr>
                        <td colspan="<?= $col; ?>"
                            style="text-align:right; font-weight:bold;"><?= lang("total_amount"); ?>
                            (<?= $default_currency->code; ?>)
                        </td>
                        <td style="text-align:right; padding-right:10px; font-weight:bold;"><?= $this->sma->formatMoney($return_sale ? ($inv->grand_total+$return_sale->grand_total) : $inv->grand_total); ?></td>
                    </tr>
                    <?php if($inv->payment_status!='pending'){ ?>
                        <tr>
                            <td colspan="<?= $col; ?>"
                                style="text-align:right; font-weight:bold;"><?= lang("paid"); ?>
                                (<?= $default_currency->code; ?>)
                            </td>
                            <td style="text-align:right; font-weight:bold;"><?= $this->sma->formatMoney($return_sale ? ($inv->paid+$return_sale->paid) : $inv->paid); ?></td>
                        </tr>
                        <?php $ash=$this->sma->formatMoney(($return_sale ? ($inv->grand_total+$return_sale->grand_total) : $inv->grand_total) - ($return_sale ? ($inv->paid+$return_sale->paid) : $inv->paid));
                        if($ash!='0.00'){
                            ?>
                            <tr>
                                <td colspan="<?= $col; ?>"
                                    style="text-align:right; font-weight:bold;"><?= lang("balance"); ?>
                                    (<?= $default_currency->code; ?>)
                                </td>
                                <td style="text-align:right; font-weight:bold;"><?= $ash; ?></td>
                            </tr>

                        <?php }
                    }
                    ?>

                    </tfoot>
                </table>
            </div>

            <?= $Settings->invoice_view > 0 ? $this->gst->summary($rows, $return_rows, ($return_sale ? $inv->product_tax+$return_sale->product_tax : $inv->product_tax)) : ''; ?>

            <div class="row">
                <div class="col-xs-12">
                    <?php
                    if ($inv->note || $inv->note != "") { ?>
                        <span class="bold"><?= lang("note"); ?>: </span>
                        <?= strip_tags($this->sma->decode_html($inv->note)); ?>
                        <br/>
                        <br/>
                        <?php
                    }
                    if ($inv->staff_note || $inv->staff_note != "") { ?>
                        <div class="well well-sm staff_note">
                            <span class="bold"><?= lang("staff_note"); ?>:</span>
                            <?= strip_tags($this->sma->decode_html($inv->staff_note)); ?>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-8">
                    Checked & received by<br/>
                    <br/>
                    Sig.: ……………………………………………<br/>
                    Name: …………………………………………<br/>
                    Designation & Seal: …………………………<br/>
                </div>
                <div class="col-xs-4 pull-right">
                    Delivered by<br/>
                    <br/>
                    <br/>
                    ………………………………………<br/>
                    (Name & Date)<br/>
                </div>
            </div>

            <?php if (!$Supplier || !$Customer) { ?>
                <div class="buttons">
                    <div class="btn-group btn-group-justified">
                        <div class="btn-group">
                            <a href="<?= admin_url('sales/add_payment/' . $inv->id) ?>" class="tip btn btn-primary" title="<?= lang('add_payment') ?>" data-toggle="modal" data-target="#myModal2">
                                <i class="fa fa-dollar"></i>
                                <span class="hidden-sm hidden-xs"><?= lang('payment') ?></span>
                            </a>
                        </div>
                        <div class="btn-group">
                            <a href="<?= admin_url('sales/add_delivery/' . $inv->id) ?>" class="tip btn btn-primary" title="<?= lang('add_delivery') ?>" data-toggle="modal" data-target="#myModal2">
                                <i class="fa fa-truck"></i>
                                <span class="hidden-sm hidden-xs"><?= lang('delivery') ?></span>
                            </a>
                        </div>
                        <?php if ($inv->attachment) { ?>
                            <div class="btn-group">
                                <a href="<?= admin_url('welcome/download/' . $inv->attachment) ?>" class="tip btn btn-primary" title="<?= lang('attachment') ?>">
                                    <i class="fa fa-chain"></i>
                                    <span class="hidden-sm hidden-xs"><?= lang('attachment') ?></span>
                                </a>
                            </div>
                        <?php } ?>
                        <div class="btn-group">
                            <a href="<?= admin_url('sales/email/' . $inv->id) ?>" data-toggle="modal" data-target="#myModal2" class="tip btn btn-primary" title="<?= lang('email') ?>">
                                <i class="fa fa-envelope-o"></i>
                                <span class="hidden-sm hidden-xs"><?= lang('email') ?></span>
                            </a>
                        </div>
                        <div class="btn-group">
                            <a href="<?= admin_url('sales/pdf/' . $inv->id) ?>" class="tip btn btn-primary" title="<?= lang('download_pdf') ?>">
                                <i class="fa fa-download"></i>
                                <span class="hidden-sm hidden-xs"><?= lang('pdf') ?></span>
                            </a>
                        </div>
                        <?php if ( ! $inv->sale_id) { ?>
                            <div class="btn-group">
                                <a href="<?= admin_url('sales/edit/' . $inv->id) ?>" class="tip btn btn-warning sledit" title="<?= lang('edit') ?>">
                                    <i class="fa fa-edit"></i>
                                    <span class="hidden-sm hidden-xs"><?= lang('edit') ?></span>
                                </a>
                            </div>
                            <div class="btn-group">
                                <a href="#" class="tip btn btn-danger bpo" title="<b><?= $this->lang->line("delete_sale") ?></b>"
                                   data-content="<div style='width:150px;'><p><?= lang('r_u_sure') ?></p><a class='btn btn-danger' href='<?= admin_url('sales/delete/' . $inv->id) ?>'><?= lang('i_m_sure') ?></a> <button class='btn bpo-close'><?= lang('no') ?></button></div>"
                                   data-html="true" data-placement="top">
                                    <i class="fa fa-trash-o"></i>
                                    <span class="hidden-sm hidden-xs"><?= lang('delete') ?></span>
                                </a>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
            <div class="company company-address">
                <b>Orogenic Office Solutions Limited</b><br/>
                S.R Tower (6th floor), Plot # 105, Road # 35, Sector # 7, Uttara C/A, Dhaka-1230 info@officeklick.com | www.officeklick.com
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?= $assets ?>js/xlsx.core.min.js"></script>
<script type="text/javascript" src="<?= $assets ?>js/FileSaver.min.js"></script>
<script type="text/javascript" src="<?= $assets ?>js/tableexport.min.js"></script>
<script type="text/javascript">
    $(document).ready( function() {
        $('.tip').tooltip();
        var excelButton = $('#table-export');
        var table = $('#tblData').tableExport({
            formats: ["xlsx"],
            filename: excelButton.attr('data-filename'),
            sheetname: excelButton.attr('data-filename'),
            exportButtons: false,
            htmlContent: true,
        });
        excelButton.click(function(){
            var exportData = table.getExportData();
            var xlsxData = exportData.tblData.xlsx; // Replace with the kind of file you want from the exportData
            table.export2file(xlsxData.data, xlsxData.mimeType, xlsxData.filename, xlsxData.fileExtension, xlsxData.merges, xlsxData.RTL, xlsxData.sheetname);
        });
    });
</script>
