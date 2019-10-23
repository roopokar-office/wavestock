<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog modal-lg no-modal-header">
    <div class="modal-content">
        <div class="modal-body">
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
                            <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $row->product_unit_code; ?></td>
                            <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $this->sma->formatQuantity($row->quantity); ?></td>
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
                                <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $row->product_unit_code; ?></td>
                                <td style="width: 80px; text-align:center; vertical-align:middle;"><?= $this->sma->formatQuantity($row->quantity); ?></td>
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

            <?= $Settings->invoice_view > 0 ? $this->gst->summary($rows, $return_rows, ($return_sale ? $inv->product_tax+$return_sale->product_tax : $inv->product_tax)) : ''; ?>

            <div class="row">
                <div class="col-xs-12">
                    <span class="bold"><?= lang("note"); ?>: </span>
                    <?= $this->sma->decode_html($this->site->getNotes('challan')->notes_description); ?>
                    <br/>
                    <br/>

                    <?php
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
<script type="text/javascript">
    $(document).ready( function() {
        $('.tip').tooltip();
    });
</script>
