<?php

/**
 * PhpMinifi
 * @link https://github.com/masicek/PhpMinifi
 * @author Viktor Mašíček <viktor@masicek.net>
 * @license "New" BSD License
 */

namespace PhpMinifi;

require_once __DIR__ . '/Exceptions.php';

/**
 * Class for minifing PHP files.
 * - it allows minifi more files together
 * - it allows retain PHPDocs
 *
 * <example>
 * // default do not include PHPDocs
 * $minifi = new \PhpMinifi\Minifi();
 *
 * // set header of minifi version
 * $minifi->setHeader('PhpMinifi: Minified version of PhpMinifi. @author Viktor Mašíček');
 *
 * // default include PHPDocs
 * $minifi->setDefaultPhpdocs(\PhpMinifi\Minifi::PHPDOCS_INCLUDE);
 *
 * // one file with PHPDocs
 * $minifi->add('./myFile_A.php');
 * // one directory with PHPDocs
 * $minifi->add('./myDirectory_A');
 * // one files and one directory with PHPDocs
 * $minifi->add(array('./myFile_B.php', './myDirectory_B'));
 * // more files and directories without PHPDocs
 * $minifi->add(
 *     array(
 *         './myFile_C.php',
 *         './myFile_E.php'
 *         './myDirectory_C',
 *         './myDirectory_E',
 *     ),
 *     \PhpMinifi\Minifi::PHPDOCS_REMOVE
 * );
 *
 * // get minifi content into variable
 * $content = $minifi->minifi();
 *
 * // save minify content into set file and return it
 * $content = $minifi->minifi('./myMinifi.php');
 * </example>
 *
 * @author Viktor Mašíček <viktor@masicek.net>
 */
class Minifi
{


	/**
	 * Version of PhpMinifi
	 */
	const VERSION = '0.1.0';

	/**
	 * Flag for include PHPDocs into minifi version
	 */
	const PHPDOCS_INCLUDE = 'phpdocs_include';

	/**
	 * Flag for not include PHPDocs into minifi version
	 */
	const PHPDOCS_REMOVE = 'phpdocs_remove';

	/**
	 * Flag for setting of including PHPDocs into minifi version by default flag
	 */
	const PHPDOCS_DEFAULT = 'phpdocs_default';

	/**
	 * Prefix for substitution for PHPDocs
	 */
	const PHPDOCS_SUBSTITUTION_PREFIX = '___PhpMinifi_PHPDoc_';

	/**
	 * Suffic for substitution for PHPDocs
	 */
	const PHPDOCS_SUBSTITUTION_SUFFIX = '___';


	/**
	 * @var bool Default flag of including PHPDocs into minifi version
	 */
	private $defaultPhpdocs = self::PHPDOCS_REMOVE;

	/**
	 * @var array List of files for minifing
	 * Key = path of file
	 * Value = flag for setting of including PHPDocs into minifi version
	 */
	private $files = array();

	/**
	 * @var string Header comment of minifi version
	 */
	private $header = '';

	/**
	 * @var string TODO
	 */
	private $substitedPhpdocs = array();


	/**
	 * Set header comment of minifi version.
	 * Begin and end of comment is added if it is not included.
	 * New lines are replaced by whitespace.
	 *
	 * @param string $header
	 *
	 * @return void
	 */
	public function setHeader($header)
	{
		$header = trim($header);

		if ((substr($header, 0, 3) !== '/**') && (substr($header, 0, 2) !== '/*'))
		{
			$header = '/** ' . $header;
		}

		if ((substr($header, -2) !== '*/'))
		{
			$header = $header . ' */';
		}

		$header = $this->minifiPhpdocs($header);

		$this->header = $header;
	}


	/**
	 * Return header comment of minifi version.
	 *
	 * @return string
	 */
	public function getHeader()
	{
		return $this->header;
	}


	/**
	 * Set default for setting of including PHPDocs into minifi version.
	 *
	 * @param bool $phpdocs \PhpMinifi\Minifi::PHPDOCS_REMOVE or \PhpMinifi\Minifi::PHPDOCS_INCLUDE
	 *
	 * @throws ErrorException Invalid input flag
	 * @return void
	 */
	public function setDefaultPhpdocs($phpdocs)
	{
		if ($phpdocs !== self::PHPDOCS_INCLUDE && $phpdocs !== self::PHPDOCS_REMOVE)
		{
			throw new ErrorException('Invalid value for default setting of including PHPDocs into minifi version.');
		}

		$this->defaultPhpdocs = $phpdocs;
	}


	/**
	 * Add one or more files or directories into list of minifing files.
	 * We can set flag for setting of including PHPDocs into minifi version for over default flag.
	 *
	 * @param array|string $paths
	 * @param bool $phpdocs
	 *
	 * @throws ErrorException Wrong added path
	 * @return void
	 */
	public function add($paths, $phpdocs = self::PHPDOCS_DEFAULT)
	{
		// cover one entity into array
		if (!is_array($paths))
		{
			$paths = array($paths);
		}

		foreach ($paths as $path)
		{
			$path = realpath($path);

			// file
			if (is_file($path))
			{
				$this->addFile($path, $phpdocs);
			}
			// directory
			else if (is_dir($path))
			{
				// add all included files and directories
				$directory = dir($path);
				while (($entry = $directory->read()) !== FALSE)
				{
					if (in_array($entry, array('.', '..')))
					{
						continue;
					}
					$this->add($path . DIRECTORY_SEPARATOR . $entry, $phpdocs);
				}
				$directory->close();
			}
			else
			{
				throw new ErrorException("Input path '{$paths}' is not file or directory.");
			}
		}
	}


