<?php
/*------------------------------------------------------------------------------
Plugin Name: Gravity forms UTM tracking
Plugin URI: https://www.woofers.be
Description: UTM tracking for Gravity Forms
Author: Dave Loodts
Version: 1.0
Author URI: https://www.woofers.be
------------------------------------------------------------------------------*/
wp_enqueue_script('jquery');
// Because Pantheon server, so get url current by js
function send_cookie_js() {   
    wp_enqueue_script( 'send_cookie_js', plugin_dir_url( __FILE__ ) . 'send-cookie.js' );
}
add_action('wp_enqueue_scripts', 'send_cookie_js');


if(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) != parse_url($_COOKIE['CK_HTTP_REFERER'], PHP_URL_HOST)   ){
    if(isset($_COOKIE['CK_HTTP_REFERER'])){
        $referer_link = $_COOKIE['CK_HTTP_REFERER'];
    }else{
        $referer_link  = $_SERVER['HTTP_REFERER'];
    }
}else{
    $referer_link = $_SERVER['HTTP_REFERER'];
}

// echo $referer_link;
// print_r($_SERVER);


// Save variable url current
function variable_urlcurrent_save_cookie(){
    // Get url current
    $url_current = $_POST['url_current'];
    // Send to array php
    $query_str = parse_url($url_current, PHP_URL_QUERY);
    parse_str($query_str, $query_params);
    foreach($query_params as $param => $key){
        setcookie('CK_'.$param, htmlspecialchars($key, ENT_QUOTES), time() + 99999999, '/', NULL);
    }

    die();
}
add_action('wp_ajax_variable_urlcurrent_save_cookie', 'variable_urlcurrent_save_cookie');
add_action('wp_ajax_nopriv_variable_urlcurrent_save_cookie', 'variable_urlcurrent_save_cookie');


// if (strpos((isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", 'test-form') !== false   || strpos((isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", 'utm_source') !== false  ) {
//     print_r($_SERVER);
//     print_r($_COOKIE);
// }

// get fields names
$gravitypopulate = explode(',', esc_attr(get_option('gravitypopulate_options')));
$gravitypopulate = array_map('trim', $gravitypopulate);
// print_r($gravitypopulate);

add_action('init', function($arg) use ($gravitypopulate, $referer_link)
{
    if(!is_admin()) save_ref($gravitypopulate, htmlspecialchars($referer_link));
}, 1);

function save_ref($gravitypopulate, $referer_link)
{

    // $gravitypopulate = str_replace('_', '-', $gravitypopulate);
    //stores GET varaible in cookies if available
    // foreach ($gravitypopulate as $key) {
    //     if (isset($_GET[$key])) {           
    //        setcookie('CK_'.$key, htmlspecialchars($_GET[$key], ENT_QUOTES), time() + 99999999, '/', NULL);                      
    //     }
        
    // }

    setcookie('CK_HTTP_REFERER', htmlspecialchars($referer_link, ENT_QUOTES), time() + 99999999, '/', NULL);
    if (isset($_COOKIE['CK_HTTP_REFERER'])) {
        $_POST['input_-2']= htmlspecialchars($referer_link);
        $_POST['input_-1']= (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }elseif(isset($referer_link) and $referer_link!=''){

        $_POST['input_-2']=htmlspecialchars($referer_link);
        $_POST['input_-1']= (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }

}


foreach ($gravitypopulate as $key) {
    add_filter('gform_field_value_' . $key, function($arg) use ($key)
    {
         if (isset($_GET[$key])) {        
            return htmlspecialchars($_GET[$key], ENT_QUOTES);        
        }else if (isset($_COOKIE['CK_'.$key])) {            
            return htmlspecialchars($_COOKIE['CK_'.$key], ENT_QUOTES);            
        }else
            return '';
    }, -999);
    
}

function generate_Populate_admin_page()
{    
    $msg = '';    
    if (!empty($_POST) && check_admin_referer('gravitypopulate_options_update', 'gravitypopulate_admin_nonce')) {        
        update_option('gravitypopulate_options', stripslashes($_POST['inputs']));  
        $msg = '<div class="updated"><p>Your settings have been<strong>updated</strong></p></div>';        
    }

    echo '<div class="wrap">

  <h2>Gravity Populate Configuration</h2>' . $msg . '

  <form action="" method="post" id="inputs">
    <p>Enter inputs Parameter Names separated with , <br/>

      <textarea type="text" id="inputs" name="inputs" style="width:60%;">' . esc_attr(get_option('gravitypopulate_options')) . '</textarea>

    </p>


    <p class="submit">

      <input type="submit" name="submit"

value="Update" />

    </p>

    ' . wp_nonce_field('gravitypopulate_options_update', 'gravitypopulate_admin_nonce') . '

  </form>

</div>';
    
    
    
}



function Gravity_Populate_add_menu_item()
{
    
    add_submenu_page('plugins.php', // Menu page to attach to
        'Gravity Populate Configuration', // page title
        'Gravity Populate', // menu title
        'manage_options', // permissions
        'Gravity-Populate', // page-name (used in the URL)
        'generate_Populate_admin_page' // clicking callback function
        );
    
}

add_action('admin_menu', 'Gravity_Populate_add_menu_item');


function gravity_custom_prepopulate_js()
{
    $js =  '<script>jQuery(document).ready(function(){';
    // $js .= 'jQuery("form").find("input[name=input_-1]").remove()';
    $js .= 'jQuery("form").append("<input type=\'hidden\' name=\'input_-1\' value=\'"+window.location.href+"\'>");';
    $js .= '});';
    $js .= '</script>';
    echo $js;
    
}

//add_filter('wp_head', 'gravity_custom_prepopulate_js');


/* tracking email */

add_filter("gform_save_field_value", "sakka_save_field_value", 10, 4);

function sakka_save_field_value($value, $lead, $field, $form)
{
    if ($field["label"] == 'Email') {
        setcookie('email', htmlspecialchars($value, ENT_QUOTES), time() + 99999999, '/', NULL);
    }
    return $value;
}



add_filter('gform_admin_pre_render','sakka_gform_admin_pre_render',1);
function sakka_gform_admin_pre_render($form){
    if($_GET['page']=='gf_edit_forms') return $form;
    array_push($form['fields'],new GF_Field_Hidden(array('label'=>'REQUEST_URI','id'=>-1)));
    array_push($form['fields'],new GF_Field_Hidden(array('label'=>'HTTP_REFERER','id'=>-2)));

    return $form;
}

add_filter('gform_pre_submission_filter','sakka_gform_pre_submission_filter',1);
function sakka_gform_pre_submission_filter($form){
    array_push($form['fields'],new GF_Field_Hidden(array('label'=>'REQUEST_URI','id'=>-1,'size'=>'medium','type'=>'text')));
    array_push($form['fields'],new GF_Field_Hidden(array('label'=>'HTTP_REFERER','id'=>-2,'size'=>'medium','type'=>'text')));
    return $form;
}