<?php

/**
 * Enqueue scripts and styles.
 */
function vr_enqueue_admin_scripts() {
    wp_enqueue_media();

    // Enqueue Sweet Alert
    wp_enqueue_script( 'sweet-alert', 'https://cdn.jsdelivr.net/npm/sweetalert2@11', array(), '11');

    // Enqueue Loading.io
    wp_enqueue_script( 'loadingio-ldloader', 'https://cdn.jsdelivr.net/gh/loadingio/ldloader@v1.0.0/dist/ldld.min.js', array(), '1.0.0');
    wp_enqueue_style( 'loadingio-ldloader-style', 'https://cdn.jsdelivr.net/gh/loadingio/ldloader@v1.0.0/dist/ldld.min.css', array(), '1.0.0', 'all' );

    // Third party
    wp_enqueue_script( 'third-party', plugins_url( '/admin/js/third-party.js', dirname( __FILE__ ) ), array( 
        'loadingio-ldloader', 'jquery', 'sweet-alert'
    ), '1.0.0', true );

    // Enqueue admin styles
    wp_enqueue_style( 'vietqr-admin-style', plugins_url( '/admin/css/style.css', dirname( __FILE__ ) ), array(), '1.0.0', 'all' );
    wp_enqueue_style( 'vietqr-admin-tailwind-style', plugins_url( '/admin/css/output.css', dirname( __FILE__ ) ), array(), '1.0.0', 'all' );

    // Enqueue admin scripts
    wp_enqueue_script( 'vietqr-admin-script', plugins_url( '/admin/js/main.js', dirname( __FILE__ ) ), array( 'jquery', 'third-party' ), '1.0.0', true );
}
add_action( 'admin_enqueue_scripts', 'vr_enqueue_admin_scripts' );

/**
 * Enqueue scripts and styles for front with Ajax URL
 */
function vr_enqueue_front_scripts() {
    // Enqueue Jquery
    wp_enqueue_script('jquery');

    // Enqueue Sweet Alert
    wp_enqueue_script( 'sweet-alert', 'https://cdn.jsdelivr.net/npm/sweetalert2@11', array(), '11');

    // Enqueue Loading.io
    wp_enqueue_script( 'loadingio-ldloader', 'https://cdn.jsdelivr.net/gh/loadingio/ldloader@v1.0.0/dist/ldld.min.js', array(), '1.0.0');
    wp_enqueue_style( 'loadingio-ldloader-style', 'https://cdn.jsdelivr.net/gh/loadingio/ldloader@v1.0.0/dist/ldld.min.css', array(), '1.0.0', 'all' );

    // Third party
    wp_enqueue_script( 'third-party', plugins_url( '/admin/js/third-party.js', dirname( __FILE__ ) ), array( 
        'loadingio-ldloader', 'jquery', 'sweet-alert'
    ), '1.0.0', true );

    // Enqueue front styles
    wp_enqueue_style( 'vietqr-front-style', plugins_url( '/public/css/style.css', dirname( __FILE__ ) ), array(), '1.0.0', 'all' );
    wp_enqueue_style( 'vietqr-front-tailwind-style', plugins_url( '/public/css/output.css', dirname( __FILE__ ) ), array(), '1.0.0', 'all' );
    
    // Enqueue front scripts
    wp_enqueue_script( 'vietqr-front-script', plugins_url( '/public/js/main.js', dirname( __FILE__ ) ), array( 'jquery' ), '1.0.0', true );
    wp_enqueue_script('vietqr-js', VIETQR_URL . 'public/js/vietqr.js', array( 'jquery', 'third-party' ), null, true);

    // Enqueue WordPress admin-ajax.php URL
    wp_localize_script('vietqr-js', 'vietqrAjax', array(
        'ajaxurl' => admin_url('admin-ajax.php')
    ));
}
add_action( 'wp_enqueue_scripts', 'vr_enqueue_front_scripts' );