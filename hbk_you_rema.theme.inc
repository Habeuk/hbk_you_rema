<?php
use Drupal\Core\Template\Attribute;
use Drupal\Core\Render\Element;

function template_preprocess_hbk_you_rema_bef_image_radios(&$vars) {
  /**
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   */
  $moduleHandler = \Drupal::moduleHandler();
  $moduleHandler->loadInclude('better_exposed_filters', "inc", 'includes/better_exposed_filters.theme');
  __you__template_preprocess_bef_radios($vars);
  // template_preprocess_bef_radios($vars);
}

/**
 * Prepares variables for bef-radios template.
 *
 * Default template: bef-radios.html.twig.
 *
 * @param array $variables
 *        An associative array containing:
 *        - element: An associative array containing the exposed form element.
 */
function __you__template_preprocess_bef_radios(array &$variables) {
  $element = &$variables['element'];
  
  // Create new wrapper attributes since the element attributes will be used
  // on the fieldset (@see template_preprocess_fieldset).
  $variables['wrapper_attributes'] = new Attribute();
  
  $variables['children'] = Element::children($element);
  $variables['display_inline'] = $element['#bef_display_inline'] ?? FALSE;
  
  // Set element name.
  $variables['attributes']['name'] = $element['#name'];
  
  // Handle nested radio buttons.
  if (!empty($variables['element']['#bef_nested'])) {
    __you__bef_preprocess_nested_elements($variables);
  }
}

function __you__bef_preprocess_nested_elements(array &$variables) {
  // Provide a hierarchical info on the element children for the template to
  // render as a nested <ul>. Views prepends '-' characters for each level of
  // depth in the vocabulary. Store that information, but remove the hyphens as
  // we don't want to display them.
  $variables['is_nested'] = TRUE;
  $variables['depth'] = [];
  $variables['element'];
  foreach ($variables['children'] as $child) {
    $variables['depth'][$child] = 0;
  }
}