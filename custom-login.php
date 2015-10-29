<?php
function fc_power_my_login_logo() { ?>
    <style type="text/css">
        .login h1 a {
            background: url(<?php echo plugin_dir_url( __FILE__ ); ?>assets/img/site-login-logo.png) top center no-repeat;
            padding-bottom: 10px;
			width:240px;
        }
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'fc_power_my_login_logo' );