	/**
	 * Return minifi content of all added files.
	 * Contete is written into file is the path is set.
	 *
	 * @param string $filePath
	 *
	 * @throws WarningException No file to minifi
	 * @throws WarningException Minified content is empty
	 * @throws ErrorException Writing into file failed
	 * @return string
	 */
	public function minifi($filePath = NULL)
	{
		if (empty($this->files))
		{
			throw new WarningException("There is no file to minifi.");
		}

		$content = '';

		foreach ($this->files as $file => $phpdocs)
		{
			$content .= $this->minifiFile($file, $phpdocs);
			$content .= ' ';
		}

		if ($content === '')
		{
			throw new WarningException("Minified content is empty.");
		}

		$content = '<?php ' . $this->getHeader() . $content;

		if (!is_null($filePath))
		{
			$writeReturn = file_put_contents($filePath, $content);
			if ($writeReturn == FALSE)
			{
				throw new ErrorException("Writing minified content into the file '{$filePath}' failed.");
			}
		}

		return $content;
	}


	/**
	 * Add one file into the list of minified files with its flag of including of PHPDocs.
	 *
	 * @param string $path
	 * @param bool $phpdocs
	 *
	 * @throws WarningException Some path was added more then once
	 * @return void
	 */
	private function addFile($path, $phpdocs)
	{
		if (isset($this->files[$path]))
		{
			throw new WarningException("The file '{$path}' is added more than once.");
		}

		if ($phpdocs == self::PHPDOCS_DEFAULT)
		{
			$phpdocs = $this->defaultPhpdocs;
		}

		$this->files[$path] = $phpdocs;
	}


	/**
	 * Return minified content of one file.
	 *
	 * @param string $file
	 * @param bool $phpdocs
	 *
	 * @return string
	 */
	private function minifiFile($file, $phpdocs)
	{
		$content = file_get_contents($file);

		if ($phpdocs == self::PHPDOCS_INCLUDE)
		{
			$content = $this->phpdocsToSubstitution($content);
		}

		$tempFile = $file . '.' . time() . '.' . 'minifi';
		file_put_contents($tempFile, $content);
		$content = exec("php -w {$tempFile}");
		unlink($tempFile);

		if ($phpdocs == self::PHPDOCS_INCLUDE)
		{
			$content = $this->substitutionToPhpdocs($content);
		}

		$this->removeRequiringConstructs($content);
		$this->removePhpScriptTags($content);

		return $content;
	}


	/**
	 * Minifi PHPDocs
	 *
	 * @param string $phpdocs
	 *
	 * @return string
	 */
	private function minifiPhpdocs($phpdocs)
	{
		$phpdocs = str_replace("\r\n", ' ', $phpdocs);
		$phpdocs = str_replace("\n", ' ', $phpdocs);
		$phpdocs = str_replace("\t", ' ', $phpdocs);
		return preg_replace('/ +/', ' ', $phpdocs);
	}


	/**
	 * Remove <?php at the begin and "? >" at the end of the input string.
	 *
	 * @param string $content
	 *
	 * @return string
	 */
	private function removePhpScriptTags($content)
	{
		$content = trim($content);

		if (substr($content, 0, 5) == '<?php')
		{
			$content = substr($content, 5);
		}

		if (substr($content, -2) == '?>')
		{
			$content = substr($content, 0, -2);
		}

		$content = trim($content);

		return $content;
	}


	/**
	 * Remove require, require_once, include and include_once.
	 *
	 * @param string $content
	 *
	 * @return string
	 */
	private function removeRequiringConstructs($content)
	{
		return preg_replace('/(require|include)(|_once)[^;]+;/', '', $content);
	}


	/**
	 * Replace PHPDocs in input string on created substiturions.
	 *
	 * @param string $content
	 *
	 * @return string
	 */
	private function phpdocsToSubstitution($content)
	{
		$tokens = token_get_all($content);
		foreach ($tokens as $token)
		{
			// PHPDoc token
			if ($token[0] == T_DOC_COMMENT)
			{
				$phpdoc = $this->minifiPhpdocs($token[1]);
				$index = count($this->substitedPhpdocs) + 1;
				$substitution = self::PHPDOCS_SUBSTITUTION_PREFIX . $index . self::PHPDOCS_SUBSTITUTION_SUFFIX;
				$this->substitedPhpdocs[$substitution] = $phpdoc;
			}
		}

		return $content;
	}


	/**
	 * Replace substitution in input string on PHPDocs.
	 *
	 * @param string $content
	 *
	 * @return string
	 */
	private function substitutionToPhpdocs($content)
	{
		foreach ($this->substitedPhpdocs as $substitution => $phpdocs)
		{
			$content = str_replace($substitution, $phpdocs, $content);
		}

		return $content;
	}


}
