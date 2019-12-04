<?php

declare(strict_types=1);

namespace Netgen\ContentBrowser\Tests\App;

use Netgen\ContentBrowser\Exceptions\RuntimeException;
use Symfony\Component\DependencyInjection\Container;

class MockerContainer extends Container
{
    /**
     * @var array<string, object>
     */
    private $originalServices = [];

    /**
     * @var array<string, object>
     */
    private $mockedServices = [];

    /**
     * @throws \Netgen\ContentBrowser\Exceptions\RuntimeException if the mocked service does not exist
     */
    public function mock(string $id, object $mock): object
    {
        if (!array_key_exists($id, $this->mockedServices)) {
            $service = $this->get($id);
            if ($service === null) {
                throw new RuntimeException(sprintf('"%s" service does not exist.', $service));
            }

            $this->originalServices[$id] = $service;
            $this->mockedServices[$id] = $this->services[$id] = $mock;
        }

        return $this->mockedServices[$id];
    }

    public function unmock(string $id): void
    {
        $this->services[$id] = $this->originalServices[$id];
        unset($this->originalServices[$id], $this->mockedServices[$id]);
    }

    /**
     * @return array<string, object>
     */
    public function getMockedServices(): array
    {
        return $this->mockedServices;
    }
}
