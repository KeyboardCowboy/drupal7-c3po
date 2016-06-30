<?php
/**
 * @file
 * Contains \C3POContentTypePlugin.
 */

/**
 * General class to assist in objectifying ctools content_type plugins.
 */
abstract class C3POContentTypePlugin extends C3POPlugin {
  // Define the plugin type.
  public static $pluginType = 'ContentType';

  /**
   * {@inheritdoc}
   */
  final protected function defaultValues() {
    return array(
      'icon' => drupal_get_path('module', 'c3po') . '/images/c3po.png',
      'title' => t("C3PO Ctools Content Type"),
      'description' => t("C3PO ctools content type description."),
      'all contexts' => TRUE,
      'single' => TRUE,
      'category' => t("C3PO Content Types"),
      'render callback' => "c3po_ctools_content_type_render",
      'edit form' => "c3po_ctools_content_type_edit_form",
      'defaults' => array(),
      'admin title' => "c3po_ctools_content_type_admin_title",
      'admin info' => "c3po_ctools_content_type_admin_info",
    );
  }

  /**
   * Configure the admin title of the pane.
   *
   * @param string $subtype
   *   The machine name of the pane.
   * @param array $conf
   *   The settings from the pane settings form.
   * @param mixed $context
   *   An array of available contexts.
   *   A single context object if there is only one available.
   *   NULL if there are no contexts.
   *
   * @return string
   *   The admin pane title.
   */
  public function adminTitle($subtype, array $conf, $context = NULL) {
    $output = $this->settings['title'];

    // Allow the configurable title to override the pane admin title.
    if ($conf['override_title'] && !empty($conf['override_title_text'])) {
      $output = filter_xss_admin($conf['override_title_text']);
    }

    return $output;
  }

  /**
   * Add the custom settings to the pane admin info.
   *
   * @param string $subtype
   *   The plugin subtype.
   * @param array $conf
   *   The configuration settings from the edit form.
   * @param mixed $context
   *   An array of available contexts.
   *   A single context object if there is only one available.
   *   NULL if there are no contexts.
   *
   * @return \stdClass
   *   The admin info object.
   *
   * @todo: Break out a recursive function so we can get settings more than 2 levels deep.
   */
  public function adminInfo($subtype, array $conf, $context = NULL) {
    $info = new stdClass();

    // Get plugin meta info.
    $plugin = is_array($subtype) ? $subtype : ctools_get_content_type($subtype);

    // Try to pull the edit form for the plugin so we can get field info.
    if ($function = ctools_plugin_get_function($plugin, 'edit form')) {
      $form = array();
      $form_state = array(
        'conf' => $plugin['defaults'],
        'subtype_name' => $subtype,
      );
      $form = $function($form, $form_state);

      $settings = array();

      if (isset($form[$subtype])) {
        foreach (element_children($form[$subtype]) as $child) {
          $item = &$form[$subtype][$child];

          // Loop through the form pulling out field titles and their
          // corresponding settings values.
          if (isset($conf[$child])) {
            // If this is a fieldset, nest the list of settings.
            if ($item['#type'] == 'fieldset') {
              $subsettings = array();
              foreach (element_children($item) as $grandchild) {
                if (!empty($item[$grandchild]['#title'])) {
                  $prefix = isset($item[$grandchild]['#field_prefix']) ? $item[$grandchild]['#field_prefix'] : '';
                  $suffix = isset($item[$grandchild]['#field_suffix']) ? $item[$grandchild]['#field_suffix'] : '';
                  $subsettings[] = $item[$grandchild]['#title'] . ': <strong>' . $prefix . ' ' . $conf[$child][$grandchild] . ' ' . $suffix . '</strong>';
                }
              }

              $settings[] = $item['#title'] . ': ' . theme('item_list', array('items' => $subsettings));
            }
            else {
              if (isset($item['#title'])) {
                $prefix = isset($item['#field_prefix']) ? $item['#field_prefix'] : '';
                $suffix = isset($item['#field_suffix']) ? $item['#field_suffix'] : '';
                $settings[] = $item['#title'] . ': <strong>' . $prefix . ' ' . $conf[$child] . ' ' . $suffix . '</strong>';
              }
            }
          }
        }
      }
    }

    // Build the admin info object.
    $info->title = t('Pane Settings');
    if (!empty($settings)) {
      $info->content = theme('item_list', array('items' => $settings));
    }
    else {
      $info->content = t("No custom settings.");
    }

    return $info;
  }

