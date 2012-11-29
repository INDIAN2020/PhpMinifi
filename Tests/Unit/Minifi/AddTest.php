<?php
/**
 * PhpMinifi
 * @link https://github.com/masicek/PhpMinifi
 * @author Viktor Mašíček <viktor@masicek.net>
 * @license "New" BSD License
 */
namespace Tests\Unit\Minifi;

/**
 * AddTest
 * @todo add more files
 * @todo add directory contains files and another subdirectory
 * @todo add files with another PHPDocs flags
 *
 * @author Viktor Mašíček <viktor@masicek.net>
 *
 * @covers \PhpMinifi\Minifi::add()
 */
class AddTest extends \Tests\Unit\TestCase
{


	/**
	 * @test
	 */
	public function oneFileAndDefaultPhpdocsFlag()
	{
		/** @var \PhpMinifi\Minifi */
		$minifi = $this->getMockBuilder('\PhpMinifi\Minifi')
			->setMethods(array('addFile'))
			->getMock();

		$minifi->expects($this->any())
				->method('addFile')
				->with(1,1);

		$minifi->setDefaultPhpDocs(\PhpMinifi\Minifi::PHPDOCS_INCLUDE);

		$minifi->add(FIXTURES_DIR . '/fileA.php');

		$files = $this->getPropertyValue($minifi, '\PhpMinifi\Minifi::$files');
		$this->assertEquals(
			array(
				FIXTURES_DIR . '/fileA.php' => \PhpMinifi\Minifi::PHPDOCS_INCLUDE,
			),
			$files
		);
	}


}
