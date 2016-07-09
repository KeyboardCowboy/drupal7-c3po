<?php
/**
 * @file
 * Contains \C3POPlugin.
 */

/**
 * General class to manage ctools plugins as objects.
 */
abstract class C3POPlugin {
  use C3POPluginTrait;

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
