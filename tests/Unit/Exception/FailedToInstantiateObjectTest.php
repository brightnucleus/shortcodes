<?php declare( strict_types=1 );

namespace BrightNucleus\Shortcode\Exception;

use PHPUnit\Framework\TestCase;

final class FailedToInstantiateObjectTest extends TestCase {

	/**
	 * @covers \BrightNucleus\Shortcode\Exception\FailedToInstantiateObject::fromFactory
	 */
	public function test_from_factory_with_string_class_name() {
		$exception = FailedToInstantiateObject::fromFactory(
			'NonExistentClass',
			'SomeInterface'
		);

		$this->assertInstanceOf( FailedToInstantiateObject::class, $exception );
		$this->assertInstanceOf( ShortcodeException::class, $exception );
		$this->assertStringContainsString( 'Could not instantiate object of type "SomeInterface" from class name: "NonExistentClass"', $exception->getMessage() );
	}

	/**
	 * @covers \BrightNucleus\Shortcode\Exception\FailedToInstantiateObject::fromFactory
	 */
	public function test_from_factory_with_callable() {
		$factory = fn() => null;
		$exception = FailedToInstantiateObject::fromFactory(
			$factory,
			'SomeInterface'
		);

		$this->assertInstanceOf( FailedToInstantiateObject::class, $exception );
		$this->assertStringContainsString( 'Could not instantiate object of type "SomeInterface" from factory of type: "object"', $exception->getMessage() );
	}

	/**
	 * @covers \BrightNucleus\Shortcode\Exception\FailedToInstantiateObject::fromFactory
	 */
	public function test_from_factory_with_previous_exception() {
		$previousException = new \Exception( 'Previous error message' );
		$exception = FailedToInstantiateObject::fromFactory(
			'NonExistentClass',
			'SomeInterface',
			$previousException
		);

		$this->assertInstanceOf( FailedToInstantiateObject::class, $exception );
		$this->assertSame( $previousException, $exception->getPrevious() );
		$this->assertStringContainsString( 'Reason: Previous error message', $exception->getMessage() );
	}

	/**
	 * @covers \BrightNucleus\Shortcode\Exception\FailedToInstantiateObject::fromFactory
	 */
	public function test_from_factory_with_invalid_argument_type() {
		$exception = FailedToInstantiateObject::fromFactory(
			123,
			'SomeInterface'
		);

		$this->assertInstanceOf( FailedToInstantiateObject::class, $exception );
		$this->assertStringContainsString( 'Could not instantiate object of type "SomeInterface" from invalid argument of type: "123"', $exception->getMessage() );
	}

	/**
	 * @covers \BrightNucleus\Shortcode\Exception\FailedToInstantiateObject::fromInvalidObject
	 */
	public function test_from_invalid_object() {
		$exception = FailedToInstantiateObject::fromInvalidObject(
			'WrongClass',
			'ExpectedInterface'
		);

		$this->assertInstanceOf( FailedToInstantiateObject::class, $exception );
		$this->assertStringContainsString( 'Could not instantiate object of type "ExpectedInterface", got "WrongClass" instead', $exception->getMessage() );
	}
}