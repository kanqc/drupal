<?php

namespace Drupal\ebt_bootstrap_button\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ebt_core\Plugin\Field\FieldWidget\EbtSettingsDefaultWidget;

/**
 * Plugin implementation of the 'ebt_settings_bootstrap_button' widget.
 *
 * @FieldWidget(
 *   id = "ebt_settings_bootstrap_button",
 *   label = @Translation("EBT Bootstrap Button settings"),
 *   field_types = {
 *     "ebt_settings"
 *   }
 * )
 */
class EbtSettingsBootstrapButtonWidget extends EbtSettingsDefaultWidget {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    $element['ebt_settings']['open_in_new_tab'] = [
      '#title' => $this->t('Open the link in a new tab'),
      '#type' => 'checkbox',
      '#default_value' => $items[$delta]->ebt_settings['open_in_new_tab'] ?? NULL,
    ];

    $element['ebt_settings']['add_nofollow'] = [
      '#title' => $this->t('Add "nofollow" option to the link'),
      '#type' => 'checkbox',
      '#default_value' => $items[$delta]->ebt_settings['add_nofollow'] ?? NULL,
    ];

    $element['ebt_settings']['alignment'] = [
      '#title' => $this->t('Alignment'),
      '#type' => 'radios',
      '#options' => [
        'left' => $this->t('Left'),
        'center' => $this->t('Center'),
        'right' => $this->t('Right'),
      ],
      '#default_value' => $items[$delta]->ebt_settings['alignment'] ?? 'left',
    ];

    $element['ebt_settings']['button_type'] = [
      '#title' => $this->t('Button Type'),
      '#type' => 'radios',
      '#options' => [
        'primary' => $this->t('Primary'),
        'secondary' => $this->t('Secondary'),
        'success' => $this->t('Success'),
        'danger' => $this->t('Danger'),
        'warning' => $this->t('Warning'),
        'info' => $this->t('Info'),
        'light' => $this->t('Light'),
        'dark' => $this->t('Dark'),
        'link' => $this->t('Link'),
      ],
      '#default_value' => $items[$delta]->ebt_settings['button_type'] ?? 'primary',
      '#required' => TRUE,
    ];

    $element['ebt_settings']['outline_button'] = [
      '#title' => $this->t('Outline Button'),
      '#type' => 'checkbox',
      '#default_value' => $items[$delta]->ebt_settings['outline_button'] ?? NULL,
    ];

    $element['ebt_settings']['active_button'] = [
      '#title' => $this->t('Active Button'),
      '#type' => 'checkbox',
      '#default_value' => $items[$delta]->ebt_settings['active_button'] ?? NULL,
    ];

    $element['ebt_settings']['disable_button'] = [
      '#title' => $this->t('Disable Button'),
      '#type' => 'checkbox',
      '#default_value' => $items[$delta]->ebt_settings['disable_button'] ?? NULL,
      '#description' => $this->t("Links don’t support the disabled attribute, it will add the .disabled class to make it visually appear disabled."),
    ];

    $element['ebt_settings']['size'] = [
      '#title' => $this->t('Size'),
      '#type' => 'radios',
      '#options' => [
        'size-default' => $this->t('Default'),
        'btn-sm' => $this->t('Small'),
        'btn-lg' => $this->t('Large'),
      ],
      '#default_value' => $items[$delta]->ebt_settings['size'] ?? 'size-default',
    ];

    $element['ebt_settings']['stetched'] = [
      '#title' => $this->t('Stretched'),
      '#type' => 'checkbox',
      '#default_value' => $items[$delta]->ebt_settings['stetched'] ?? NULL,
    ];

    $element['ebt_settings']['custom_class_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Custom class name'),
      '#default_value' => $items[$delta]->ebt_settings['custom_class_name'] ?? '',
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    foreach ($values as &$value) {
      $value += ['ebt_settings' => []];
    }
    return $values;
  }

}
