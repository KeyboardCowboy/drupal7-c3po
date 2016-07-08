<?php
/**
 * @file
 * Contains \C3PORelationshipPluginC3poExample.
 */

/**
 * Custom relationship plugin.
 */
class C3PORelationshipPluginC3poExample extends C3PORelationshipPlugin implements C3PORelationshipPluginInterface {
  /**
   * {@inheritdoc}
   */
  public function create($context, $conf, $empty) {
    return ctools_context_create('entity:node', 1);
  }
}

/**
 * Define the plugin.
 */
$plugin = C3PORelationshipPluginC3poExample::getInstance()->plugin(array(
  'required context' => new ctools_context_required(t('Node'), 'node'),
));
