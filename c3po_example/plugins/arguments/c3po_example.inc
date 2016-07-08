<?php
/**
 * @file
 * Contains \C3POArgumentPluginC3POExample.
 */

/**
 * Example argument plugin.
 */
class C3POArgumentPluginC3POExample extends C3POArgumentPlugin implements C3POArgumentPluginInterface {
  /**
   * {@inheritdoc}
   */
  public function createContext($arg, $conf, $empty) {
    if ($empty) {
      return ctools_context_create_empty('c3po_example');
    }

    if (empty($arg)) {
      return FALSE;
    }

    return ctools_context_create('c3po_example', $arg, $conf);
  }

}

/**
 * Define the plugin.
 */
$plugin = C3POArgumentPluginC3POExample::getInstance()->plugin(array(
  'title' => t("Month of the Year"),
  'keyword' => 'month',
));
