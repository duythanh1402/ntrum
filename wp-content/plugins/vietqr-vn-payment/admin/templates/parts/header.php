<?php
/**
 * Header template.
 * 
 * @package VietQR
 */

global $errors;
global $successes;

?>

<div class="vietqr-header">
    <?php
    vr_get_admin_template( 'parts/notice-error', compact( 'errors' ) );
    vr_get_admin_template( 'parts/notice-success', compact( 'successes' ) );
    ?>
</div>