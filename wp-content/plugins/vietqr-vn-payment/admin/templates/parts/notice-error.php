<?php
/**
 * Display a error notice in the WordPress admin area.
 *
 * @var array $errors Array of success messages.
 */

if (!empty($errors)) {
    echo '<div class="notice notice-error is-dismissible">';
    foreach ($errors as $error) {
        ?>
            <p><?php echo esc_html($error); ?></p>
        <?php
    }
    echo '</div>';
}