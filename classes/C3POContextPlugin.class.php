<?php

class C3POContextPlugin extends C3POCtoolsContext {
  // Define the plugin type.
  public static $pluginType = 'Context';

  protected $context;

  /**
   * {@inheritdoc}
   */
  final protected function defaultValues() {
    return array(
      'title' => t("C3PO Context Plugin"),
      'description' => t("C3PO Ctools context plugin."),
      'context' => 'c3po_ctools_context_plugin_create',
      'edit form' => 'c3po_ctools_context_plugin_settings_form',
      'defaults' => array(),
      'convert list' => 'c3po_ctools_context_plugin_convert_list',
      'convert' => 'c3po_ctools_context_plugin_convert',
      'placeholder form' => array(
        '#type' => 'textfield',
        '#description' => t("Provide a sample argument for the context."),
      ),
      'get child' => 'c3po_ctools_context_plugin_get_child',
      'get children' => 'c3po_ctools_context_plugin_get_children',
      'no ui' => FALSE,
      'keyword' => 'c3po',
      'token subs' => '',
    );
  }

  /**
   * @param bool $empty
   *   If true, just return an empty context.
   * @param array $data
   *   The data parameter of the context object.
   * @param bool $conf
   *   Whether the context is being loaded within the admin configuration.
   * @param array $plugin
   *   The plugin definition for the context.
   *
   * @return $this
   *   A ctools context.
   */
  public function create($empty, $data = NULL, $conf = FALSE, $plugin) {
    $this->context = new ctools_context('c3po_example', $data);

    // The filename minus the extension.
    $this->context->plugin = $plugin['name'];
    $this->context->keyword = $plugin['keyword'];

    // Setup the data param for context info.
    if (is_string($data)) {
      $this->context->title = $data;
      $this->context->argument = $data;
    }

    $this->context->data = new stdClass();

    return $this->context;
  }

  public function settingsForm($form, &$form_state) {
    $form['settings']['#tree'] = TRUE;
    return $form;
  }

  public function settingsFormValidate($form, &$form_state) {}

  public function settingsFormSubmit($form, &$form_state) {
    foreach ($form_state['values']['settings'] as $field => $value) {
      $form_state['conf'][$field] = $value;
    }
  }

  public function convertList($plugin) {
    $list = array();

    if (!empty($plugin['token subs'])) {
      $list = $this->tokenReplaceConvertList($plugin['token subs']);
    }

    return $list;
  }

  public function convert($context, $type, $options) {
    $value = '';

    if (!empty($this->settings['token subs'])) {
      $value = $this->tokenReplaceConvert($this->settings['token subs'], $context, $type, $options);
    }

    return $value;
  }

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
 * Context create callback.
 */
function c3po_ctools_context_plugin_create($empty, $data = NULL, $conf = FALSE, $plugin) {
  $key = isset($data['keyword']) ? $data['keyword'] : '';

  if (empty($key) && !empty($conf['keyword'])) {
    $key = $conf['keyword'];
  }

  $class = C3POPlugin::getPluginClass($plugin['name'], C3POContextPlugin::$pluginType);
  return $class::getInstance($key)->create($empty, $data, $conf, $plugin);
}

/**
 * Settings form callback.
 */
function c3po_ctools_context_plugin_settings_form($form, &$form_state) {
  $class = C3POPlugin::getPluginClass($form_state['conf']['name'], C3POContextPlugin::$pluginType);
  return $class::getInstance()->settingsForm($form, $form_state);
}

/**
 * Settings form validation callback.
 */
function c3po_ctools_context_plugin_settings_form_validate($form, &$form_state) {
  $class = C3POPlugin::getPluginClass($form_state['conf']['name'], C3POContextPlugin::$pluginType);
  return $class::getInstance()->settingsFormValidate($form, $form_state);
}

/**
 * Settings form submit callback.
 */
function c3po_ctools_context_plugin_settings_form_submit($form, &$form_state) {
  $class = C3POPlugin::getPluginClass($form_state['conf']['name'], C3POContextPlugin::$pluginType);
  return $class::getInstance()->settingsFormSubmit($form, $form_state);
}

/**
 * Convert list callback.
 */
function c3po_ctools_context_plugin_convert_list($plugin) {
  $class = C3POPlugin::getPluginClass($plugin['name'], C3POContextPlugin::$pluginType);
  return $class::getInstance()->convertList($plugin);
}

/**
 * Placeholder conversion callback.
 */
function c3po_ctools_context_plugin_convert($context, $type, $options = array()) {
  $class = C3POPlugin::getPluginClass($context->plugin, C3POContextPlugin::$pluginType);
  return $class::getInstance()->convert($context, $type, $options);
}

/**
 * Child plugin callback.
 */
function c3po_ctools_context_plugin_get_child($plugin, $parent, $child) {
  $class = C3POPlugin::getPluginClass($parent, C3POContextPlugin::$pluginType);
  return $class::getInstance()->getChild($plugin, $parent, $child);
}

/**
 * Get plugin children callback.
 */
function c3po_ctools_context_plugin_get_children($plugin, $parent) {
  $class = C3POPlugin::getPluginClass($parent, C3POContextPlugin::$pluginType);
  return $class::getInstance()->getChildren($plugin, $parent);
}
