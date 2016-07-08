<?php
/**
 * @file
 * Contains \C3POAccessPluginC3POExample.
 */

/**
 * Example access plugin.
 */
class C3POAccessPluginC3poDowAccess extends C3POAccessPlugin implements C3POAccessPluginInterface {
  /**
   * {@inheritdoc}
   */
  public function settingsForm($form, &$form_state, $conf) {
    $form['settings']['day'] = array(
      '#type' => 'checkboxes',
      '#title' => t("Day"),
      '#description' => t("Activate this variant only on these days."),
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
    $active_days = $this->getSelectedDays($conf);

    // Return true if today is one of the selected days.
    return (in_array(date('N'), $active_days));
  }

  /**
   * {@inheritdoc}
   */
  public function summary($conf, $context, $plugin) {
    $active_days = $this->getSelectedDays($conf);
    $all_days = $this->getDayValues();
    $days = array();

    foreach (array_keys($active_days) as $day_int) {
      $days[] = $all_days[$day_int];
    }

    return t("Today is @days", array('@days' => implode(', ', $days)));
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

  /**
   * Get an array of days that are checked.
   *
   * @param array $conf
   *   The configuration settings from the settings form.
   *
   * @return array
   *   Active days.
   */
  private function getSelectedDays(array $conf) {
    return array_filter($conf['day']);
  }
}

/**
 * Define the plugin.
 */
$plugin = C3POAccessPluginC3poDowAccess::getInstance()->plugin(array(
  'title' => t("Day of the Week"),
  'description' => t("Restrict access based on the day of the week."),
  'defaults' => array(
    'day' => 1,
  ),
));
