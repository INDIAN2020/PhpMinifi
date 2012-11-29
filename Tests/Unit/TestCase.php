<?php

/**
 * PhpMinifi
 * @link https://github.com/masicek/PhpMinifi
 * @author Viktor Mašíček <viktor@masicek.net>
 * @license "New" BSD License
 */
namespace Tests\Unit;

/**
 * Extends of PHPUnit TestCase class.
 *
 * @author Viktor Mašíček <viktor@masicek.net>
 */
class TestCase extends \PHPUnit_Framework_TestCase
{


	/**
	 * Call protected / private method.
	 *
	 * If we want to call private method, which not define the object, but a parent,
	 * you need to specify the form $method "Class::method", where Class is class, which the property defined.
	 *
	 * @param string|object $object Name of class (for static property) or instance of class
	 * @param string $method Name of method (e.g. "foo" or "Class::foo")
	 * @param array $params Parameters sent into the method
	 *
	 * @return mixed Value returner by the called method
	 */
	protected function callMethod($object, $method, $params = array())
	{
		if (($pos = strpos($method, '::')) !== FALSE)
		{
			$class = substr($method, 0, $pos);
			$method = substr($method, $pos + 2);
			if (substr($method, -2) === '()')
			{
				$method = substr($method, 0, -2);
			}
		}

		$method = $this->getAccessibleMethod(isset($class) ? $class :
			$object, $method);
		return $method->invokeArgs($object, $params);
	}


	/**
	 * Return value of protected / private property.
	 *
	 * If you want to read the value of private property, which not define the object, but a parent,
	 * you need to specify the form $property "Class::$propertyName", where Class is class, which the property defined.
	 *
	 * @param string|object $object Name of class (for static property) or instance of class
	 * @param string $property Name of property
	 *
	 * @return mixed Value of getted property
	 */
	protected function getPropertyValue($object, $property)
	{
		if (($pos = strpos($property, '::$')) !== FALSE)
		{
			$class = substr($property, 0, $pos);
			$property = substr($property, $pos + 3);
		}

		$property = $this->getAccessibleProperty(isset($class) ? $class :
			$object, $property);
		return $property->getValue($object);
	}


	/**
	 * Change value of protected / private property.
	 *
	 * If you want to change the value of private property, which not define the object, but a parent,
	 * you need to specify the form $property "Class::$propertyName", where Class is class, which the property defined.
	 *
	 * Example: change request in presenter
	 * <pre>
	 *  $presenter = new \MyModule\SomePresenterPresenter();
	 *  $requestObject = Mocker::mock('\Nette\Http\Request');
	 *  $this->setPropertyValue($presenter, '\Nette\Application\UI\Presenter::$request', $requestObject);
	 * </pre>
	 *
	 * @param string|object	$object Neme of class (for static property) or instance of class
	 * @param string $property Name of property
	 * @param mixed $value New value of property
	 *
	 * @return void
	 */
	protected function setPropertyValue($object, $property, $value)
	{
		if (($pos = strpos($property, '::$')) !== FALSE)
		{
			$class = substr($property, 0, $pos);
			$property = substr($property, $pos + 3);
		}

		$property = $this->getAccessibleProperty(isset($class) ? $class :
			$object, $property);
		$property->setValue($object, $value);
	}


	/**
	 * Return amenable \ReflectionMethod
	 *
	 * @param string $class Class name
	 * @param string $name Method name
	 *
	 * @return \ReflectionMethod
	 */
	protected static function getAccessibleMethod($class, $name)
	{
		$method = new \ReflectionMethod($class, $name);
		$method->setAccessible(TRUE);

		return $method;
	}


	/**
	 * Return amenable \ReflectionProperty
	 *
	 * @param string $class Class name
	 * @param string $name Property name
	 *
	 * @return \ReflectionProperty
	 */
	protected static function getAccessibleProperty($class, $name)
	{
		$property = new \ReflectionProperty($class, $name);
		$property->setAccessible(TRUE);

		return $property;
	}


}
