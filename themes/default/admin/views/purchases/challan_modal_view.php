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
                    <h1 class="text-uppercase">Delivery Challan</h1>
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
                        display: contents !important;
                        position: absolute !important;
                        left: 0 !important;
                        bottom: -100px !important;
                        width: 100% !important;
                        background-color: #000 !important;
                    }
                }
            </style>
            <div class="row">
                <div class="col-xs-6">
                    <div class="bordered-box">
                        <h2 style="margin-top:10px;"><?= $supplier->company && $supplier->company != '-' ? $supplier->company : $supplier->name; ?></h2>
                        <?= $supplier->company && $supplier->company != '-' ? "" : "Attn: " . $supplier->name ?>

                        <?php
                        echo $supplier->address . "<br />"
                            . $supplier->city . " " . $supplier->postal_code . " " . $supplier->state . "<br />"
                            . $supplier->country . "<br />"
                            . $supplier->phone . "<br />"
                            . $supplier->email;

                        echo "<p>";

                        if ($supplier->vat_no != "-" && $supplier->vat_no != "") {
                            echo "<br>" . lang("vat_no") . ": " . $supplier->vat_no;
                        }
                        if ($supplier->gst_no != "-" && $supplier->gst_no != "") {
                            echo "<br>" . lang("gst_no") . ": " . $supplier->gst_no;
                        }
                        if ($supplier->cf1 != "-" && $supplier->cf1 != "") {
                            echo "<br>" . lang("scf1") . ": " . $supplier->cf1;
                        }
                        if ($supplier->cf2 != "-" && $supplier->cf2 != "") {
                            echo "<br>" . lang("scf2") . ": " . $supplier->cf2;
                        }
                        if ($supplier->cf3 != "-" && $supplier->cf3 != "") {
                            echo "<br>" . lang("scf3") . ": " . $supplier->cf3;
                        }
                        if ($supplier->cf4 != "-" && $supplier->cf4 != "") {
                            echo "<br>" . lang("scf4") . ": " . $supplier->cf4;
                        }
                        if ($supplier->cf5 != "-" && $supplier->cf5 != "") {
                            echo "<br>" . lang("scf5") . ": " . $supplier->cf5;
                        }
                        if ($supplier->cf6 != "-" && $supplier->cf6 != "") {
                            echo "<br>" . lang("scf6") . ": " . $supplier->cf6;
                        }

                        echo "</p>";
                        ?>
                    </div>
                </div>
                <div class="col-xs-6">
                    <div class="bordered-box">
                        <?= lang("Challan No. "); ?>: <?= rand(); ?><br>
                        <?= lang("Challan_Date"); ?>: <?= $this->sma->hrld($inv->date); ?><br>
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
                <table class="table table-bordered table-hover table-striped print-table order-table">
                    <thead>
                    <tr>
                        <th><?= lang("no."); ?></th>
                        <th class="test"><?= lang("Name of the Products"); ?></th>
                        <?php if ($Settings->indian_gst) { ?>
                            <th><?= lang("hsn_code"); ?></th>
                        <?php } ?>
                        <th><?= lang("UOM"); ?></th>
                        <th class="test"><?= lang("quantity"); ?></th>
                        <?php
                        if ($inv->status == 'partial') {
                            echo '<th>' . lang("received") . '</th>';
                        }
                        ?>
                        <?php
                        if ($Settings->tax1 && $inv->product_tax > 0) {
                            echo '<th>' . lang("tax") . '</th>';
                        }
                        if ($Settings->product_discount && $inv->product_discount != 0) {
                            echo '<th>' . lang("discount") . '</th>';
                        }
                        ?>
                    </tr>

                    </thead>

                    <tbody>

                    <?php $r = 1;
                    $tax_summary = array();
                    foreach ($rows as $row):
                        ?>
                        <tr>
                            <td style="text-align:center; width:40px; vertical-align:middle;"><?= $r; ?></td>
                            <td style="vertical-align:middle;">
                                <?= $row->product_code . ' - ' . $row->product_name . ($row->variant ? ' (' . $row->variant . ')' : ''); ?>
                                <?= $row->second_name ? '<br>' . $row->second_name : ''; ?>
                                <?= $row->supplier_part_no ? '<br>' . lang('supplier_part_no') . ': ' . $row->supplier_part_no : ''; ?>
                                <?= $row->details ? '<br>' . $row->details : ''; ?>
                                <?= ($row->expiry && $row->expiry != '0000-00-00') ? '<br>' . lang('expiry') . ': ' . $this->sma->hrsd($row->expiry) : ''; ?>
                            </td>
                            <?php if ($Settings->indian_gst) { ?>
                                <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $row->hsn_code; ?></td>
                            <?php } ?>
                            <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $row->product_unit_code; ?></td>
                            <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $this->sma->formatQuantity($row->unit_quantity); ?></td>
                            <?php
                            if ($inv->status == 'partial') {
                                echo '<td style="text-align:center;vertical-align:middle;width:80px;">' . $this->sma->formatQuantity($row->quantity_received) . ' ' . $row->product_unit_code . '</td>';
                            }
                            ?>
                            <?php
                            if ($Settings->tax1 && $inv->product_tax > 0) {
                                echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . ($row->item_tax != 0 ? '<small>(' . ($Settings->indian_gst ? $row->tax : $row->tax_code) . ')</small>' : '') . ' ' . $this->sma->formatMoney($row->item_tax) . '</td>';
                            }
                            if ($Settings->product_discount && $inv->product_discount != 0) {
                                echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . ($row->discount != 0 ? '<small>(' . $row->discount . ')</small> ' : '') . $this->sma->formatMoney($row->item_discount) . '</td>';
                            }
                            ?>
                        </tr>
                        <?php
                        $r++;
                    endforeach;
                    if ($return_rows) {
                        echo '<tr class="warning"><td colspan="100%" class="no-border"><strong>' . lang('returned_items') . '</strong></td></tr>';
                        foreach ($return_rows as $row):
                            ?>
                            <tr class="warning">
                                <td style="text-align:center; width:40px; vertical-align:middle;"><?= $r; ?></td>
                                <td style="vertical-align:middle;">
                                    <?= $row->product_code . ' - ' . $row->product_name . ($row->variant ? ' (' . $row->variant . ')' : ''); ?>
                                    <?= $row->second_name ? '<br>' . $row->second_name : ''; ?>
                                    <?= $row->supplier_part_no ? '<br>' . lang('supplier_part_no') . ': ' . $row->supplier_part_no : ''; ?>
                                    <?= $row->details ? '<br>' . $row->details : ''; ?>
                                    <?= ($row->expiry && $row->expiry != '0000-00-00') ? '<br>' . lang('expiry') . ': ' . $this->sma->hrsd($row->expiry) : ''; ?>
                                </td>
                                <?php if ($Settings->indian_gst) { ?>
                                    <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $row->hsn_code; ?></td>
                                <?php } ?>
                                <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $row->product_unit_code; ?></td>
                                <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $this->sma->formatQuantity($row->unit_quantity); ?></td>
                                <?php
                                if ($inv->status == 'partial') {
                                    echo '<td style="text-align:center;vertical-align:middle;width:80px;">' . $this->sma->formatQuantity($row->quantity_received) . ' ' . $row->product_unit_code . '</td>';
                                }
                                ?>
                                <?php
                                if ($Settings->tax1 && $inv->product_tax > 0) {
                                    echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . ($row->item_tax != 0 ? '<small>(' . ($Settings->indian_gst ? $row->tax : $row->tax_code) . ')</small>' : '') . ' ' . $this->sma->formatMoney($row->item_tax) . '</td>';
                                }
                                if ($Settings->product_discount && $inv->product_discount != 0) {
                                    echo '<td style="width: 100px; text-align:right; vertical-align:middle;">' . ($row->discount != 0 ? '<small>(' . $row->discount . ')</small> ' : '') . $this->sma->formatMoney($row->item_discount) . '</td>';
                                }
                                ?>
                            </tr>
                            <?php
                            $r++;
                        endforeach;
                    }
                    ?>
                    </tbody>
                    <tfoot>

                    </tfoot>
                </table>
            </div>

            <?= $Settings->invoice_view > 0 ? $this->gst->summary($rows, $return_rows, ($return_purchase ? $inv->product_tax + $return_purchase->product_tax : $inv->product_tax), true) : ''; ?>

            <div class="row">
                <div class="col-xs-12">
                    <?php
                    if ($inv->note || $inv->note != "") { ?>
                        <div class="well well-sm">
                            <p class="bold"><?= lang("note"); ?>:</p>
                            <div><?= $this->sma->decode_html($inv->note); ?></div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <br/>
            <p>Note: The above materials have been received in good conditions. No claims of any sort would be accepted after the delivery of goods.</p>
            <br/>
            <div class="row">
                <div class="col-xs-8">
                    Checked & received by<br/>
                    <br/>
                    Sig.: ……………………………………………<br/>
                    Name: …………………………………………<br/>
                    Designation & Seal: …………………………<br/>
                    <br/>
                    <br/>
                    ………………………………………<br/>
                    (Name & Date)<br/>
                    <br/>
                    Contact No.: ………………………………….<br/>
                </div>
                <div class="col-xs-4">
                    Delivered by<br/>
                    <br/>
                    <br/>
                    ………………………………………<br/>
                    (Name & Date)<br/>
                </div>
            </div>
            <?php if (!$Supplier || !$Customer) { ?>
                <div class="buttons">
                    <?php if ($inv->attachment) { ?>
                        <div class="btn-group">
                            <a href="<?= admin_url('welcome/download/' . $inv->attachment) ?>" class="tip btn btn-primary" title="<?= lang('attachment') ?>">
                                <i class="fa fa-chain"></i>
                                <span class="hidden-sm hidden-xs"><?= lang('attachment') ?></span>
                            </a>
                        </div>
                    <?php } ?>
                    <div class="btn-group btn-group-justified">
                        <div class="btn-group">
                            <a href="<?= admin_url('purchases/add_payment/' . $inv->id) ?>" data-toggle="modal" data-target="#myModal2" class="tip btn btn-primary" title="<?= lang('add_payment') ?>">
                                <i class="fa fa-dollar"></i>
                                <span class="hidden-sm hidden-xs"><?= lang('add_payment') ?></span>
                            </a>
                        </div>
                        <div class="btn-group">
                            <a href="<?= admin_url('purchases/email/' . $inv->id) ?>" data-toggle="modal" data-target="#myModal2" class="tip btn btn-primary" title="<?= lang('email') ?>">
                                <i class="fa fa-envelope-o"></i>
                                <span class="hidden-sm hidden-xs"><?= lang('email') ?></span>
                            </a>
                        </div>
                        <div class="btn-group">
                            <a href="<?= admin_url('purchases/pdf/' . $inv->id) ?>" class="tip btn btn-primary" title="<?= lang('download_pdf') ?>">
                                <i class="fa fa-download"></i>
                                <span class="hidden-sm hidden-xs"><?= lang('pdf') ?></span>
                            </a>
                        </div>
                        <div class="btn-group">
                            <a href="<?= admin_url('purchases/edit/' . $inv->id) ?>" class="tip btn btn-warning sledit" title="<?= lang('edit') ?>">
                                <i class="fa fa-edit"></i>
                                <span class="hidden-sm hidden-xs"><?= lang('edit') ?></span>
                            </a>
                        </div>
                        <div class="btn-group">
                            <a href="#" class="tip btn btn-danger bpo" title="<b><?= $this->lang->line("delete") ?></b>"
                               data-content="<div style='width:150px;'><p><?= lang('r_u_sure') ?></p><a class='btn btn-danger' href='<?= admin_url('purchases/delete/' . $inv->id) ?>'><?= lang('i_m_sure') ?></a> <button class='btn bpo-close'><?= lang('no') ?></button></div>"
                               data-html="true" data-placement="top">
                                <i class="fa fa-trash-o"></i>
                                <span class="hidden-sm hidden-xs"><?= lang('delete') ?></span>
                            </a>
                        </div>
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
<script type="text/javascript">
    $(document).ready(function () {
        $('.tip').tooltip();
    });
</script>
