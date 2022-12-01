<?php

namespace Drupal\font_iconpicker\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'font_iconpicker' formatter.
 *
 * @FieldFormatter(
 *   id = "font_iconpicker",
 *   label = @Translation("Font Icon Picker"),
 *   field_types = {
 *     "font_iconpicker",
 *   }
 * )
 */
class FontIconpicker extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $elements[$delta] = [
        '#theme' => 'font_icon',
        '#icon' => $this->viewValue($item),
        '#attached' => [
          'library' => [
            'font_iconpicker/font-custom',
          ],
        ],
      ];
    }

    return $elements;
  }

  /**
   * Generate the output appropriate for one field item.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   One field item.
   *
   * @return string
   *   The textual output generated.
   */
  protected function viewValue(FieldItemInterface $item) {
    // The text value has no text format assigned to it, so the user input
    // should equal the output, including newlines.
    return nl2br(Html::escape($item->value));
  }

}
