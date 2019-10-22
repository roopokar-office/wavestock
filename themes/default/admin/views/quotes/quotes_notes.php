<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>


<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-sticky-note"></i><?= lang('Quotation_Notes'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext">Enter Default Quotation Notes in the Text Box</p>
                <?php
                $attrib = array('data-toggle' => 'validator', 'role' => 'form');
                echo admin_form_open_multipart("quotes/notes", $attrib);

                ?>
                <div class="row">
                    <div class="col-lg-12">

                        <div class="row" id="bt">
                            <div class="col-md-12">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?= lang("Quotation_Note", "quotes_note"); ?>
                                        <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : $this->site->getNotes('quotes')->notes_description), 'class="form-control" id="quotes_note" style="margin-top: 10px; height: 100px;"'); ?>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="fprom-group"><?php echo form_submit('update_notes', $this->lang->line("submit"), 'id="update_notes" class="btn btn-primary" style="padding: 6px 15px; margin:15px 0;"'); ?>
                        </div>
                    </div>
                </div>

                <?php echo form_close(); ?>

            </div>
        </div>
    </div>
</div>
