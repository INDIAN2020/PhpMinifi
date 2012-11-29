<?php
/**
 * PhpMinifi
 * @link https://github.com/masicek/PhpMinifi
 * @author Viktor Mašíček <viktor@masicek.net>
 * @license "New" BSD License
 */
namespace Tests\Unit\Minifi;

/**
 * MinifiPhpdocsTest
 * @todo add inputs with "   *  " at the begin of some lines
 *
 * @author Viktor Mašíček <viktor@masicek.net>
 *
 * @covers \PhpMinifi\Minifi::minifiPhpdocs()
 */
class MinifiPhpdocsTest extends \Tests\Unit\TestCase
{


	/**
	 * @dataProvider comments
	 * @test
	 */
	public function test($comment, $expectedMinifiComment)
	{
		$minifi = new \PhpMinifi\Minifi();
		$minifiComment = $this->callMethod($minifi, 'minifiPhpdocs', array($comment));
		$this->assertEquals($expectedMinifiComment, $minifiComment);
	}


	/**
	 * @provider
	 */
	public function comments()
	{
		return array(
			'begin-long, end, mix new lines, redundant whitespace' => array(
				"/**  \nTest    comment \r\n @version 1.2.3\n@author \tJon Doe\n  \t */",
				"/** Test comment @version 1.2.3 @author Jon Doe */"
			),
			'already minified comment' => array(
				"/** Test comment @version 1.2.3 @author Jon Doe */",
				"/** Test comment @version 1.2.3 @author Jon Doe */"
			),
		);
	}


}
