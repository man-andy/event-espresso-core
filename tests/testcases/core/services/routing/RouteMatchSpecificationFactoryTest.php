<?php

namespace EventEspresso\tests\testcases\core\services\routing;

use EventEspresso\core\exceptions\InvalidDataTypeException;
use EventEspresso\core\exceptions\InvalidInterfaceException;
use EventEspresso\core\services\loaders\LoaderFactory;
use EventEspresso\core\services\loaders\LoaderInterface;
use EventEspresso\core\services\routing\RouteMatchSpecificationFactory;
use EventEspresso\tests\includes\EspressoPHPUnitFrameworkTestCase;
use InvalidArgumentException;
use PHPUnit\Framework\Exception;
use ReflectionException;

/**
 * Class RouteMatchSpecificationFactoryTest
 * Description
 *
 * @package EventEspresso\tests\testcases\core\services\routing
 * @author  Brent Christensen
 * @since   4.9.71.p
 */
class RouteMatchSpecificationFactoryTest extends EspressoPHPUnitFrameworkTestCase
{

    /**
     * @var LoaderInterface $loader
     */
    protected $loader;

    /**
     * @since 4.9.71.p
     * @throws InvalidDataTypeException
     * @throws InvalidInterfaceException
     * @throws InvalidArgumentException
     */
    public function setUp()
    {
        $this->loader = LoaderFactory::getLoader();
    }

    /**
     * @since 4.9.71.p
     * @return RouteMatchSpecificationFactory
     */
    public function getFactory()
    {
        return new RouteMatchSpecificationFactory(
            $this->loader->getShared(
                'EventEspresso\core\services\routing\RouteMatchSpecificationDependencyResolver'
            ),
            $this->loader
        );
    }

    /**
     * @since 4.9.71.p
     * @throws Exception
     */
    public function test__construct()
    {
        $this->assertInstanceOf(
            'EventEspresso\core\services\routing\RouteMatchSpecificationFactory',
            $this->getFactory()
        );
    }

    /**
     * @since 4.9.71.p
     * @throws InvalidDataTypeException
     * @throws Exception
     * @throws ReflectionException
     */
    public function testCreateNewRouteMatchSpecification()
    {
        $fqcn = 'EventEspresso\core\domain\entities\routing\specifications\admin\EspressoEventEditorEdit';
        $this->assertInstanceOf(
            $fqcn,
            $this->getFactory()->createNewRouteMatchSpecification($fqcn)
        );
    }

    /**
     * @since 4.9.71.p
     * @throws InvalidDataTypeException
     * @throws InvalidInterfaceException
     * @throws InvalidArgumentException
     * @throws Exception
     * @throws ReflectionException
     */
    public function testCreate()
    {
        $fqcn = 'EventEspresso\core\domain\entities\routing\specifications\admin\EspressoEventEditorEdit';
        $this->assertInstanceOf(
            $fqcn,
            RouteMatchSpecificationFactory::create($fqcn)
        );
    }
}
// location: /tests/testcases/core/services/routing/RouteMatchSpecificationFactoryTest.php