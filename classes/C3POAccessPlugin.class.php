<?php
/**
 * @file
 * Contains \C3POAccessPlugin.
 */

/**
 * General class to objectify ctools access plugin.
 */
class C3POAccessPlugin extends C3POPlugin {
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
      'settings form submit' => 'c3po_ctools_access_settings_form_submit',
      'summary' => 'c3po_ctools_access_summary',
      'restrictions' => 'c3po_ctools_access_restrictions',
      'get child' => 'c3po_ctools_access_get_child',
      'get children' => 'c3po_ctools_access_get_children',
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
   * Act on the plugin settings.
   *
   * This is optional as the plugin will handle storage of the custom settings.
   *
   * @param array $form
   *   The settings form.
   * @param array $form_state
   *   The settings form_state.
   */
  public function settingsFormSubmit($form, &$form_state) {}

  /**
   * Add restrictions for the matched context.
   *
   * This cannot be overridden in a child class due to the data provided to
   * the callback function.  A custom callback function must be provided in the
   * plugin definition and manually mapped into the custom plugin object.
   *
   * @param array $conf
   *   The configuration settings from the settings form.
   * @param mixed $context
   *   A context object or NULL if none provided.
   */
  public function restrictions($conf, &$context) {}

  /**
   * Load a child plugin.
   *
   * In cases where the plugin has no children, return its own definition.
   *
   * @param array $plugin
   *   The plugin definition.
   * @param string $parent
   *   The parent plugin name.
   * @param string $child
   *   The child plugin name.
   *
   * @return array
   *   The child plugin.
   */
  public function getChild($plugin, $parent, $child) {
    $plugins = $this->getChildren($plugin, $parent);
    return $plugins[$child];
  }

  /**
   * Load all children of a plugin.
   *
   * @param array $plugin
   *   The plugin definition.
   * @param $parent
   *   The parent plugin's name.
   *
   * @return array
   *   The child plugin definitions, including itself.
   */
  public function getChildren($plugin, $parent) {
    $plugins = array();
    $plugins[$parent] = $plugin;
    return $plugins;
  }

}

/**
 * Interface C3POAccessPluginInterface.
 *
 * Custom access plugins should implement this interface.
 */
interface C3POAccessPluginInterface {
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
  public function accessCheck($conf, array $context, array $plugin);

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
  public function summary($conf, $context, $plugin);
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
  $class = C3POPlugin::getPluginClass($form_state['plugin']['name'], C3POAccessPlugin::$pluginType);
  return $class::getInstance()->settingsForm($form, $form_state, $conf);
}

/**
 * Settings form submit handler callback.
 */
function c3po_ctools_access_settings_form_submit($form, &$form_state, $conf) {
  $class = C3POPlugin::getPluginClass($form_state['plugin']['name'], C3POAccessPlugin::$pluginType);
  return $class::getInstance()->settingsFormSubmit($form, $form_state, $conf);
}

/**
 * Summary callback.
 */
function c3po_ctools_access_summary($conf, $context, $plugin) {
  $class = C3POPlugin::getPluginClass($plugin['name'], C3POAccessPlugin::$pluginType);
  return $class::getInstance()->summary($conf, $context, $plugin);
}

/**
 * Restrictions callback.
 */
function c3po_ctools_access_restrictions($conf, &$context) {
  // There is no way to get the plugin name from the data passed into this
  // function, so if you want to implement your own restrictions, you'll need
  // to override the 'restrictions' callback in your custom plugin settings
  // and map it to your own object method.
  C3POAccessPlugin::getInstance()->restrictions($conf, $context);
}

/**
 * Get child callback.
 */
function c3po_ctools_access_get_child($plugin, $parent, $child) {
  $class = C3POPlugin::getPluginClass($parent, C3POAccessPlugin::$pluginType);
  return $class::getInstance()->getChild($plugin, $parent, $child);
}

/**
 * Get children callback.
 */
function c3po_ctools_access_get_children($plugin, $parent) {
  $class = C3POPlugin::getPluginClass($plugin['name'], C3POAccessPlugin::$pluginType);
  return $class::getInstance()->getChildren($plugin, $parent);
}
