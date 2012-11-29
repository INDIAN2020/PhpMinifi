<?php
/**
 * PhpMinifi
 * @link https://github.com/masicek/PhpMinifi
 * @author Viktor Mašíček <viktor@masicek.net>
 * @license "New" BSD License
 */
namespace Tests\Unit\Minifi;

/**
 * RemoveRequiringConstructsTest
 *
 * @author Viktor Mašíček <viktor@masicek.net>
 *
 * @covers \PhpMinifi\Minifi::removeRequiringConstructs()
 */
class RemoveRequiringConstructsTest extends \Tests\Unit\TestCase
{


	/**
	 * @dataProvider content
	 * @test
	 */
	public function test($content, $expectedOutputContent)
	{
		$minifi = new \PhpMinifi\Minifi();
		$outputContent = $this->callMethod($minifi, 'removeRequiringConstructs', array($content));
		$this->assertEquals($expectedOutputContent, $outputContent);
	}


	/**
	 * @provider
	 */
	public function content()
	{
		return array(
			'require' => array(
				"some content \n require   PATH . '/file.php'  ; and another content",
				"some content \n  and another content",
			),
			'require_once' => array(
				"some content \n require_once   PATH   . '/file.php'  ; and another content",
				"some content \n  and another content",
			),
			'include' => array(
				"some content \n include  PATH . '/file.php'; and another content",
				"some content \n  and another content",
			),
			'include_once' => array(
				"some content \n include_once   PATH .   '/file.php'  ; and another content",
				"some content \n  and another content",
			),
			'all together' => array(
				"lorem require_once 'fileClass.php'; test_once 'file.php'; content \n include_once   PATH .   '/file.php'  ; and another; include '/file.php'; \t\n\t require 'file2.php';",
				"lorem  test_once 'file.php'; content \n  and another;  \t\n\t ",
			),
		);
	}


}
