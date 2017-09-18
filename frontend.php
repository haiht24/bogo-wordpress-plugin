<?php
// Add the JS
function theme_name_scripts() {
    $roles = wp_get_current_user()->roles;
    if($roles[0] === 'administrator' || $roles[0] === 'editor'){
        wp_enqueue_script( 'bogo-frontend-script', plugin_dir_url( __FILE__ ) . '/js/frontend.js', ['jquery'], '1.0.0', true );
        wp_localize_script( 'bogo-frontend-script', 'bogoAjaxVar', [
            // URL to wp-admin/admin-ajax.php to process the request
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            // generate a nonce with a unique ID "myajax-post-comment-nonce"
            // so that you can check it later when an AJAX request is sent
            'security' => wp_create_nonce( 'haiht_secret_key_string' )
        ]);
    }
}
add_action( 'wp_enqueue_scripts', 'theme_name_scripts' );

// The function that handles the AJAX request
function frontend_callback() {
    check_ajax_referer( 'haiht_secret_key_string', 'security' );
    $whatever = intval( $_POST['whatever'] );
    $whatever += 10;
    echo $whatever;
    die(); // this is required to return a proper result
}
add_action( 'wp_ajax_quick_update_post', 'update_post' );
function update_post() {
    if($_POST['action'] === 'quick_update_post'){
        $id = $_POST['params']['id'];
        $title = $_POST['params']['title'];
//        $author = get_current_user_id();
        $updateAt = current_time('mysql');
        $params = [
            'ID' => $id,
            'post_title' =>$title,
//            'post_author' => $author,
            'post_date' => $updateAt
        ];
        $result = wp_update_post($params, false);
        if($result === (Int)$id){
            echo 'success';
        }else{
            echo 'error';
        }
    }
    die;
}