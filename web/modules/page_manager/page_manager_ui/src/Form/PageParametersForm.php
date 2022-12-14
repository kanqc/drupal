<?php

namespace Drupal\page_manager_ui\Form;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

class PageParametersForm extends FormBase {

  /**
   * @var string
   */
  protected $machine_name;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'page_manager_parameters_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $cached_values = $form_state->getTemporaryValue('wizard');
    $this->machine_name = $cached_values['id'];
    $form['items'] = [
      '#type' => 'markup',
      '#prefix' => '<div id="available-parameters">',
      '#suffix' => '</div>',
      '#theme' => 'table',
      '#header' => [
        $this->t('Machine name'),
        $this->t('Label'),
        $this->t('Type'),
        $this->t('Operations'),
      ],
      '#rows' => $this->renderRows($cached_values),
      '#empty' => $this->t('There are no parameters defined for this page.'),
    ];
    return $form;
  }

  protected function renderRows($cached_values) {
    $rows = [];
    /** @var $page \Drupal\page_manager\Entity\Page */
    $page = $cached_values['page'];
    /**
     * @var string $parameter
     */
    foreach ($page->getParameterNames() as $parameter_name) {
      $parameter = $page->getParameter($parameter_name);
      $row = [];
      $row['machine_name'] = $parameter['machine_name'] ?? '';
      if ($label = $parameter['label'] ?? '') {
        $row['label'] = $label;
      }
      else {
        $row['type']['colspan'] = 2;
      }
      $row['type']['data'] = isset($parameter['type']) ?: $this->t('<em>No context assigned</em>');

      [$route_partial, $route_parameters] = $this->getOperationsRouteInfo($cached_values, $cached_values['id'], $parameter_name);
      $build = [
        '#type' => 'operations',
        '#links' => $this->getOperations($route_partial, $route_parameters),
      ];
      $row['operations']['data'] = $build;
      $rows[$parameter_name] = $row;
    }

    return $rows;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $cached_values = $form_state->getTemporaryValue('wizard');
  }

  /**
   * Operations for Page Parameters form.
   *
   * @param $route_name_base
   *   The base route name.
   * @param array $route_parameters
   *   The route parameters.
   *
   * @return array
   *   The set of operations for the form.
   */
  protected function getOperations($route_name_base, array $route_parameters = []) {
    $operations['edit'] = [
      'title' => t('Edit'),
      'url' => new Url($route_name_base . '.edit', $route_parameters),
      'weight' => 10,
      'attributes' => [
        'class' => ['use-ajax'],
        'data-dialog-type' => 'modal',
        'data-dialog-options' => Json::encode([
          'width' => 700,
        ]),
      ],
    ];
    return $operations;
  }

  /**
   * Returns the tempstore id to use.
   *
   * @return string
   *   The default tempstore ID.
   */
  protected function getTempstoreId() {
    return 'page_manager.page';
  }

  /**
   * Get Operation Route Information.
   *
   * @param $cached_values
   *   The Cached Values.
   * @param $machine_name
   *   Machine name of the route.
   * @param $row
   *   The row being operated on.
   *
   * @return array
   *
   */
  protected function getOperationsRouteInfo($cached_values, $machine_name, $row) {
    return [
      'page_manager.parameter', [
        'machine_name' => $machine_name,
        'name' => $row
      ],
    ];
  }

}
