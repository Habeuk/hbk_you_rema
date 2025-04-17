<?php

namespace Drupal\hbk_you_rema\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\StringFormatter;
use Drupal\Core\Form\FormStateInterface;


/**
 * Plugin implementation of the 'text_long, text_with_summary' formatter.
 *
 * @FieldFormatter(
 *   id = "sep_text_long_formatter",
 *   label = @Translation("sep text long formatter"),
 *   field_types = {
 *     "string"
 *   }
 * )
 */
class SepTextFormatter extends StringFormatter {

  /**
   *
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'flexible_settings' => [
        "left_classes" => "fw-800",
        "separator" => "pipe",
        "right_classes" => "",
        "element_classes" => "",
        'show_separator' => False,
      ],
    ] + parent::defaultSettings();
  }

  /**
   *
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    // dd(array_key_exists("separator", $this->getsetting("flexible_settings"))
    //   ? $this->getsetting("flexible_settings")["separator"]
    //   : $this->defaultSettings()['flexible_settings']["separator"]);
    $form1 =  [
      'flexible_settings' =>
      [
        '#type' => 'details',
        '#title' => $this->t('flexibles Settings'),
        '#tree' => TRUE,
        '#open' => FALSE,
        'separator' => [
          "#title" => $this->t("Separator"),
          "#type" => "radios",
          "#options" => [
            "pipe" => $this->t("Pipe"),
            "accent" => $this->t("Accent"),
          ],
          "#default_value" => array_key_exists("separator", $this->getsetting("flexible_settings"))
            ? $this->getsetting("flexible_settings")["separator"]
            : $this->defaultSettings()['flexible_settings']["separator"]
        ],
        'show_separator' => [
          '#title' => t('Show separator'),
          '#type' => 'checkbox',
          '#default_value' => array_key_exists("show_separator", $this->getsetting("flexible_settings"))
            ? $this->getsetting("flexible_settings")["show_separator"]
            : $this->defaultSettings()['flexible_settings']["show_separator"]
        ],
        'element_classes' => [
          '#title' => t('element classes'),
          '#type' => 'textfield',
          '#default_value' => array_key_exists("element_classes", $this->getSetting("flexible_settings"))
            ? $this->getsetting("flexible_settings")["element_classes"]
            : $this->defaultSettings()["flexible_settings"]["element_classes"]
        ],
        'left_classes' => [
          '#title' => t('left side classes'),
          '#type' => 'textfield',
          '#default_value' => array_key_exists("left_classes", $this->getSetting("flexible_settings"))
            ? $this->getsetting("flexible_settings")["left_classes"]
            : $this->defaultSettings()["flexible_settings"]["left_classes"]
        ],
        'right_classes' => [
          '#title' => t('right side classes'),
          '#type' => 'textfield',
          '#default_value' => array_key_exists("left_classes", $this->getsetting("flexible_settings"))
            ? $this->getsetting("flexible_settings")["right_classes"]
            : $this->defaultSettings()["flexible_settings"]["right_classes"]
        ]
      ]
    ] + parent::settingsForm($form, $form_state);
    return $form1;
  }

  /**
   *
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    $elements = [];
    $flexibleSettings  = $this->getsetting("flexible_settings");
    $defaultSettings = $this->defaultSettings()["flexible_settings"];
    $elementsClasses = array_key_exists("element_classes", $flexibleSettings) ? $flexibleSettings["element_classes"] : $defaultSettings["element_classes"];
    $rightClasses = array_key_exists("right_classes", $flexibleSettings) ? $flexibleSettings["right_classes"] : $defaultSettings["right_classes"];
    $leftClasses = array_key_exists("left_classes", $flexibleSettings) ? $flexibleSettings["left_classes"] : $defaultSettings["left_classes"];
    $showSeparator = array_key_exists("show_separator", $flexibleSettings) ? $flexibleSettings["show_separator"] : $defaultSettings["show_separator"];
    $separator = array_key_exists("separator", $flexibleSettings) ? $flexibleSettings["separator"] : $defaultSettings["separator"];
    // dd($separator);
    /**
     * @var  \Drupal\Core\Field\Plugin\Field\FieldType\StringItem $item
     */
    foreach ($items as $delta => &$item) {
      $value = $item->getValue()["value"];
      $elements[$delta] = $this->viewValue($value, ["right" => $rightClasses, "left" => $leftClasses, "element" => $elementsClasses], $separator);
    }
    // dd($elements);
    return $elements;
  }

  /**
   *
   * {@inheritdoc}
   */
  public function view(FieldItemListInterface $items, $langcode = NULL) {
    $elements = parent::view($items, $langcode);
    return $elements;
  }

  /**
   * Generate the output appropriate for one field item.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *        One field item.
   *        
   * @return array The textual output generated as a render array.
   */
  protected function viewValue($value, array $classes = ["right" => "", "left" => ""], $separator = "pipe") {
    // The text value has no text format assigned to it, so the user input
    // should equal the output, including newlines.
    $elementClass = $classes["element"] ?? false;
    $leftClass = $classes["left"] ?? false;
    $rightClass = $classes["right"] ?? false;
    $pattern = $separator == "pipe" ? "|([^\|]+)\|(.*)|" : "|([^\`]+)\`(.*)|";
    preg_match($pattern, $value, $matches);
    if (!$matches) {
      return [
        '#type' => 'inline_template',
        '#template' => '{{ value|raw }}',
        '#context' => [
          'value' => "<div>$value</div>"
        ]
      ];
    }
    $render = $elementClass ? "<div class='$elementClass'>" : "<div>";
    if (isset($matches[1])) {
      $attributes = $leftClass ? "class='$leftClass'" : "";
      $render .= "<span $attributes>$matches[1]</span>";
    }

    if (isset($matches[2])) {
      $attributes = $rightClass ? "class='$rightClass'" : "";
      $render .= "<span $attributes>$matches[2]</span>";
    }

    $render .= "</div>";
    return [
      '#type' => 'inline_template',
      '#template' => '{{ value|raw }}',
      '#context' => [
        'value' => $render
      ]
    ];
  }
}
