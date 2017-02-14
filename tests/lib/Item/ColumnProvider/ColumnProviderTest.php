<?php

namespace Netgen\ContentBrowser\Tests\Item\ColumnProvider;

use Netgen\ContentBrowser\Config\Configuration;
use Netgen\ContentBrowser\Item\ColumnProvider\ColumnProvider;
use Netgen\ContentBrowser\Item\Renderer\ItemRendererInterface;
use Netgen\ContentBrowser\Tests\Stubs\ColumnValueProvider;
use Netgen\ContentBrowser\Tests\Stubs\Item;
use PHPUnit\Framework\TestCase;

class ColumnProviderTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemRendererMock;

    /**
     * @var \Netgen\ContentBrowser\Config\ConfigurationInterface
     */
    protected $config;

    /**
     * @var \Netgen\ContentBrowser\Item\ColumnProvider\ColumnProvider
     */
    protected $columnProvider;

    public function setUp()
    {
        $this->itemRendererMock = $this->createMock(ItemRendererInterface::class);

        $this->config = new Configuration(
            'value',
            array(
                'columns' => array(
                    'column' => array(
                        'value_provider' => 'provider',
                    ),
                ),
            )
        );

        $this->columnProvider = new ColumnProvider(
            $this->itemRendererMock,
            $this->config,
            array('provider' => new ColumnValueProvider())
        );
    }

    /**
     * @covers \Netgen\ContentBrowser\Item\ColumnProvider\ColumnProvider::__construct
     * @expectedException \Netgen\ContentBrowser\Exceptions\InvalidArgumentException
     */
    public function testConstructorThrowsInvalidArgumentException()
    {
        $this->columnProvider = new ColumnProvider(
            $this->itemRendererMock,
            $this->config,
            array('other' => new ColumnValueProvider())
        );
    }

    /**
     * @covers \Netgen\ContentBrowser\Item\ColumnProvider\ColumnProvider::provideColumns
     * @covers \Netgen\ContentBrowser\Item\ColumnProvider\ColumnProvider::provideColumn
     */
    public function testProvideColumns()
    {
        $this->assertEquals(
            array('column' => 'some_value'),
            $this->columnProvider->provideColumns(new Item())
        );
    }

    /**
     * @covers \Netgen\ContentBrowser\Item\ColumnProvider\ColumnProvider::provideColumns
     * @covers \Netgen\ContentBrowser\Item\ColumnProvider\ColumnProvider::provideColumn
     */
    public function testProvideColumnsWithTemplate()
    {
        $this->config = new Configuration(
            'value',
            array(
                'columns' => array(
                    'column' => array(
                        'template' => 'template.html.twig',
                    ),
                ),
            )
        );

        $this->columnProvider = new ColumnProvider(
            $this->itemRendererMock,
            $this->config,
            array()
        );

        $this->itemRendererMock
            ->expects($this->once())
            ->method('renderItem')
            ->with($this->equalTo(new Item()), $this->equalTo('template.html.twig'))
            ->will($this->returnValue('rendered column'));

        $this->assertEquals(
            array('column' => 'rendered column'),
            $this->columnProvider->provideColumns(new Item())
        );
    }
}