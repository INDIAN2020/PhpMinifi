<?php
/**
 * PhpMinifi
 * @link https://github.com/masicek/PhpMinifi
 * @author Viktor Mašíček <viktor@masicek.net>
 * @license "New" BSD License
 */
namespace Tests\Unit\Minifi;

/**
 * SetDefaultPhpdocsTest
 *
 * @author Viktor Mašíček <viktor@masicek.net>
 *
 * @covers \PhpMinifi\Minifi::setDefaultPhpdocs()
 */
class SetDefaultPhpdocsTest extends \Tests\Unit\TestCase
{


	/**
	 * @dataProvider correctValues
	 * @test
	 */
	public function setCorrectValue_valueIsSet($setValue)
	{
		$minifi = new \PhpMinifi\Minifi();
		$minifi->setDefaultPhpdocs($setValue);
		$defultPhpdocs = $this->getPropertyValue($minifi, 'defaultPhpdocs');
		$this->assertEquals($setValue, $defultPhpdocs);
	}


	/**
	 * @provider
	 */
	public function correctValues()
	{
		return array(
			'include' => array(\PhpMinifi\Minifi::PHPDOCS_INCLUDE),
			'remove' => array(\PhpMinifi\Minifi::PHPDOCS_REMOVE),
		);
	}


	/**
	 * @dataProvider incorrectValues
	 * @test
	 * @expectedException \PhpMinifi\ErrorException
	 */
	public function setIncorrectValue_getException($setValue)
	{
		$minifi = new \PhpMinifi\Minifi();
		$minifi->setDefaultPhpdocs($setValue);
	}


	/**
	 * @provider
	 */
	public function incorrectValues()
	{
		return array(
			'default' => array(\PhpMinifi\Minifi::PHPDOCS_DEFAULT),
			'string' => array('include'),
			'number' => array(0),
		);
	}


}
