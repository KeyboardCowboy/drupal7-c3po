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

/**
 * General class to extend ctools context objects.
 */
abstract class C3POCtoolsContext {
  use C3POPluginTrait;

  /**
   * {@inheritdoc}
   */
  //public function __construct($type = 'none', $data = NULL) {
  //  parent::ctools_context($type, $data);
  //}

  public function tokenReplaceConvertList($type) {
    $list = array();

    $tokens = token_info();
    if (isset($tokens['tokens'][$type])) {
      foreach ($tokens['tokens'][$type] as $id => $info) {
        if (!isset($list[$id])) {
          $list[$id] = $info['name'];
        }
      }
    }

    return $list;
  }

  public function tokenReplaceConvert($token_type, $context, $token_name, $options) {
    $token_value = '';

    $values = token_generate($token_type, array($token_name => $token_name), array($token_type => $context->data), $options);
    if (isset($values[$token_name])) {
      $token_value = $values[$token_name];
    }

    return $token_value;
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
