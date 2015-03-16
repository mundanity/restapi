<?php


/**
 * Configuration form for administrative settings.
 *
 */
function restapi_admin_form($form, &$form_state) {

  // TODO: clear menu cache after url_prefix changes. #submit doesn't seem to
  // work here :[
  $form['restapi_url_prefix'] = [
    '#type'          => 'textfield',
    '#title'         => t('URL prefix'),
    '#size'          => 10,
    '#description'   => 'Prepend this value to all URLs defined in hook_restapi_resource(). (e.g. A resource with the URL "some/resource" will  become "$prefix/some/resource").',
    '#default_value' => variable_get('restapi_url_prefix'),
  ];

  $form['restapi_default_auth_class'] = [
    '#type'          => 'textfield',
    '#title'         => t('Default authentication class'),
    '#size'          => 40,
    '#description'   => t('The full name of the class to use as the default authentication class. Defaults to "Drupal\restapi\Auth\DrupalAuthenticationService".'),
    '#default_value' => variable_get('restapi_default_auth_class'),
  ];

  $form['restapi_debug'] = [
    '#type'          => 'checkbox',
    '#title'         => t('Enable debug mode'),
    '#description'   => t('Places the Rest API into debug mode. Additional diagnostic data, such as memory usage and execution time, will be set while in debug mode.'),
    '#default_value' => variable_get('restapi_debug', FALSE),
  ];

  return system_settings_form($form);

}


/**
 * Validation form for administrative settings.
 *
 */
function restapi_admin_form_validate($form, &$form_state) {

  $class  = isset($form_state['values']['restapi_default_auth_class']) ? $form_state['values']['restapi_default_auth_class'] : NULL;
  $prefix = isset($form_state['values']['restapi_url_prefix']) ? $form_state['values']['restapi_url_prefix'] : NULL;

  if ($class && !class_exists($class)) {
    form_set_error('restapi_default_auth_class', t('The class "@class" does not seem to exist, or is not callable.', [
      '@class' => $class,
    ]));
  }

  if ($prefix) {
    $prefix = trim($prefix);
    $prefix = rtrim($prefix, '/');
    $prefix = ltrim($prefix, '/');
    $form_state['values']['restapi_url_prefix'] = $prefix;
  }

}