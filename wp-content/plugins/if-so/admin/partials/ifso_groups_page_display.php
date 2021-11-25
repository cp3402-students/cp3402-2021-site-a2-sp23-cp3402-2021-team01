<?php
$groups_service = IfSo\PublicFace\Services\GroupsService\GroupsService::get_instance();
$groups_list = $groups_service->get_groups();

function generate_version_symbol($version_number) {
    //This function appears in multiple places - move to a utility class - DRY
    $version_number += 65;
    $num_of_characters_in_abc = 26;
    $base_ascii = 64;
    $version_number = intval($version_number) - $base_ascii;

    $postfix = '';
    if ($version_number > $num_of_characters_in_abc) {
        $postfix = intval($version_number / $num_of_characters_in_abc) + 1;
        $version_number %= $num_of_characters_in_abc;
        if ($version_number == 0) {
            $version_number = $num_of_characters_in_abc;
            $postfix -= 1;
        }
    }

    $version_number += $base_ascii;
    return chr($version_number) . strval($postfix);
}
?>
<div class="wrap">
    <h2>
        <?php
        _e('If-So Dynamic Content | Audiences');
        ?>
    </h2>
    <form class="add_new_group" method="post"  action="<?php echo admin_url('admin-ajax.php'); ?>" >
        <input name="group_name" type="text" required placeholder="<?php _e('New Audience Name', 'if-so');?>">
        <input type="hidden" name="ifso_groups_action" value="add_group">
        <input type="hidden" name="action" value="ifso_groups_req">
        <?php wp_nonce_field( "ifso-groups-action-nonce"); ?>
        <button class="button button-primary" type="submit"><?php _e('Create New Audience', 'if-so'); ?></button>
    </form>
    <table id="ifso-all-groups-table" class="widefat striped">
        <colgroup>
            <col span="1" style="width: 20%;">
            <col span="1" style="width: 60%;">
            <col span="1" style="width: 20%;">
        </colgroup>
        <thead>
        <tr>
            <th><?php _e('Audience Name', 'if-so');?></th>
            <th><?php _e('Triggers for which users are added or removed from this audience', 'if-so');?></th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php
        if(isset($groups_list) && is_array($groups_list) && !empty($groups_list)){
            foreach ($groups_list as $group){
                $occurences = '';
                foreach($groups_service->scanTriggersForGroupOccurence($group) as $occ){
                    $versionsText='';
                    if(isset($occ['versions']) && is_array($occ['versions'])){
                        foreach($occ['versions'] as $version=>$action){
                            $versionName = generate_version_symbol($version);
                            $versionsText .= "Version {$versionName} ({$action}), ";
                        }
                        $versionsText = substr($versionsText, 0, -2);
                    }
                    $link = "<a href={$occ['link']} target='_blank '>{$occ['title']}</a>";
                    $versions = "<span>{$versionsText}</span>";
                    $occurences .= $link . '<br>&nbsp;&nbsp;&nbsp;&nbsp;' . $versions .  '<br>';
                }
                $delme = admin_url('admin-ajax.php?action=ifso_groups_req&ifso_groups_action=remove_group&group_name=' . urlencode($group) . '&_wpnonce=' . wp_create_nonce('ifso-groups-action-nonce'));
                echo "<tr>
                        <td> {$group}</td>
                        <td>{$occurences}</td>
                        <td><a class='delete' href='{$delme}'>Delete Audience</a></td>
                    </tr>";
            }
        }
        ?>
        <tbody>
    </table>
    <?php
        if(!isset($groups_list) || empty($groups_list))
            echo "<p style='text-align:center;font-style:italic;font-size: 18px;'>You haven't created any audiences yet<br><a href='https://www.if-so.com/help/documentation/segments/?utm_source=Plugin&utm_medium=Help&utm_campaign=AudPage' target='_blank'>Learn about Audiences</a></p>";
    ?>
</div>

<!-- Generate Short-code Start -->

<!-- CSS -->
<style type="text/css">
    .form-generate-shortcode-section .wp-core-ui select{
        background: #f1f1f1;
        border:1px solid #cccccc;
    }
    .form-generate-shortcode-section {
        padding: 20px;
        margin: 30px 20px 0 2px;
        border: 1px solid #c3c4c7;
    }
    .form-generate-shortcode-section th{
        color: #5c5b5b;
    }
    .form-generate-shortcode-section input[type=color],
    .form-generate-shortcode-section input[type=date],
    .form-generate-shortcode-section input[type=datetime-local],
    .form-generate-shortcode-section input[type=datetime],
    .form-generate-shortcode-section input[type=email],
    .form-generate-shortcode-section input[type=month],
    .form-generate-shortcode-section input[type=number],
    .form-generate-shortcode-section input[type=password],
    .form-generate-shortcode-section input[type=search],
    .form-generate-shortcode-section input[type=tel],
    .form-generate-shortcode-section input[type=text],
    .form-generate-shortcode-section input[type=time],
    .form-generate-shortcode-section input[type=url],
    .form-generate-shortcode-section input[type=week],
    .form-generate-shortcode-section select,
    .form-generate-shortcode-section textarea{
        background-color: #f1f1f1;
    }

    button#fgs_button {
        border: none;
        background: #1aa61a;
        padding: 7px 25px;
        border-radius: 5px;
        color: #fff;
        text-transform: uppercase;
        cursor: pointer;
    }
    label#fgs_shortcode {
        background: #dcdbdb;
        padding: 10px;
        border: 1px solid #7d7d7d;
        display:block;
        margin-bottom: 1em;
    }
    .audiences_group {
        width: 30%;
        display: inline-block;
        margin: 5px 0;
    }
    .audiences_group input[type=checkbox],
    .audiences_group input[type=radio]{
        margin: 0 10px 0 0;
        color: #000;
        border: 1px solid #000;
        box-shadow: none;
    }
    .audiences_group label{
        color:#000;
        text-transform: capitalize;
    }
    label.description {
        font-style: italic;
        color: #5c5b5b;
    }
    input#fgs_ajax{
        background: #f1f1f1;
    }
    @media screen and (max-width: 500px){
        .audiences_group{
            width: 50%;
        }
    }

    .generate-shortcode-message{
        background: #fff;
        border: 1px solid #c3c4c7;
        border-left-color: rgb(195, 196, 199);
        border-left-width: 1px;
        border-left-width: 4px;
        box-shadow: 0 1px 1px rgba(0,0,0,.04);
        margin: 5px 15px 2px;
        padding: 1px 12px;
        padding-right: 12px;
        border-left-color: #d63638;
        border-left-color: #dba617;
    }
