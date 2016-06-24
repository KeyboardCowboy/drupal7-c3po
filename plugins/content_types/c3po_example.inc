<?php
/**
 * @file
 * Example ctools content type plugin using the c3po objectifier.
 */

/**
 * Class AlCtoolsContentTypeAlExample.
 *
 * The overriding class name must be C3POContentTypePlugin[filename]. For
 * example, this filename is 'c3po_example.inc' so the class should be named
 * C3POContentTypePluginC3poExample.
 */
class C3POContentTypePluginC3poExample extends C3POContentTypePlugin {
  /**
   * {@inheritdoc}
   */
  public function editForm($form, &$form_state) {
    $conf = $form_state['conf'];

    // Use the machine name of the plugin (the filename) as the root of your
    // settings and there is no need to explicitly call the submit handler.
    $form['c3po_example']['custom_text'] = array(
      '#type' => 'textfield',
      '#title' => t('Custom Setting'),
      '#default_value' => $conf['custom_text'],
    );

    return parent::editForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function render($subtype, array $conf, array $args, $context) {
    $block = new stdClass();
    $block->title = 'C3PO Block';
    $block->content = t("Your setting") . ': ' . filter_xss_admin($conf['custom_text']);

    return $block;
  }
}

/**
 * Define the plugin.
 *
 * This must occur after the class declaration.
 */
$plugin = C3POContentTypePluginC3poExample::getInstance()->plugin(array(
  'title' => t("C3PO Example Content Type Plugin"),
  'description' => t("Example of how to build a ctools content type plugin."),
  'category' => t('Examples'),
  'defaults' => array(
    'custom_text' => '',
  ),
));
