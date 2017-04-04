<?php


/**
 * Configuration form for administrative settings.
 *
 */
function restapi_admin_form($form, &$form_state) {

  $form['restapi_url_prefix'] = [
    '#type'          => 'textfield',
    '#title'         => t('URL prefix'),
    '#size'          => 15,
    '#description'   => t('Prepend this value to all URLs defined in hook_restapi_resource(). (e.g. A resource with the URL "some/resource" will  become "$prefix/some/resource").'),
    '#default_value' => variable_get('restapi_url_prefix'),
  ];

  $form['restapi_api_name'] = [
    '#type'          => 'textfield',
    '#title'         => t('API name'),
    '#size'          => 15,
    '#description'   => t('Used in the header that specifies the API version. (e.g. An API with the name "API-Name" will add the header "X-API-Name-Version").'),
    '#default_value' => variable_get('restapi_api_name', 'API'),
  ];

  $form['restapi_current_version'] = [
    '#type'             => 'textfield',
    '#title'            => t('Current API version'),
    '#size'             => 15,
    '#description'      => t('Specifies the current API version, which is used by default if a version is not provided. This should also be the most recent API version.)'),
    '#default_value'    => variable_get('restapi_current_version', 1),
    '#element_validate' => ['element_validate_integer_positive'],
  ];

  $form['restapi_minimum_version'] = [
    '#type'             => 'textfield',
    '#title'            => t('Minimum API version'),
    '#size'             => 15,
    '#description'      => t('Specifies the minimum API version that clients may request.)'),
    '#default_value'    => variable_get('restapi_minimum_version', 0),
    '#element_validate' => ['element_validate_integer_positive'],
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

  // We want to include our custom submission handler as well, to execute after
  // variables are saved via system_settings_form_submit().
  $form = system_settings_form($form);
  $form['#submit'][] = __FUNCTION__ . '_submit';

  return $form;

}


/**
 * Validation handler for restapi administrative settings.
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

  if ($prefix != variable_get('restapi_url_prefix')) {
    $form_state['storage']['restapi_url_prefix_changed'] = TRUE;

    $prefix = trim($prefix);
    $prefix = rtrim($prefix, '/');
    $prefix = ltrim($prefix, '/');
    $form_state['values']['restapi_url_prefix'] = $prefix;
  }

}


/**
 * Submission handler for restapi administrative settings.
 *
 */
function restapi_admin_form_submit($form, &$form_state) {

  // Ensure the menu system picks up the changes due to the prefix update.
  if (!empty($form_state['storage']['restapi_url_prefix_changed'])) {
    menu_rebuild();
  }

}
