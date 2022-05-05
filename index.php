<?php
/*
Plugin Name: WP JWT
Description: Esse plugin faz o login via JWT e permite utilizar requisições autenticadas sem cookies.
*/
include('jwt.php');

function wb_api_init() {
  $namespace = 'wpjwt/v1';

  register_rest_route($namespace, '/login', array(
    'methods' => 'POST',
    'callback' => 'wb_api_ep_login'
  ));

  register_rest_route($namespace, '/validate', array(
    'methods' => 'GET',
    'callback' => 'wb_api_ep_validate'
  ));
}

function wb_api_ep_validate($req) {
  $array = array('valid' => false);

  $params = $req->get_params();

  if(!empty($params['jwt'])) {
    $jwt = new JWT();

    echo $info = $jwt->validate($params['jwt']);

    if($info && !empty($info->id)) {
      $array['valid'] = true;
    }
  }

  return $array;
}

function wb_api_ep_login($req) {
  $array = array('logged' => false);

  $params = $req->get_params();

  $result = wp_signon(array(
      'user_login' => $params['username'],
      'user_password' => $params['password']
  ));

  if(isset($result->data)){
      $jwt = new JWT();

      $token = $jwt->create( array('id' => $result->data->ID) );

      $array['logged'] = true;
      $array['token'] = $token;

  } else {

  }

  return $array;
  
/*
  $array = array(
    'teste' => '123',
    'params' => $params
  );

  return $array; */
}

add_action('rest_api_init', 'wb_api_init');