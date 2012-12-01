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
 * @covers \PhpMinifi\Minifi::phpdocsToSubstitution()
 * @covers \PhpMinifi\Minifi::substitutionToPhpdocs()
 */
class SubstitutionTest extends \Tests\Unit\TestCase
{


	/**
	 * @test
	 */
	public function setComplexContent_getModifiedContent()
	{
		$inputContent = '<?php
			/** one line PhpDoc comment */
			define("CONST", "hello world");

			/**
				* more line PhpDoc comment
				*
				* @param int $variable1 comment one
				* @param int $variable2 comment two
				*
				* @return void
				*/
			public function aaa($variable1, $variable2)
			{
				/** @var \Namespace\ClassName */
				$someClass = $variable1->getSomeClass();

				$lorem = "ipsum";
			}

			public function bbb()
			{
				$xxx = "renamed string";
				$yyy = "removed string";
			}
		?>';

		$modifiedContent = '<?php
			/** one line PhpDoc comment */
			define("CONST", "hello world");

			/**
				* more line PhpDoc comment
				*
				* @param int $variable1 comment one
				* @param int $variable2 comment two
				*
				* @return void
				*/
			public function aaa($variable1, $variable2)
			{
				/** @var \Namespace\ClassName */
				$someClass = $variable1->getSomeClass();

				$lorem = "ipsum";
			}

			public function bbb()
			{
				$xxx = "new text";
				$yyy = "";
			}
		?>';

		$minifi = $this->getMockBuilder('\PhpMinifi\Minifi')
			->setMethods(array('minifiPhpdocs'))
			->getMock();

		$minifi->expects($this->any())
			->method('minifiPhpdocs')
			->will($this->returnArgument(1));

		$substituedContent = $this->callMethod($minifi, 'phpdocsToSubstitution', array($inputContent));

		$substituedContent = str_replace('renamed string', 'new text', $substituedContent);
		$substituedContent = str_replace('removed string', '', $substituedContent);

		$returnedBackSubstituedContent = $this->callMethod($minifi, 'substitutionToPhpdocs', array($substituedContent));
		$this->assertEquals($modifiedContent, $returnedBackSubstituedContent);
	}


}
