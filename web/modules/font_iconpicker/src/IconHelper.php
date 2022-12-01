<?php

namespace Drupal\font_iconpicker;

use Drupal\Core\Asset\LibraryDiscoveryParser;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;

/**
 * Class Helper.
 *
 * @package Drupal\font_iconpicker
 */
class IconHelper {

  /**
   * The font library name declared in "font_iconpicker.libraryes.yml" file.
   *
   * @var string
   */
  const FONT_LIBRARY_NAME = 'font-custom';

  /**
   * The module settings.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected ImmutableConfig $config;

  /**
   * The library discovery parser.
   *
   * @var \Drupal\Core\Asset\LibraryDiscoveryParser
   */
  protected LibraryDiscoveryParser $discoveryParser;

  /**
   * Constructs a CacheCollector object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The configuration factory.
   * @param \Drupal\Core\Asset\LibraryDiscoveryParser $discovery_parser
   *   The discovery parser service.
   */
  public function __construct(
    ConfigFactoryInterface $config_factory,
    LibraryDiscoveryParser $discovery_parser
  ) {
    $this->config = $config_factory->get('font_iconpicker.settings');
    $this->discoveryParser = $discovery_parser;
  }

  /**
   * Get icons available in the custom font.
   *
   * @return string[]
   *   The icons available.
   */
  public function getIconsAvailable(): array {
    static $icons;

    if (isset($icons)) {
      return $icons;
    }

    $icons = [];
    $files = $this->getCssFiles();
    foreach ($files as &$file) {
      $icons = array_merge($icons, $this->parseCssFile($file));
    }

    return $icons;
  }

  /**
   * Get CSS files content attached to the custom font library.
   *
   * @return string[]
   *   The CSS files path.
   */
  private function getCssFiles(): array {
    $files = $this->parseCssLibrary();

    if (!isset($files)) {
      throw new \LogicException(sprintf(
        'The font library "%s" does not contain any CSS file.',
        self::FONT_LIBRARY_NAME
      ));
    }

    return $files;
  }

  /**
   * Parse CSS file to find icons declarations.
   *
   * @param string $filepath
   *   The CSS filepath.
   *
   * @return array
   *   The icon class declarations.
   */
  private function parseCssFile(string $filepath): array {
    $content = file_get_contents($filepath);

    if (preg_match_all(
      '#\.(' . $this->config->get('class_prefix') . '[\w_-]+)#',
      $content,
      $matches
    )) {
      return $matches[1];
    }

    return [];
  }

  /**
   * Parse the custom font library to find CSS files.
   *
   * @return string[]
   *   The CSS files path attached to the custom font library.
   */
  private function parseCssLibrary(): array {
    $libraries = $this->discoveryParser->buildByExtension('font_iconpicker');

    if (!isset($libraries[self::FONT_LIBRARY_NAME]['css'])) {
      throw new \LogicException(sprintf(
        'The font library "%s" is missing.',
        self::FONT_LIBRARY_NAME
      ));
    }

    $files = [];
    foreach ($libraries[self::FONT_LIBRARY_NAME]['css'] as &$css) {
      // Only parse CSS file.
      if ($css['type'] === 'file') {
        $files[] = $css['data'];
      }
    }

    return $files;
  }

}
