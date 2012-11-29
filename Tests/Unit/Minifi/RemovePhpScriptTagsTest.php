<?php
/**
 * PhpMinifi
 * @link https://github.com/masicek/PhpMinifi
 * @author Viktor Mašíček <viktor@masicek.net>
 * @license "New" BSD License
 */
namespace Tests\Unit\Minifi;

/**
 * RemovePhpScriptTagsTest
 *
 * @author Viktor Mašíček <viktor@masicek.net>
 *
 * @covers \PhpMinifi\Minifi::removePhpScriptTags()
 */
class RemovePhpScriptTagsTest extends \Tests\Unit\TestCase
{


	/**
	 * @dataProvider content
	 * @test
	 */
	public function test($content, $expectedOutputContent)
	{
		$minifi = new \PhpMinifi\Minifi();
		$outputContent = $this->callMethod($minifi, 'removePhpScriptTags', array($content));
		$this->assertEquals($expectedOutputContent, $outputContent);
	}


	/**
	 * @provider
	 */
	public function content()
	{
		return array(
			'begin, end, with whitespace' => array(
				" \r\n <?php Some\r\nContent \t another \n content ?>   \n \t ",
				"Some\r\nContent \t another \n content",
			),
			'begin, end, no whitespace' => array(
				"<?php Some\r\nContent \t another \n content ?>",
				"Some\r\nContent \t another \n content",
			),
			'no begin, end, no whitespace' => array(
				" Some\r\nContent \t another \n content ?>",
				"Some\r\nContent \t another \n content",
			),
			'begin, no end, no whitespace' => array(
				"<?php Some\r\nContent \t another \n content ",
				"Some\r\nContent \t another \n content",
			),
			'no begin, no end, no whitespace' => array(
				" Some\r\nContent \t another \n content ",
				"Some\r\nContent \t another \n content",
			),
		);
	}


}
