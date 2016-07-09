<?php
/**
 * @file
 * Contains \C3POArgumentPlugin.
 *
 * @see ctools/ctools_plugin_example/plugins/arguments/simplecontext_arg.inc
 */

/**
 * General class for ctools argument plugins.
 */
class C3POArgumentPlugin extends C3POPlugin {
  // Define the plugin type.
  public static $pluginType = 'Argument';

  /**
   * {@inheritdoc}
   */
  final protected function defaultValues() {
    return array(
      'title' => t("C3PO Ctools Argument"),
      'description' => t("Create a context from a page argument."),
      'keyword' => 'C3POKeyword',
      'context' => 'c3po_ctools_argument_to_context',
      'settings form' => 'c3po_ctools_argument_settings_form',
      'placeholder form' => 'c3po_ctools_argument_placeholder_form',
      'no ui' => FALSE,
    );
  }

  /**
   * Get the settings form for the argument plugin.
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
  public function settingsForm($form, $form_state, $conf) {
    return $form;
  }

  /**
   * Configure the placeholder form for the preview tab.
   *
   * @param array $conf
   *   The plugin configuration.
   *
   * @return array
   *   The form field config.
   */
  public function placeholderForm($conf) {
    $form = array(
      '#type' => 'textfield',
      '#description' => t("Provide a sample argument for the preview."),
    );

    return $form;
  }
}

/**
 * Interface C3POArgumentPluginInterface.
 *
 * Custom argument plugins should implement this interface.
 */
interface C3POArgumentPluginInterface {
  /**
   * Create a context from a URL argument.
   *
   * @param mixed $arg
   *   The arg from the URL or possibly a loaded object.
   * @param array $conf
   *   Configuration settings for the plugin.  Not just values from the
   *     settings form.
   * @param bool $empty
   *   If TRUE, simply return an empty context.
   *
   * @return mixed
   *   An empty ctools_context object, a loaded ctools_context object, or FALSE
   *   if $arg fails validation.
   */
  public function createContext($arg, $conf, $empty);
}

/**
 * Context creation callback.
 */
function c3po_ctools_argument_to_context($arg = NULL, $conf = NULL, $empty = FALSE) {
  $class = C3POPlugin::getPluginClass($conf['name'], C3POArgumentPlugin::$pluginType);
  return $class::getInstance()->createContext($arg, $conf, $empty);
}

/**
 * Settings form callback.
 */
function c3po_ctools_argument_settings_form($form, &$form_state, $conf) {
  $name = $form_state['page']->subtask['subtask']->temporary_arguments[$form_state['keyword']]['name'];
  $class = C3POPlugin::getPluginClass($name, C3POArgumentPlugin::$pluginType);
  return $class::getInstance()->settingsForm($form, $form_state, $conf);
}

/**
 * Placeholder form callback.
 */
function c3po_ctools_argument_placeholder_form($conf) {
  $class = C3POPlugin::getPluginClass($conf['name'], C3POArgumentPlugin::$pluginType);
  return $class::getInstance()->placeholderForm($conf);
}
