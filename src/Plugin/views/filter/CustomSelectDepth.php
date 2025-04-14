<?php

namespace Drupal\hbk_you_rema\Plugin\views\filter;

use Drupal\more_fields\Plugin\views\filter\MoreFieldsCheckboxList;
use Drupal\taxonomy\Plugin\views\filter\TaxonomyIndexTidDepth;
use Drupal\Core\Form\FormStateInterface;
use Drupal\taxonomy\Entity\Term;

/**
 * Filter by term id.
 *
 * @deprecated car pas utiliser à supprimer à la fin du projet.
 * @ingroup views_filter_handlers (pas sur...)
 *
 * @ViewsFilter("hbk_you_custom_custom_select_depth")
 */
class CustomSelectDepth extends TaxonomyIndexTidDepth {
  
  /**
   *
   * @return array
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    //
    $options['display_image'] = [
      'default' => false
    ];
    return $options;
  }
  
  protected function valueForm(&$form, FormStateInterface $form_state) {
    if ($exposed = $form_state->get('exposed')) {
      $image_url = \Drupal\image\Entity\ImageStyle::load('thumbnail');
      /**
       *
       * @var \Drupal\Core\Render\Renderer $renderer
       */
      $renderer = \Drupal::service('renderer');
      $vocabulary = $this->vocabularyStorage->load($this->options['vid']);
      $options = [];
      $query = \Drupal::entityQuery('taxonomy_term')->accessCheck(TRUE)->
      // @todo Sorting on vocabulary properties -
      // https://www.drupal.org/node/1821274.
      sort('weight')->sort('name')->addTag('taxonomy_term_access');
      if (!$this->currentUser->hasPermission('administer taxonomy')) {
        $query->condition('status', 1);
      }
      if ($this->options['limit']) {
        $query->condition('vid', $vocabulary->id());
      }
      $customTerms = [
        676,
        88,
        37,
        94
      ];
      $query->condition('tid', $customTerms, 'IN');
      $terms = Term::loadMultiple($query->execute());
      foreach ($terms as $term) {
        // $contents = $this->buildImage($term, $image_url);
        $options[$term->id()] = $term->id();
        // $options[$term->id()] =
        // \Drupal::service('entity.repository')->getTranslationFromContext($term)->label();
      }
      $default_value = (array) $this->value;
      $identifier = $this->options['expose']['identifier'];
      
      $form['value'] = [
        '#type' => 'select',
        '#title' => $this->options['limit'] ? $this->t('Select terms from vocabulary @voc', [
          '@voc' => $vocabulary->label()
        ]) : $this->t('Select terms'),
        '#multiple' => TRUE,
        '#options' => $options,
        '#size' => min(9, count($options)),
        '#default_value' => $default_value
      ];
      
      $user_input = $form_state->getUserInput();
      if ($exposed && isset($identifier) && !isset($user_input[$identifier])) {
        $user_input[$identifier] = $default_value;
        $form_state->setUserInput($user_input);
      }
    }
    else
      parent::valueForm($form, $form_state);
  }
  
  /**
   * Construit l'image à partir d'un champs de terme taxonomique.
   */
  protected function buildImage(Term $term, \Drupal\image\Entity\ImageStyle $image_url) {
    $elements = [
      '#type' => 'html_tag',
      '#tag' => 'div',
      '#attributes' => [
        'class' => [
          'coloris',
          'd-flex',
          'flex-wrap'
        ]
      ]
    ];
    if ($term) {
      if ($term->hasField('field_pattern_image')) {
        $img = $term->get('field_pattern_image')->getValue();
        if (!empty($img)) {
          $file = \Drupal\file\Entity\File::load($img[0]['target_id']);
          if (!empty($file)) {
            $alt = $img[0]['alt'] ?? $term->label();
            $elements[] = [
              '#markup' => '<img fid="' . $file->id() . '" title="' . $term->label() . '" src="' . $image_url->buildUrl($file->getFileUri()) . '" alt="' . $alt . '" class="img-fluid block-color"/>'
            ];
          }
        }
        elseif ($term->hasField('field_color_hexa')) {
          if (!empty($term->get('field_color_hexa')->color))
            $elements[] = [
              '#type' => 'html_tag',
              '#tag' => 'span',
              '#attributes' => [
                'style' => 'background-color:' . $term->get('field_color_hexa')->color . ';',
                'class' => [
                  'block-color'
                ]
              ]
            ];
        }
      }
    }
    return $elements;
  }
  
  /**
   *
   * {@inheritdoc}
   * @see \Drupal\views\Plugin\views\filter\FilterPluginBase::buildOptionsForm()
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);
    $form['display_image'] = [
      '#type' => 'checkbox',
      '#title' => "display_image",
      '#default_value' => $this->options['display_image'],
      '#description' => "Specifique au vocabulaire couleur"
    ];
  }
}