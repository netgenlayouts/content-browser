<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\EventListener;

use Netgen\Bundle\ContentBrowserBundle\EventListener\SetCurrentBackendListener;
use Netgen\Bundle\ContentBrowserBundle\EventListener\SetIsApiRequestListener;
use Netgen\ContentBrowser\Backend\BackendInterface;
use Netgen\ContentBrowser\Registry\BackendRegistry;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class SetCurrentBackendListenerTest extends TestCase
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @var \Netgen\ContentBrowser\Registry\BackendRegistryInterface
     */
    protected $backendRegistry;

    /**
     * @var \Netgen\Bundle\ContentBrowserBundle\EventListener\SetCurrentBackendListener
     */
    protected $eventListener;

    public function setUp()
    {
        $this->container = new Container();
        $this->backendRegistry = new BackendRegistry();

        $this->eventListener = new SetCurrentBackendListener(
            $this->container,
            $this->backendRegistry
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetCurrentBackendListener::__construct
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetCurrentBackendListener::getSubscribedEvents
     */
    public function testGetSubscribedEvents()
    {
        $this->assertEquals(
            array(KernelEvents::REQUEST => 'onKernelRequest'),
            $this->eventListener->getSubscribedEvents()
        );
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetCurrentBackendListener::onKernelRequest
     */
    public function testOnKernelRequest()
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);
        $request->attributes->set('itemType', 'item_type');

        $event = new GetResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST
        );

        $backendMock = $this->createMock(BackendInterface::class);
        $this->backendRegistry->addBackend('item_type', $this->createMock(BackendInterface::class));

        $this->eventListener->onKernelRequest($event);

        $this->assertTrue($this->container->has('netgen_content_browser.current_backend'));
        $this->assertEquals($backendMock, $this->container->get('netgen_content_browser.current_backend'));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetCurrentBackendListener::onKernelRequest
     */
    public function testOnKernelRequestInSubRequest()
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);
        $request->attributes->set('itemType', 'item_type');

        $event = new GetResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::SUB_REQUEST
        );

        $this->eventListener->onKernelRequest($event);

        $this->assertFalse($this->container->has('netgen_content_browser.current_backend'));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetCurrentBackendListener::onKernelRequest
     */
    public function testOnKernelRequestWithNoItemType()
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, true);

        $event = new GetResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST
        );

        $this->eventListener->onKernelRequest($event);

        $this->assertFalse($this->container->has('netgen_content_browser.current_backend'));
    }

    /**
     * @covers \Netgen\Bundle\ContentBrowserBundle\EventListener\SetCurrentBackendListener::onKernelRequest
     */
    public function testOnKernelRequestWithNoContentBrowserRequest()
    {
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $request = Request::create('/');
        $request->attributes->set(SetIsApiRequestListener::API_FLAG_NAME, false);

        $event = new GetResponseEvent(
            $kernelMock,
            $request,
            HttpKernelInterface::MASTER_REQUEST
        );

        $this->eventListener->onKernelRequest($event);

        $this->assertFalse($this->container->has('netgen_content_browser.current_backend'));
    }
}