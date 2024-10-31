<?php


/**
****************************************************************
* GENERAL PAGE CALLBACK -- HTML
* @since   0.0.3
****************************************************************/
function rsggds_sheet(){ ?>
    <div class="rynerg_rsggds rsg-input_all rsg-strong_green_all" style="padding:20px;">
        <div>
            <p class="rsg-big_title">Retrieve Google Drive Spreadsheet</p>
            <p class="rsg-reg_title">shortcode: [RSGGDS_TABLE]</p>
        </div>
        <div>
            <div class="rsggds_my_contents" style="padding:10px;">
                <p> Welcome and thank you for using this plugin.This helps you display your spreadsheets from your Google Drive.<br>
                    Make sure however that the link is public and is published to the web.<br>
                    To publish your spreadsheet and make it public, <a target="_blank" href="https://drive.google.com/open?id=1QfH3GLKMlaUGNZExCSXPi-B9lBKa8pf-"><strong>click me</strong></a> for more information.
                    <br>
                    Display of tables uses the <a href="https://mottie.github.io/tablesorter/docs/"><strong>tablesorter script library</strong></a>. You can check their jQuery plugin awesomeness <a href="https://github.com/Mottie/tablesorter"><strong>at github</strong></a>.
                </p>
                <p> <i>Spreadsheet Link ID.</i>
                    <input type="text" id="rsggds_sheet-link" value="<?php echo esc_html(rsggds_get_option('rsggds_link')); ?>">
                </p>
                <p> <i>Spreadsheet Table Title.</i>
                    <input type="text" id="rsggds_sheet-title" value="<?php echo esc_html(rsggds_get_option('rsggds_title')); ?>">
                </p>
                <p> <i>Enter columns to include. Separate multiple columns by comma. (example: A,B,C,D)</i>
                    <input type="text" id="rsggds_sheet-cols" value="<?php echo esc_html(rsggds_get_option('rsggds_cols')); ?>">
                </p>
                <p> <i>Enter the number of the desired first and last row. Separate by period. (example: 1.15). Using zero(0) will revert to default.</i>
                    <input type="number" id="rsggds_sheet-row" value="<?php echo esc_html(rsggds_get_option('rsggds_row')); ?>">
                </p>

                <p style="max-width: 250px; width: 100%; display: inline-block;">
                    <i>Table Max Height (px) Default=500px</i>
                    <input type="number" id="rsggds_sheet-height" value="<?php echo esc_html(rsggds_get_option('rsggds_tbl_h')); ?>" placeholder="Height">
                </p>

                <p style="max-width: 250px; width: 100%; display: inline-block;">
                    <i>Table Max Width (px) Default=1200px</i>
                    <input type="number" id="rsggds_sheet-width" value="<?php echo esc_html(rsggds_get_option('rsggds_tbl_w')); ?>" placeholder="Width">
                </p>

                <p style="max-width: 250px; width: 100%; display: inline-block;">
                    <i>Table Border ( Outside border only) </i>
                    <input type="color" id="rsggds_sheet-color" style="padding:unset; height:23px;" value="<?php echo get_option('rsggds_tbl_color'); ?>">
                </p>

                <p> <span class="rsg-button_main rsg-btn_white" id="rsggds_save" style="border-radius:5px; font-size:18px;">Save</span> </p>
                <hr>
                 <?php if(get_option('rsggds_link') == '1AmhOJ_pYHGc4q89tY5U8HtIpwB7klSAOdDR1b6M_x7o'): ?>
                    <p> This is my sample table. Check the Spreadsheet <a href="https://docs.google.com/spreadsheets/d/1AmhOJ_pYHGc4q89tY5U8HtIpwB7klSAOdDR1b6M_x7o/edit#gid=0" target="_blank"><strong>here!</strong></a> </p>
                    <br>
                <?php endif; ?>
                <p> <?php echo do_shortcode('[RSGGDS_TABLE]'); ?> </p>
            </div>
        </div>
    </div>
    <?php
}

