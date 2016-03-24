<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\EventListener;

use Netgen\Bundle\ContentBrowserBundle\EventListener\SetCurrentConfigListener;
use Netgen\Bundle\ContentBrowserBundle\Config\ConfigLoaderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Request;

class SetCurrentConfigListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\Config\ConfigLoaderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configLoaderMock;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $containerMock;

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\EventListener\SetCurrentConfigListener
     */
    protected $eventListener;

    public function setUp()
    {
        $this->configLoaderMock = self::getMock(ConfigLoaderInterface::class);
        $this->containerMock = self::getMock(ContainerInterface::class);

        $this->eventListener = new SetCurrentConfigListener($this->configLoaderMock);
        $this->eventListener->setContainer($this->containerMock);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetCurrentConfigListener::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetCurrentConfigListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        self::assertEquals(
            array(KernelEvents::REQUEST => 'onKernelRequest'),
            $this->eventListener->getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetCurrentConfigListener::onKernelRequest
     */
    public function testOnKernelRequest()
    {
        $kernelMock = $this->getMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set('_route', 'netgen_content_browser_api_test');
        $request->attributes->set('config', 'config_name');

        $event = new GetResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST
        );

        $config = array(
            'converter' => 'converter_service',
            'backend' => 'backend_service',
        );

        $this->configLoaderMock
            ->expects($this->at(0))
            ->method('loadConfig')
            ->with($this->equalTo('config_name'))
            ->will($this->returnValue($config));

        $this->containerMock
            ->expects($this->at(0))
            ->method('set')
            ->with(
                $this->equalTo('netgen_content_browser.current_config'),
                $this->equalTo($config)
            );

        $this->containerMock
            ->expects($this->at(1))
            ->method('get')
            ->with($this->equalTo('converter_service'))
            ->will($this->returnValue('converter_service_object'));

        $this->containerMock
            ->expects($this->at(2))
            ->method('set')
            ->with(
                $this->equalTo('netgen_content_browser.current_converter'),
                $this->equalTo('converter_service_object')
            );

        $this->containerMock
            ->expects($this->at(3))
            ->method('get')
            ->with($this->equalTo('backend_service'))
            ->will($this->returnValue('backend_service_object'));

        $this->containerMock
            ->expects($this->at(4))
            ->method('set')
            ->with(
                $this->equalTo('netgen_content_browser.current_backend'),
                $this->equalTo('backend_service_object')
            );

        $this->eventListener->onKernelRequest($event);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetCurrentConfigListener::onKernelRequest
     */
    public function testOnKernelRequestInSubRequest()
    {
        $kernelMock = $this->getMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set('_route', 'netgen_content_browser_api_test');
        $request->attributes->set('config', 'config_name');

        $event = new GetResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::SUB_REQUEST
        );

        $this->configLoaderMock
            ->expects($this->never())
            ->method('loadConfig');

        $this->containerMock
            ->expects($this->never())
            ->method('set');

        $this->eventListener->onKernelRequest($event);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetCurrentConfigListener::onKernelRequest
     */
    public function testOnKernelRequestWithNoConfig()
    {
        $kernelMock = $this->getMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set('_route', 'netgen_content_browser_api_test');

        $event = new GetResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST
        );

        $this->configLoaderMock
            ->expects($this->never())
            ->method('loadConfig');

        $this->containerMock
            ->expects($this->never())
            ->method('set');

        $this->eventListener->onKernelRequest($event);
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetCurrentConfigListener::onKernelRequest
     */
    public function testOnKernelRequestWithNoContentBrowserRequest()
    {
        $kernelMock = $this->getMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set('_route', 'some_route');

        $event = new GetResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST
        );

        $this->configLoaderMock
            ->expects($this->never())
            ->method('loadConfig');

        $this->containerMock
            ->expects($this->never())
            ->method('set');

        $this->eventListener->onKernelRequest($event);
    }
}