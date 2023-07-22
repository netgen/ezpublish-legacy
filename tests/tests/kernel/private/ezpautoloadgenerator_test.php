<?php
/**
 * File containing the ezpAutoloadGeneratorTest class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package tests
 */

class ezpAutoloadGeneratorTest extends PHPUnit_Framework_TestCase
{
  private ?\eZAutoloadGenerator $autoload_generator = null;

  public function setUp()
  {
    $this->autoload_generator = new eZAutoloadGenerator();
  }

  public function testBuildPHPUnitConfigurationFile()
  {
    $autoloadArray = @include 'autoload/ezp_kernel.php';
    
    static::assertEquals(null, $this->autoload_generator->buildPHPUnitConfigurationFile());

    $this->autoload_generator->setMode(eZAutoloadGenerator::MODE_KERNEL);

    $dom = $this->autoload_generator->buildPHPUnitConfigurationFile();

    static::assertInstanceOf('DomDocument', $dom);
    static::assertTrue($dom->hasChildNodes());

    $elements = $dom->getElementsByTagName('filter');
    static::assertEquals(1, $elements->length);

    $filter = $elements->item(0);
    $blacklist = $filter->getElementsByTagName('blacklist');
    $whitelist = $filter->getElementsByTagName('whitelist');
    static::assertEquals(1, $blacklist->length);
    static::assertEquals(1, $whitelist->length);

    static::assertContains('tests', $blacklist->item(0)->getElementsByTagName('directory')->item(0)->nodeValue);
    static::assertEquals(is_countable($autoloadArray) ? count($autoloadArray) : 0, $whitelist->item(0)->getElementsByTagName('file')->length);

    
    //$this->assertTrue($dom->hasChildNodes('filter'));

    //echo $dom->saveXML();
  }
}
?>