/**
****************************************************************
* SAVE FIELDS
* @since   0.0.3
*
****************************************************************/
add_action('wp_ajax_nopriv_rsggds_do_action','rsggds_do_action');
add_action('wp_ajax_rsggds_do_action','rsggds_do_action');
function rsggds_do_action(){
    
    $get_link = sanitize_text_field($_REQUEST['link']);
    $get_ttl  = sanitize_text_field($_REQUEST['ttl']);
    $get_row  = preg_replace("/[^0-9.]/", "", str_replace(',', '.', sanitize_text_field( abs($_REQUEST['row']) ) ) );
    $get_cols = preg_replace("/[^A-Z,]/", "", str_replace('.', ',', sanitize_text_field( $_REQUEST['cols'] ) ) );
    if($get_row == 0 ){
        $get_row = 1.15;
    }else{
        if (strpos($get_row, ".") !== false) {
            $rows   = explode('.',$get_row );  
            if(count($rows) > 2){ foreach($rows as $k=>$r){ if($k > 1){ unset($rows[$k]); } } }
            if($rows[0] == 0){ $rows[0] = 1; }
            if($rows[0] > $rows[1]){ $rows[1] = $rows[0] + 1; }
            $get_row   = implode('.', $rows);  
        }else{
            $gr = strlen(absint($get_row));
            $cr = '1';
            while( $gr != 0 ){ $cr = $cr.'0'; $gr--; }
            $get_row = 1 + ($get_row / (float)$cr);
        }
    }
    
    
    update_option('rsggds_link',$get_link);
    update_option('rsggds_title',$get_ttl);
    update_option('rsggds_row',abs($get_row));
    update_option('rsggds_cols',$get_cols);

    $get_tbl_h      = preg_replace("/[^0-9]/", "", sanitize_text_field( abs($_REQUEST['tbl_h'])));
    $get_tbl_w      = preg_replace("/[^0-9]/", "", sanitize_text_field( abs($_REQUEST['tbl_w'])));
    $get_tbl_color  = sanitize_text_field( $_REQUEST['tbl_color']);

    update_option('rsggds_tbl_h',$get_tbl_h);
    update_option('rsggds_tbl_w',$get_tbl_w);
    update_option('rsggds_tbl_color',$get_tbl_color);

    $result['tbl'] = do_shortcode('[RSGGDS_TABLE]');
    $result = json_encode($result); echo $result; die();
}


/**
****************************************************************
* GET OPTIONS
* @since   0.0.1
****************************************************************/
function rsggds_get_option($get){
    $val = get_option($get);
    if( $val == false ){
        if( $get == 'rsggds_row' ){
            if( $val == 0 ){
                $val = 1.15;
            }
        }else{
            $val = '';    
        }
    }
    return $val;
}

/**
****************************************************************
* RETURN OPTIONS
* @since   0.0.1
****************************************************************/
function rsggds_return_contents(){
    $me = [];
    $calling_api = new RSGGDSS_CALL();
    $call_api = $calling_api->rsggds_call_api( rsggds_get_option('rsggds_link'),rsggds_get_option('rsggds_title') );
    if( $call_api == 'error_men_tol' ){
        return 'error_men_tol';
    }else{
        $call_api = $call_api->getCellFeed();
        $col = array(
            '1'=>'A', '2'=>'B', '3'=>'C', '4'=>'D', '5'=>'E', '6'=>'F', '7'=>'G', '8'=>'H', '9'=>'I', '10'=>'J',
            '11'=>'K', '12'=>'L', '13'=>'M', '14'=>'N', '15'=>'O', '16'=>'P', '17'=>'Q', '18'=>'R', '19'=>'S', '20'=>'T',
            '21'=>'U', '22'=>'V', '23'=>'W', '24'=>'X', '25'=>'Y', '26'=>'Z' );
        $me['api'] = $call_api->getEntries();
        $me['cmp'] = $col;

        return $me;
    }
}


