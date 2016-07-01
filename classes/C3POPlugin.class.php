<?php
/**
 * @file
 * Contains \C3POPlugin.
 */

/**
 * General class to manage ctools plugins as objects.
 */
abstract class C3POPlugin {
  // Store the plugin definition.
  protected $settings = array();

  // Set the plugin type.
  public static $pluginType;

  /**
   * C3POPlugin constructor.
   */
  public function __construct() {}

  /**
   * Build the plugin definition array.
   *
   * The plugin file should set the variable $plugin to the value returned by
   * this method.
   *
   * @param array $settings
   *   Custom definition settings for this plugin.
   *
   * @return array
   *   A properly formatted $plugin definition.
   */
  public function plugin(array $settings) {
    $this->settings = $settings + $this->defaultValues();

    return $this->settings;
  }

  /**
   * Generate the class name given the pane subtype.
   *
   * @param string $name
   *   The machine name of the plugin.
   *
   * @return string
   *   The appropriate class name to extend this class.
   */
  public static function getPluginClass($name, $plugin_type) {
    $class = str_replace('_', ' ', $name);
    $class = ucwords(strtolower($class));
    $class = str_replace(' ', '', $class);
    $class = "C3PO{$plugin_type}Plugin{$class}";

    if (class_exists($class)) {
      return $class;
    }
    else {
      $args = array('%class' => $class);
      drupal_set_message(t("Class %class was not found. Make sure your plugin class name matches the pattern 'C3PO{$plugin_type}Plugin[filename]'.", $args), 'error', FALSE);
      return NULL;
    }
  }

  /**
   * Instantiate and return the object.
   *
   * For classes that implement only static methods, such as custom module
   * functionality, this method will allow those static methods to be written
   * as dynamic classes and called via this static method.  This allows them
   * to be overridden for PhpUnit testing.
   *
   * @return static
   *   An instance of this class.
   */
  public static function getInstance() {
    static $instance;
    if (!isset($instance)) {
      $instance = new static();
    }

    return $instance;
  }

  /**
   * Define the default values for a plugin.
   *
   * These can be overridden by a custom plugin.
   *
   * @return array
   *   The default plugin settings.
   */
  abstract protected function defaultValues();

}
