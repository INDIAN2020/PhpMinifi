<?php
/**
 * PhpMinifi
 * @link https://github.com/masicek/PhpMinifi
 * @author Viktor Mašíček <viktor@masicek.net>
 * @license "New" BSD License
 */
namespace Tests\Unit\Minifi;

/**
 * HeaderTest
 *
 * @author Viktor Mašíček <viktor@masicek.net>
 *
 * @covers \PhpMinifi\Minifi::setHeader()
 * @covers \PhpMinifi\Minifi::getHeader()
 */
class HeaderTest extends \Tests\Unit\TestCase
{


	/**
	 * @dataProvider comments
	 * @test
	 */
	public function test($header, $expectedOutputHeader)
	{
		$minifi = new \PhpMinifi\Minifi();
		$minifi->setHeader($header);
		$outputHeader = $minifi->getHeader();
		$this->assertEquals($expectedOutputHeader, $outputHeader);
	}


	/**
	 * @provider
	 */
	public function comments()
	{
		return array(
			'begin-long, end, linux new lines' => array(
				" \n /**\nTest comment\n@version 1.2.3\n@author Jon Doe\n*/  ",
				"/** Test comment @version 1.2.3 @author Jon Doe */"
			),
			'begin-short, end, linux new lines' => array(
				" \t /*\nTest comment\n@version 1.2.3\n@author Jon Doe\n*/    ",
				"/* Test comment @version 1.2.3 @author Jon Doe */"
			),
			'begin-long, end, windows new lines' => array(
				"   \n/**\r\nTest comment\r\n@version 1.2.3\r\n@author Jon Doe\r\n*/   ",
				"/** Test comment @version 1.2.3 @author Jon Doe */"
			),
			'begin-long, no end, linux new lines' => array(
				"   /**\nTest comment\n@version 1.2.3\n@author Jon Doe  \n \t ",
				"/** Test comment @version 1.2.3 @author Jon Doe */"
			),
			'no begin, end, linux new lines' => array(
				"  Test comment\n@version 1.2.3\n@author Jon Doe\n*/",
				"/** Test comment @version 1.2.3 @author Jon Doe */"
			),
			'no begin, no end, linux new lines' => array(
				"Test comment\n@version 1.2.3\n@author Jon Doe   ",
				"/** Test comment @version 1.2.3 @author Jon Doe */"
			),
			'no begin, no end, no new lines' => array(
				"Test comment @version 1.2.3 @author Jon Doe",
				"/** Test comment @version 1.2.3 @author Jon Doe */"
			),
		);
	}


}
