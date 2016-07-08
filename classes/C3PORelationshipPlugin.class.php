<?php
/**
 * @file
 * Contains \C3PORelationshipPlugin.
 */

/**
 * General class for ctools relationship plugins.
 */
class C3PORelationshipPlugin extends C3POPlugin {
  // Define the plugin type.
  public static $pluginType = 'Relationship';

  /**
   * {@inheritdoc}
   */
  final protected function defaultValues() {
    return array(
      'title' => t("C3PO Relationship"),
      'keyword' => 'c3po',
      'description' => t("Add a new context from an existing context."),
      'context' => 'c3po_ctools_relationship_context_create',
      'edit form' => 'c3po_ctools_relationship_settings_form',
      'no ui' => FALSE,
      'defaults' => array(),
      // 'required context' => new ctools_context_required(t('Simplecontext'), 'simplecontext'),
    );
  }

  /**
   * Get the settings form for the argument plugin.
   *
   * @param array $form
   *   The settings form.
   * @param array $form_state
   *   The settings form_state.
   *
   * @return array
   *   The settings form.
   */
  public function settingsForm($form, &$form_state) {
    return $form;
  }
}

/**
 * Interface C3PORelationshipPluginInterface.
 *
 * Custom relationship plugins should implement this interface.
 */
interface C3PORelationshipPluginInterface {
  /**
   * Create the new context.
   *
   * @param object $context
   *   A context object.
   * @param array $conf
   *   Plugin configuration for the relationship.
   * @param bool $empty
   *   Whether to return an empty context.
   *
   * @return mixed
   */
  public function create($context, $conf, $empty);
}

function c3po_ctools_relationship_context_create($context = NULL, $conf, $empty) {
  $class = C3POPlugin::getPluginClass($conf['name'], C3PORelationshipPlugin::$pluginType);
  return $class::getInstance()->create($context, $conf, $empty);
}

function c3po_ctools_relationship_settings_form($form, &$form_state) {
  $class = C3POPlugin::getPluginClass($form_state['conf']['name'], C3PORelationshipPlugin::$pluginType);
  return $class::getInstance()->settingsForm($form, $form_state);
}
