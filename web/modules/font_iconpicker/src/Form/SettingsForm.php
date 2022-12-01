<?php

namespace Drupal\font_iconpicker\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides the site configuration form.
 *
 * @internal
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'font_iconpicker_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['font_iconpicker.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('font_iconpicker.settings');

    $form['css_font_path'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Font CSS file path'),
      '#description' => $this->t('Example: themes/custom/{my_theme}/font/{my_font}/style.css'),
      '#required' => TRUE,
      '#default_value' => $config->get('css_font_path'),
      '#element_validate' => [
        [static::class, 'validateCssFontPath'],
      ],
    ];

    $form['class_prefix'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Class prefix'),
      '#description' => $this->t('Example: icon-'),
      '#required' => TRUE,
      '#default_value' => $config->get('class_prefix'),
    ];

    $form['additional_class'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Additional class'),
      '#description' => $this->t('Some icons font generators like "IcoMoon" need an additional "icon" class.'),
      '#default_value' => $config->get('additional_class'),
    ];

    $form['theme'] = [
      '#type' => 'radios',
      '#title' => $this->t('Widget theme'),
      '#options' => [
        'bootstrap' => $this->t('Bootstrap'),
        'dark-grey' => $this->t('Dark grey'),
        'grey' => $this->t('Grey'),
        'inverted' => $this->t('Inverted'),
      ],
      '#default_value' => $config->get('theme'),
      '#required' => TRUE,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('font_iconpicker.settings')
      ->set('additional_class', $form_state->getValue('additional_class'))		  
      ->set('css_font_path', $form_state->getValue('css_font_path'))
      ->set('class_prefix', $form_state->getValue('class_prefix'))
      ->set('theme', $form_state->getValue('theme'))
      ->save();

    drupal_flush_all_caches();

    parent::submitForm($form, $form_state);
  }

  /**
   * Validation callback for CSS font path field.
   *
   * @param array $element
   *   The element being processed.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * @param array $complete_form
   *   The complete form structure.
   */
  public static function validateCssFontPath(array &$element, FormStateInterface $form_state, array &$complete_form) {
    $filepath = $form_state->getValue('css_font_path');

    if (!empty($filepath)) {
      if (!file_exists($filepath)) {
        $form_state->setError($element, t('The file %file does not exist.', ['%file' => $filepath]));
        return;
      }

      $extension = pathinfo($filepath, PATHINFO_EXTENSION);
      if ($extension !== 'css') {
        $form_state->setError($element, t('You must to specify a valid CSS file.'));
      }
    }
  }

}
