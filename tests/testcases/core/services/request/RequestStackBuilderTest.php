<?php

namespace EventEspresso\tests\testcases\core\services\request;

use EE_Error;
use EE_UnitTestCase;
use EventEspresso\core\exceptions\InvalidDataTypeException;
use EventEspresso\core\exceptions\InvalidInterfaceException;
use EventEspresso\core\services\request\InvalidRequestStackMiddlewareException;
use EventEspresso\tests\mocks\core\services\request\RequestStackBuilderMock;
use EventEspresso\tests\mocks\core\services\request\RequestStackCoreAppMock;
use Exception;
use InvalidArgumentException;

class RequestStackBuilderTest extends EE_UnitTestCase
{

    /**
     * @var RequestStackBuilderMock $request_stack_builder
     */
    private $request_stack_builder;

    /**
     * @var string $obi_wan_kenobi
     */
    private $obi_wan_kenobi;

    /**
     * @var string $general_grievous
     */
    private $general_grievous;


    /**
     * @throws InvalidDataTypeException
     * @throws InvalidInterfaceException
     * @throws InvalidArgumentException
     */
    public function set_up()
    {
        parent::set_up();
        $this->request_stack_builder = $this->loader->getShared(
            'EventEspresso\tests\mocks\core\services\request\RequestStackBuilderMock',
            [$this->loader]
        );
    }


    /**
     * @return RequestStackCoreAppMock
     */
    public function getCoreApp(): RequestStackCoreAppMock
    {
        return $this->loader->getShared(
            'EventEspresso\tests\mocks\core\services\request\RequestStackCoreAppMock'
        );
    }


    /**
     * @return array
     */
    public function getNotices(): array
    {
        $notices = EE_Error::get_vanilla_notices();
        if (isset($notices['errors']['RequestStackCoreAppMock - handleRequest - 34'])) {
            $notices['errors'][] = str_replace(
                '<br/><span class="tiny-text">RequestStackCoreAppMock - handleRequest - 34</span>',
                '',
                $notices['errors']['RequestStackCoreAppMock - handleRequest - 34']
            );
            unset($notices['errors']['RequestStackCoreAppMock - handleRequest - 34']);
        }
        return $notices;
    }


    /**
     * @return void
     */
    public function setMiddlewareFQCNs()
    {
        $this->obi_wan_kenobi   = 'EventEspresso\tests\mocks\core\services\request\ObiWanKenobiWare';
        $this->general_grievous = 'EventEspresso\tests\mocks\core\services\request\GeneralGrievousWare';
    }


    /**
     * @param bool $legacy
     * @return void
     */
    public function addMiddleware(bool $legacy = false)
    {
        // but first, clear out any existing middleware
        while ($this->request_stack_builder->count() > 0) {
            $this->request_stack_builder->pop();
        }
        $this->assertCount(0, $this->request_stack_builder);
        $this->setMiddlewareFQCNs();
        if ($legacy) {
            $this->request_stack_builder->push([0, $this->general_grievous]);
            $this->request_stack_builder->push([1, $this->obi_wan_kenobi]);
        } else {
            $this->request_stack_builder->push([$this->general_grievous, []]);
            $this->request_stack_builder->push([$this->obi_wan_kenobi, []]);
        }
    }


    /**
     * @return void
     */
    public function testRequestStackBuilderAsSplDoublyLinkedList()
    {
        $this->addMiddleware();
        // list should be FILO, so first in is at bottom of the stack
        $first      = $this->request_stack_builder->bottom();
        $first_fqcn = reset($first);
        $this->assertEquals($this->general_grievous, $first_fqcn);
        $last      = $this->request_stack_builder->top();
        $last_fqcn = reset($last);
        $this->assertEquals($this->obi_wan_kenobi, $last_fqcn);
    }


    /**
     * returns following parameters:
     *      $middleware_app middleware app class details (FQCN, args array)
     *      $recurse        true may call validateMiddlewareAppDetails() twice
     *      $expected       expected middleware app class
     *
     * @return array
     */
    public function middlewareAppProvider(): array
    {
        $this->setMiddlewareFQCNs();
        return [
            // empty array
            [[], false, ''],
            // arguments in wrong order
            [[[], $this->obi_wan_kenobi], true, $this->obi_wan_kenobi],
            // arguments in wrong order, no recurse
            [[[], $this->obi_wan_kenobi], false, ''],
            // legacy middleware
            [[0, $this->obi_wan_kenobi], true, $this->obi_wan_kenobi],
            // legacy middleware, no recurse
            [[0, $this->obi_wan_kenobi], false, ''],
            // invalid FQCN
            [['invalid/FQCN', []], false, ''],
            // all good
            [[$this->obi_wan_kenobi, []], true, $this->obi_wan_kenobi],
            [[$this->general_grievous, []], true, $this->general_grievous],
        ];
    }


    /**
     * @dataProvider middlewareAppProvider
     * @param array  $middleware_app
     * @param bool   $recurse
     * @param string $expected
     * @throws InvalidRequestStackMiddlewareException
     */
    public function testValidateMiddlewareAppDetails(array $middleware_app, bool $recurse, string $expected)
    {
        if ($recurse === false) {
            $this->setExceptionExpected('EventEspresso\core\services\request\InvalidRequestStackMiddlewareException');
            $middleware_app_class = $this->request_stack_builder->validateMiddlewareAppDetails(
                $middleware_app
            );
            $this->assertEquals(
                null,
                $middleware_app_class,
                'An InvalidRequestStackMiddlewareException was expected but not thrown!'
            );
            return;
        }
        $middleware_app       = $this->request_stack_builder->validateMiddlewareAppDetails(
            $middleware_app,
            true
        );
        $middleware_app_class = array_shift($middleware_app);
        $this->assertEquals($expected, $middleware_app_class);
    }


    /**
     * @return void
     * @throws Exception
     */
    public function testRequestStackBuilderResolve()
    {
        $this->addMiddleware();
        $this->TheBattleOfUtapau();
    }


    /**
     * @return void
     * @throws Exception
     */
    public function testRequestStackBuilderResolveWithLegacyMiddleware()
    {
        $this->addMiddleware(true);
        $this->TheBattleOfUtapau();
    }


    /**
     * @return void
     * @throws Exception
     */
    public function TheBattleOfUtapau()
    {
        $request_stack = $this->request_stack_builder->resolve($this->getCoreApp());
        $this->assertInstanceOf(
            'EventEspresso\core\services\request\RequestStack',
            $request_stack
        );
        $this->assertEquals(0, EE_Error::has_notices());
        $request_stack->handleRequest($this->request, $this->getResponse());
        $notices = $this->getNotices();
        $this->assertCount(1, $notices['success'], var_export($notices['success'], true));
        $this->assertEquals('Hello There!', reset($notices['success']));
        $this->assertCount(1, $notices['attention'], var_export($notices['attention'], true));
        $this->assertEquals('General Kenobi!', reset($notices['attention']));
        $this->assertCount(1, $notices['errors']);
        $this->assertEquals(
            'Back away! I will deal with this Jedi slime myself!',
            reset($notices['errors']),
            'notices array: ' . var_export($notices, true)
        );
        $request_stack->handleResponse();
        $notices = $this->getNotices();
        $this->assertCount(2, $notices['success']);
        $this->assertEquals("Now, let's get a move on. We've got a battle to win here.", $notices['success'][1]);
        // now clear all notices
        EE_Error::reset_notices();
    }
}
// location: tests/testcases/core/services/requests/RequestStackBuilderTest.php