  /**
   * Edit form callback.
   */
  public function editForm($form, &$form_state, $subtype) {
    // Preset a 'container' for custom settings.  This allows us to automate
    // things like the admin settings display and the edit form submit handler.
    $form[$subtype]['#tree'] = TRUE;

    // Hide the text field when we are not overriding the title.
    $form['override_title_text']['#states'] = array(
      'invisible' => array(
        ":input[name='override_title']" => array('checked' => FALSE),
      ),
    );

    return $form;
  }

  /**
   * Edit form validate callback.
   */
  public function editFormValidate($form, &$form_state, $subtype) {}

  /**
   * Edit form submit callback.
   */
  public function editFormSubmit($form, &$form_state, $subtype) {
    if (isset($form_state['values'][$subtype])) {
      foreach ($form_state['values'][$subtype] as $name => $value) {
        $form_state['conf'][$name] = $value;
      }
    }
  }

  /**
   * Render callback.
   *
   * @param string $subtype
   *   The plugin machine name.
   * @param array $conf
   *   The settings from the pane settings form.
   * @param array $args
   *   The URL arguments as configured in the panels setup.
   * @param mixed $context
   *   An array of available contexts.
   *   A single context object if there is only one available.
   *   NULL if there are no contexts.
   *
   * @return \stdClass
   *   An object containing at least the following properties:
   *    - title
   *    - content
   */
  abstract public function render($subtype, array $conf, array $args, $context);

  /**
   * Extract a specific context from an array of possible contexts.
   *
   * @param mixed $contexts
   *   The contexts from a content_type rendering callback.
   * @param array|string $types
   *   The type(s) of context to find.
   *
   * @return object|null
   *   The context or NULL if not found.
   */
  public function getContext($contexts, $types) {
    $types = (array) $types;

    foreach ($contexts as $context) {
      if (is_array($context->type) && array_intersect($context->type, $types)) {
        return $context;
      }
      elseif (is_string($context->type) && in_array($context->type, $types)) {
        return $context;
      }
    }

    return NULL;
  }

}

/**
 * Render callback.
 */
function c3po_ctools_content_type_render($subtype, $conf, $args, $context) {
  $class = C3POPlugin::getSubtypeClass($subtype, C3POContentTypePlugin::$pluginType);
  return $class::getInstance()->render($subtype, $conf, $args, $context);
}

/**
 * Edit form callback.
 */
function c3po_ctools_content_type_edit_form($form, &$form_state) {
  $subtype = isset($form_state['subtype_name']) ? $form_state['subtype_name'] : '';
  $class = C3POPlugin::getSubtypeClass($subtype, C3POContentTypePlugin::$pluginType);
  return $class::getInstance()->editForm($form, $form_state, $subtype);
}

/**
 * Edit form validate callback.
 */
function c3po_ctools_content_type_edit_form_validate($form, &$form_state) {
  $subtype = isset($form_state['subtype_name']) ? $form_state['subtype_name'] : '';
  $class = C3POPlugin::getSubtypeClass($subtype, C3POContentTypePlugin::$pluginType);
  return $class::getInstance()->editFormValidate($form, $form_state, $subtype);
}

/**
 * Edit form submit callback.
 */
function c3po_ctools_content_type_edit_form_submit($form, &$form_state) {
  $subtype = isset($form_state['subtype_name']) ? $form_state['subtype_name'] : '';
  $class = C3POPlugin::getSubtypeClass($subtype, C3POContentTypePlugin::$pluginType);
  return $class::getInstance()->editFormSubmit($form, $form_state, $subtype);
}

/**
 * Admin title callback.
 */
function c3po_ctools_content_type_admin_title($subtype, $conf, $context = NULL) {
  $class = C3POPlugin::getSubtypeClass($subtype, C3POContentTypePlugin::$pluginType);
  return $class::getInstance()->adminTitle($subtype, $conf, $context);
}

/**
 * Admin info callback.
 */
function c3po_ctools_content_type_admin_info($subtype, $conf, $context = NULL) {
  $class = C3POPlugin::getSubtypeClass($subtype, C3POContentTypePlugin::$pluginType);
  return $class::getInstance()->adminInfo($subtype, $conf, $context);
}
