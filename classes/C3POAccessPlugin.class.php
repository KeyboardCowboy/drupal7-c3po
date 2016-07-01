<?php
/**
 * @file
 * Contains \C3POAccessPlugin.
 */

/**
 * General class to objectify ctools access plugin.
 */
abstract class C3POAccessPlugin extends C3POPlugin {
  // Define the plugin type.
  public static $pluginType = 'Access';

  /**
   * {@inheritdoc}
   */
  final protected function defaultValues() {
    return array(
      'title' => t("C3PO Ctools Access Plugin"),
      'description' => t("C3PO Ctools access plugin description."),
      'callback' => 'c3po_ctools_access_check',
      'settings form' => 'c3po_ctools_access_settings_form',
      'summary' => 'c3po_ctools_access_summary',
      'defaults' => array(),
      'all contexts' => TRUE,
    );
  }

  /**
   * Get the settings form for the access plugin.
   *
   * @param array $form
   *   The settings form.
   * @param array $form_state
   *   The settings form_state.
   * @param array|NULL $conf
   *   The default values for the settings form.
   *
   * @return array
   *   The settings form.
   */
  public function settingsForm($form, &$form_state, $conf) {
    return $form;
  }

  /**
   * Perform the logic to determine whether a variant is active.
   *
   * @param array|NULL $conf
   *   The values from the settings form.
   * @param array $context
   *   The available contexts.
   * @param $plugin
   *   The plugin definition.
   *
   * @return bool
   *   TRUE to activate the variant.
   */
  abstract public function accessCheck($conf, array $context, array $plugin);

  /**
   * Explain the conditions that will return TRUE.
   *
   * The summary should complete the statement:
   *   "This panel will be selected if..."
   *
   * @param $conf
   * @param $context
   * @param $plugin
   *
   * @return mixed
   */
  abstract public function summary($conf, $context, $plugin);

}

/**
 * Access check callback.
 */
function c3po_ctools_access_check($conf, $context, $plugin) {
  $class = C3POPlugin::getPluginClass($plugin['name'], C3POAccessPlugin::$pluginType);
  return $class::getInstance()->accessCheck($conf, $context, $plugin);
}

/**
 * Settings form callback.
 */
function c3po_ctools_access_settings_form($form, &$form_state, $conf) {
  $name = $form_state['plugin']['name'];

  if (!empty($name)) {
    $class = C3POPlugin::getPluginClass($name, C3POAccessPlugin::$pluginType);
    return $class::getInstance()
                 ->settingsForm($form, $form_state, $conf);
  }
  else {
    return $form;
  }
}

/**
 * Summary callback.
 */
function c3po_ctools_access_summary($conf, $context, $plugin) {
  $class = C3POPlugin::getPluginClass($plugin['name'], C3POAccessPlugin::$pluginType);
  return $class::getInstance()->summary($conf, $context, $plugin);
}
