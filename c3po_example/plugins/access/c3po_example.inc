<?php
/**
 * @file
 * Contains \C3POAccessPluginC3POExample.
 */

/**
 * Example access plugin.
 */
class C3POAccessPluginC3POExample extends C3POAccessPlugin {
  /**
   * {@inheritdoc}
   */
  public function settingsForm($form, &$form_state, $conf) {
    $form['settings']['day'] = array(
      '#type' => 'select',
      '#title' => t("Day"),
      '#description' => t("Activate this variant only on this day."),
      '#options' => $this->getDayValues(),
      '#required' => TRUE,
      '#default_value' => $conf['day'],
    );

    return parent::settingsForm($form, $form_state, $conf);
  }

  /**
   * {@inheritdoc}
   */
  public function accessCheck($conf, array $context, array $plugin) {
    // Return true if today is the selected date.
    return (date('N') == $conf['day']);
  }

  /**
   * {@inheritdoc}
   */
  public function summary($conf, $context, $plugin) {
    $dows = $this->getDayValues();

    return t("Today is @day", array('@day' => $dows[$conf['day']]));
  }

  /**
   * Get a simple array to map date values to their day of the week.
   *
   * @return array
   *   DOW values.
   */
  private function getDayValues() {
    return array(
      1 => t("Monday"),
      2 => t("Tuesday"),
      3 => t("Wednesday"),
      4 => t("Thursday"),
      5 => t("Friday"),
      6 => t("Saturday"),
      7 => t("Sunday"),
    );
  }
}

/**
 * Define the plugin.
 */
$plugin = C3POAccessPluginC3POExample::getInstance()->plugin(array(
  'title' => t("C3PO Example Selection Rule"),
  'defaults' => array(
    'day' => 1,
  ),
));
