<?php
/**
 * @file
 * Contains \C3POContextPluginC3POExample.
 */

/**
 * Custom C3PO context plugin.
 */
class C3POContextPluginC3POExample extends C3POContextPlugin {
  /**
   * {@inheritdoc}
   */
  public function create($empty, $data = NULL, $conf = FALSE, $plugin) {
    // Instantiate the context.
    parent::create($empty, $data, $conf, $plugin);

    $context = &$this->context;

    // Return an empty context if that's all we need.
    if ($empty) {
      return $this->context;
    }

    // Set default values.
    $context->data->full = '';
    $context->data->abbrev = '';
    $context->data->int = '';

    // Account for manually added contexts.
    if (isset($data['month'])) {
      $context->argument = $data['month'];
    }

    // Build the context data.
    if (!empty($context->argument)) {
      $month = new stdClass();
      $month->full = $context->argument;
      $month->abbrev = substr($context->argument, 0, 3);
      $month->int = 7;
      $context->data = $month;
    }

    return $context;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm($form, &$form_state) {
    $conf = $form_state['conf'];

    $form['settings']['month'] = array(
      '#type' => 'textfield',
      '#title' => t("Month"),
      '#default_value' => $conf['month'],
    );

    return parent::settingsForm($form, $form_state);
  }

}

/**
 * Define the plugin.
 */
$plugin = C3POContextPluginC3POExample::getInstance()->plugin(array(
  'title' => t("Month"),
  'token subs' => 'c3po_example_month',
));
