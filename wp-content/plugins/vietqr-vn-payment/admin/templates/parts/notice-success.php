<?php
/**
 * Display a success notice in the WordPress admin area.
 *
 * @var array $successes Array of success messages.
 */

if (!empty($successes)) {
    foreach ($successes as $success) {
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?php echo esc_html($success); ?></p>
        </div>
        <?php
    }
}