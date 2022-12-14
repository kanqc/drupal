<?php

namespace Drupal\Tests\page_manager\Unit;

use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Plugin\Context\Context;
use Drupal\Core\Plugin\Context\ContextDefinition;
use Drupal\page_manager\EventSubscriber\LanguageInterfaceContext;
use Prophecy\Argument;

/**
 * Tests the current language interface context.
 *
 * @coversDefaultClass \Drupal\page_manager\EventSubscriber\LanguageInterfaceContext
 *
 * @group PageManager
 */
class LanguageInterfaceContextTest extends PageContextTestBase {

  /**
   * The context repository service.
   *
   * @var \Drupal\Core\Plugin\Context\ContextRepositoryInterface
   */
  protected $contextRepository;

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();

    $language_manager = $this->createMock('\Drupal\Core\Language\LanguageManagerInterface');

    $context = new Context(new ContextDefinition('language', 'current_language_context'), $language_manager->getCurrentLanguage(LanguageInterface::TYPE_INTERFACE));

    $this->contextRepository = $this->createMock('\Drupal\Core\Plugin\Context\ContextRepositoryInterface');
    $this->contextRepository->expects($this->once())
      ->method('getRunTimeContexts')
      ->willReturn(['@language.current_language_context:language_interface' => $context]);
  }

  /**
   * @covers ::onPageContext
   */
  public function testOnPageContext() {
    $this->page->addContext('language_interface', Argument::type(Context::class))->shouldBeCalled();
    $language_interface_context = new LanguageInterfaceContext($this->contextRepository);
    $language_interface_context->onPageContext($this->event);
  }

}