/**
****************************************************************
* RETURN HTML TABLE -- SHORTCODE
* @since   0.0.1
****************************************************************/
function rsggds_return_table_sc($sc){
    ob_start();
    $me     = rsggds_return_contents();
    if( $me != 'error_men_tol' ){
        $cols   = explode(',',rsggds_get_option('rsggds_cols') );
        $row    = explode('.',rsggds_get_option('rsggds_row') );
        $rw = $cl = [];
        $headctr = $bodyctr = 0;

        // CONVERT LETTERS TO NUMBERS OF COLUMNS
        foreach( $cols as $c ){ foreach( $me['cmp'] as $col_num=> $col_let ){ if( $c == $col_let ){ array_push($cl, $col_num); } } }

        // GET ALL ROWS BASED ON ENTERED VALUES
        $row_first = $row[0];  $row_last  = $row[1];
        while( $row_first < ($row_last+1) ){ array_push($rw, $row_first); $row_first++; }

        $rsggds_tbl_h = 'max-height:'.get_option('rsggds_tbl_h').'px;';
        $rsggds_tbl_w = 'max-width:'.get_option('rsggds_tbl_w').'px;';
        $rsggds_tbl_color = 'border:1px solid '.get_option('rsggds_tbl_color').';'; ?>

        <!-- STICKY HEADER WONT WORK IF ENQUEUED -->
        <i class="rsggds_admin_home" data="<?php echo rsggds_LIB; ?>"></i>
        <div class="rsggds_table_cotain" style="<?php echo $rsggds_tbl_h. $rsggds_tbl_w.$rsggds_tbl_color; ?>">
            <table class="rsggds_table_contents tablesorter" border="0">
                <thead>
                    <?php foreach ($rw as $row_val):
                        if($headctr == 0):
                            echo '<tr>';
                            foreach($me['api'] as $entry):
                                $col = $entry->getColumn();
                                $tex = str_replace('_',' ',$entry->getContent());
                                $row = $entry->getRow();
                                if( ($row == $row_val) ){
                                    foreach( $cl as $col_val ){
                                        if( $col == $col_val ){ if( $tex == '---' ){ echo '<td></td>'; } else{ echo '<td>'.$tex.'</td>'; } }
                                    }
                                }  
                            endforeach;
                            echo '</tr>';
                            $headctr++;
                        endif;
                    endforeach; ?>
                </thead>

                <tbody>
                    <?php foreach ($rw as $row_val):
                        if($bodyctr != 0):
                            echo '<tr>';
                            foreach($me['api'] as $entry) {
                                $col = $entry->getColumn();
                                $tex = str_replace('_',' ',$entry->getContent());
                                $row = $entry->getRow();
                                if( ($row == $row_val) ){
                                    foreach( $cl as $col_val ){
                                        if( $col == $col_val ){ if( $tex == '---' ){ echo '<td></td>'; }else{ echo '<td>'.$tex.'</td>'; } }
                                    }
                                }
                            }
                            echo '</tr>';
                        endif; $bodyctr++;
                    endforeach; ?>
                </tbody>
            </table>
        </div>
        <script>
            jQuery(function() {
                jQuery("table.rsggds_table_contents").tablesorter({
                    widthFixed : true,
                    headerTemplate : '{content} {icon}',
                    widgets: ["columnSelector", "zebra", "pager", 'stickyHeaders'],
                    widgetOptions : {
                        stickyHeaders_attachTo: jQuery('.rsggds_table_cotain'),
                        stickyHeaders_zIndex : 2,
                        stickyHeaders_addResizeEvent : true,
                        stickyHeaders_cloneId : '-sticky',
                        stickyHeaders_offset : 0,
                        stickyHeaders : 'hasStickyHeaders'
                    },
                    sortInitialOrder: "asc", usNumberFormat : false, sortReset : true, sortRestart : true
                });
            });
        </script>
    <?php
    }else{
        echo '<div style="text-align:center; font-size:20px;"> <strong>No Table Found</strong></div>';
    } 
    return ob_get_clean();
}
require('rsggds-call.php');