</style>

<!--IFSO GROUP FORM SHORTCODE GENERATOR HTML -->

<div class="form-generate-shortcode-section">
    <div class="wrap">
        <?php
        if(!defined('IFSO_ADD_TO_GROUP_FORM_ON') || !IFSO_ADD_TO_GROUP_FORM_ON):?>
        <h2></h2>
        <?php endif; ?>
        <div class="selection-title"><h2><?php _e('Generate a User Self-selection Form', 'if-so'); ?></h2></div>
        <div class="inner-table">
            <table class="form-table generate-tbl">
                <tbody>
                <tr>
                    <th><?php _e('Form Type', 'if-so'); ?></th>
                    <td>
                        <select id="fgs_type">
                            <option value="ftype_radio"><?php _e('Radio Buttons', 'if-so'); ?></option>
                            <option value="ftype_select"><?php _e('Select field', 'if-so'); ?></option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <th><?php _e('Audiences', 'if-so'); ?></th>
                    <td>
                        <?php
                        if(isset($groups_list) && is_array($groups_list) && !empty($groups_list)){
                            foreach ($groups_list as $group){
                                echo '<div class="audiences_group"><input type="checkbox" name="fgs_audiences[]" value="'.$group.'" id="'.$group.'" />';
                                echo '<label for="'.$group.'" />'.$group.'</label></div>';
                            }
                        }
                        ?>
                    </td>
                </tr>

                <tr>
                    <th><?php _e('Button Text', 'if-so'); ?></th>
                    <td>
                        <input type="text" id="fgs_text" />
                        <label class="description"><?php _e('Leave blank if you don\'t want to include a button', 'if-so'); ?></label>
                    </td>
                </tr>

                <tr>
                    <th><?php _e('Default Option', 'if-so'); ?></th>
                    <td>
                        <input type="text" id="fgs_default" />
                        <label class="description"><?php _e('The default option is the option that users will see before clicking the select form', 'if-so'); ?></label>
                    </td>
                </tr>

                <tr>
                    <th><?php _e('Ajax', 'if-so'); ?></th>
                    <td>
                        <input type="checkbox" id="fgs_ajax" checked />
                        <label class="description" for="fgs_ajax"><?php _e('If you uncheck this option the page will be loaded again automatically when the user submits the form', 'if-so'); ?></label>
                    </td>
                </tr>

                <tr>
                    <th>
                        <button id="fgs_button"><?php _e('Generate', 'if-so'); ?></button>
                    </th>
                </tr>
                </tbody>
            </table>
        </div>
        <div>
            <h3><?php _e('Copy and paste the shortcode', 'if-so'); ?></h3>
            <label id="fgs_shortcode">You haven't generated the shortcode</label>
        </div>
        <div class="generate-shortcode-message">
            <p>Please download and install the <a href="https://www.if-so.com/dynamic-select-form/">Self-selection Fields extension</a> in order for the shortcode to work. </p>
        </div>
    </div>
</div>

<!-- jQuery / JavaScript -->
<script type="text/javascript">
    $ = jQuery;
    $(document).ready(function() {
        $('#fgs_button').click(function(event) {
            var fgs_type = $('#fgs_type').val();
            var fgs_default = $('#fgs_default').val();
            var fgs_text = $('#fgs_text').val();
            var fgs_audiences = $('input[name="fgs_audiences[]"]:checked');
            var fgs_shortcode_text = '[ifso_group_selection';

            if(fgs_type.length != 0 ) {
                var type = '';
                if( fgs_type == 'ftype_radio' ){
                    type = 'radio';
                } else {
                    type = 'select';
                }
                fgs_shortcode_text = fgs_shortcode_text + ' type="' + type + '"';
            }

            if(fgs_audiences.length > 0 ){
                var audiences = '';
                fgs_audiences.each(function() {
                    audiences = audiences + $(this).val() + ',';
                });
                audiences = audiences.slice(0, -1);
                fgs_shortcode_text = fgs_shortcode_text + ' options="' + audiences + '"';
            }

            if(fgs_default.length != 0 ) {
                fgs_shortcode_text = fgs_shortcode_text + ' default-option="' + fgs_default + '"';
            }

            if($('#fgs_ajax').is(':checked')) {
                fgs_shortcode_text = fgs_shortcode_text + ' ajax="yes"';
            } else {
                fgs_shortcode_text = fgs_shortcode_text + ' ajax="no"';
            }

            if(fgs_text.length != 0 ) {
                fgs_shortcode_text = fgs_shortcode_text + ' button="' + fgs_text + '"';
            }

            $('#fgs_shortcode').html(fgs_shortcode_text + ' ]');
        });
    });
</script>

<!-- Generate Short-code Start -->
