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
 *
 * @author Viktor Mašíček <viktor@masicek.net>
 *
 * @covers \PhpMinifi\Minifi::add()
 * @covers \PhpMinifi\Minifi::addFile()
 */
class AddTest extends \Tests\Unit\TestCase
{


	/**
	 * @test
	 */
	public function oneFileAndDefaultPhpdocsFlag()
	{
		$minifi = new \PhpMinifi\Minifi();

		$minifi->setDefaultPhpDocs(\PhpMinifi\Minifi::PHPDOCS_INCLUDE);

		$minifi->add(FIXTURES_DIR . '/fileA.php');

		$files = $this->getPropertyValue($minifi, '\PhpMinifi\Minifi::$files');
		$this->assertEquals(
			array(
			realpath(FIXTURES_DIR . '/fileA.php') => \PhpMinifi\Minifi::PHPDOCS_INCLUDE,
			),
			$files
		);
	}


	/**
	 * @test
	 */
	public function moreFileAndDifferentPhpdocsFlag()
	{
		$minifi = new \PhpMinifi\Minifi();

		$minifi->setDefaultPhpDocs(\PhpMinifi\Minifi::PHPDOCS_REMOVE);

		$minifi->add(FIXTURES_DIR . '/fileA.php');
		$minifi->add(
			array(FIXTURES_DIR . '/dirA/fileA.php', FIXTURES_DIR . '/fileC.php'),
			\PhpMinifi\Minifi::PHPDOCS_INCLUDE
		);
		$minifi->add(FIXTURES_DIR . '/fileD.php', \PhpMinifi\Minifi::PHPDOCS_REMOVE);

		$files = $this->getPropertyValue($minifi, '\PhpMinifi\Minifi::$files');
		$this->assertEquals(
			array(
				realpath(FIXTURES_DIR . '/fileA.php') => \PhpMinifi\Minifi::PHPDOCS_REMOVE,
				realpath(FIXTURES_DIR . '/dirA/fileA.php') => \PhpMinifi\Minifi::PHPDOCS_INCLUDE,
				realpath(FIXTURES_DIR . '/fileC.php') => \PhpMinifi\Minifi::PHPDOCS_INCLUDE,
				realpath(FIXTURES_DIR . '/fileD.php') => \PhpMinifi\Minifi::PHPDOCS_REMOVE,
			),
			$files
		);
	}


	/**
	 * @test
	 */
	public function moreFilesAndDirectoriesAndDifferentPhpdocsFlag()
	{
		$minifi = new \PhpMinifi\Minifi();

		$minifi->setDefaultPhpDocs(\PhpMinifi\Minifi::PHPDOCS_REMOVE);

		$minifi->add(
			array(FIXTURES_DIR . '/dirA', FIXTURES_DIR . '/fileD.php'),
			\PhpMinifi\Minifi::PHPDOCS_INCLUDE
		);
		$minifi->add(FIXTURES_DIR . '/dirB');

		$files = $this->getPropertyValue($minifi, '\PhpMinifi\Minifi::$files');
		$this->assertEquals(
			array(
				realpath(FIXTURES_DIR . '/dirA/subdirA/fileA.php') => \PhpMinifi\Minifi::PHPDOCS_INCLUDE,
				realpath(FIXTURES_DIR . '/dirA/subdirA/fileB.php') => \PhpMinifi\Minifi::PHPDOCS_INCLUDE,
				realpath(FIXTURES_DIR . '/dirA/fileA.php') => \PhpMinifi\Minifi::PHPDOCS_INCLUDE,
				realpath(FIXTURES_DIR . '/fileD.php') => \PhpMinifi\Minifi::PHPDOCS_INCLUDE,
				realpath(FIXTURES_DIR . '/dirB/fileA.php') => \PhpMinifi\Minifi::PHPDOCS_REMOVE,
				realpath(FIXTURES_DIR . '/dirB/fileB.php') => \PhpMinifi\Minifi::PHPDOCS_REMOVE,
			),
			$files
		);
	}


	/**
	 * @test
	 */
	public function nonexistFile_getException()
	{
		$minifi = new \PhpMinifi\Minifi();

		$minifi->setDefaultPhpDocs(\PhpMinifi\Minifi::PHPDOCS_REMOVE);

		// add exist files
		$minifi->add(
			array(FIXTURES_DIR . '/dirA', FIXTURES_DIR . '/fileD.php'),
			\PhpMinifi\Minifi::PHPDOCS_INCLUDE
		);
		// add non exist file end expect exception
		$this->setExpectedException('\PhpMinifi\ErrorException');
		$minifi->add(FIXTURES_DIR . '/nonexist');
	}


	/**
	 * @test
	 */
	public function addFileTwice_getException()
	{
		$minifi = new \PhpMinifi\Minifi();

		$minifi->add(FIXTURES_DIR . '/dirA');

		$this->setExpectedException('\PhpMinifi\WarningException');
		$minifi->add(FIXTURES_DIR . '/dirA/fileA.php');
	}


}
