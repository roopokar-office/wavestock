<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('edit_tag'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        echo admin_form_open_multipart("tags/edit_tag/" . $tag->id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('update_info'); ?></p>

            <div class="form-group">
                <?= lang('code', 'code'); ?>
                <?= form_input('code', $tag->code, 'class="form-control" id="code"'); ?>
            </div>

            <div class="form-group">
                <?= lang('name', 'name'); ?>
                <?= form_input('name', $tag->name, 'class="form-control gen_slug" id="name" required="required"'); ?>
            </div>

            <div class="form-group all">
                <?= lang('slug', 'slug'); ?>
                <?= form_input('slug', set_value('slug', $tag->slug), 'class="form-control tip" id="slug" required="required"'); ?>
            </div>

            <div class="form-group all">
                <?= lang('description', 'description'); ?>
                <?= form_input('description', set_value('description', $tag->description), 'class="form-control tip" id="description" required="required"'); ?>
            </div>

            <div class="form-group">
                <?= lang("image", "image") ?>
                <input id="image" type="file" data-browse-label="<?= lang('browse'); ?>" name="userfile" data-show-upload="false" data-show-preview="false"
                       class="form-control file">
            </div>
            <?php echo form_hidden('id', $tag->id); ?>
        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_tag', lang('edit_tag'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<?= $modal_js ?>
<script>
    $(document).ready(function() {
        $('.gen_slug').change(function(e) {
            getSlug($(this).val(), 'tag');
        });
    });
</script>
