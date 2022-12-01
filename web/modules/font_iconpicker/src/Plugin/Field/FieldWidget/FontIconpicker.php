<?php

namespace Drupal\font_iconpicker\Plugin\Field\FieldWidget;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\font_iconpicker\IconHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'font_iconpicker' widget.
 *
 * @FieldWidget(
 *   id = "font_iconpicker",
 *   label = @Translation("Font Icon Picker"),
 *   field_types = {
 *     "font_iconpicker",
 *   }
 * )
 */
class FontIconpicker extends WidgetBase {

  /**
   * The icon helper service.
   *
   * @var \Drupal\font_iconpicker\IconHelper
   */
  private IconHelper $iconHelper;

  /**
   * The icon helper service.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  private ImmutableConfig $config;

  /**
   * {@inheritdoc}
   */
  public function __construct(
    $plugin_id,
    $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    array $third_party_settings,
    ConfigFactoryInterface $config_factory,
    IconHelper $icon_helper
  ) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);
    $this->config = $config_factory->get('font_iconpicker.settings');
    $this->iconHelper = $icon_helper;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition
  ) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['third_party_settings'],
      $container->get('config.factory'),
      $container->get('font_iconpicker.icon_helper')
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'has_search' => FALSE,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = [];

    $elements['has_search'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Display search input'),
      '#default_value' => $this->getSetting('has_search'),
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];

    $summary[] = $this->t('Display search input: @value', [
      '@value' => $this->getSetting('has_search') ? $this->t('yes') : $this->t('no'),
    ]);

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(
    FieldItemListInterface $items,
    $delta,
    array $element,
    array &$form,
    FormStateInterface $form_state
  ) {
    $element['value'] = $element + [
      '#type' => 'select',
      '#options' => array_combine(
        $this->iconHelper->getIconsAvailable(),
        $this->iconHelper->getIconsAvailable()
      ),
      '#default_value' => $items[$delta]->value ?? NULL,
      '#attributes' => [
        'class' => [
          'font-iconpicker-element',
        ],
      ],
      '#attached' => [
        'library' => [
          'font_iconpicker/widget',
        ],
        'drupalSettings' => [
          'font_iconpicker' => $this->getSettings() + [
            'additional_class' => $this->config->get('additional_class'),
            // Display empty icon when field is optional.
            'empty_icon' => !$this->fieldDefinition->isRequired(),
            'theme' => $this->config->get('theme'),
          ],
        ],
      ],
    ];

    // Define default value if field is required.
    $element['value']['#empty_value'] = '';
    if (
      $this->fieldDefinition->isRequired() &&
      $items[$delta]->isEmpty()
    ) {
      $element['value']['#default_value'] = key($element['value']['#options']);
    }

    return $element;
  }

}
