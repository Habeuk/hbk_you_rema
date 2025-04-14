<?php

namespace Drupal\hbk_you_rema\Plugin\better_exposed_filters\filter;

use Drupal\more_fields\Plugin\better_exposed_filters\filter\RadiosCheckboxes;
use Drupal\Core\Form\FormStateInterface;
use Drupal\taxonomy\Entity\Term;

/**
 * Default widget implementation.
 *
 * @BetterExposedFiltersFilterWidget(
 *   id = "hbk_you_custom_images_radios",
 *   label = @Translation("hbk_you_rema_images_radios"),
 * )
 */
class DisplayImageMainColor extends RadiosCheckboxes {
  
  public function defaultConfiguration() {
    return parent::defaultConfiguration() + [
      'layoutgenentitystyles_view_options' => [],
      'layoutgenentitystyles_view' => 'hbk_you_rema/hbk_you_rema_images_radios'
    ];
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);
    $form['layoutgenentitystyles_view'] = [
      '#type' => 'hidden',
      '#title' => "Style d'affichage",
      '#default_value' => 'hbk_you_rema/hbk_you_rema_images_radios'
    ];
    unset($form['theme_color']);
    return $form;
  }
  
  /**
   *
   * {@inheritdoc}
   */
  public function exposedFormAlter(array &$form, FormStateInterface $form_state) {
    parent::exposedFormAlter($form, $form_state);
    $filter = $this->handler;
    // Form element is designated by the element ID which is user-
    // configurable.
    $field_id = $filter->options['is_grouped'] ? $filter->options['group_info']['identifier'] : $filter->options['expose']['identifier'];
    //
    $form[$field_id]['#theme'] = 'hbk_you_rema_bef_image_radios';
    $image_url = \Drupal\image\Entity\ImageStyle::load('thumbnail');
    foreach ($form[$field_id]['#options'] as $key => $tid) {
      $term = Term::load($key);
      if ($term) {
        $form[$field_id]['#options'][$key] = $this->buildImage($term, $image_url);
      }
    }
    // dd($form[$field_id]['#options']);
  }
  
  /**
   * Construit l'image à partir d'un champs de terme taxonomique.
   */
  protected function buildImage(Term $term, \Drupal\image\Entity\ImageStyle $image_url) {
    $element = [];
    if ($term) {
      if ($term->hasField('field_pattern_image')) {
        $img = $term->get('field_pattern_image')->getValue();
        if (!empty($img)) {
          $file = \Drupal\file\Entity\File::load($img[0]['target_id']);
          if (!empty($file)) {
            $alt = $img[0]['alt'] ?? $term->label();
            $element = [
              '#markup' => '<img fid="' . $file->id() . '" title="' . $term->label() . '" src="' . $image_url->buildUrl($file->getFileUri()) . '" alt="' . $alt . '" class="img-fluid block-color"/>'
            ];
          }
        }
        elseif ($term->hasField('field_color_hexa')) {
          if (!empty($term->get('field_color_hexa')->color))
            $element = [
              '#theme' => 'hbk_you_rema_color_block',
              '#color' => $term->get('field_color_hexa')->color,
              '#title' => $term->label()
            ];
        }
      }
    }
    return $element;
  }
}