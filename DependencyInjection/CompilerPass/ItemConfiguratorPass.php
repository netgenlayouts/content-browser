<?php

namespace Netgen\Bundle\ContentBrowserBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use RuntimeException;

class ItemConfiguratorPass implements CompilerPassInterface
{
    const SERVICE_NAME = 'netgen_content_browser.item_configurator';
    const TAG_NAME = 'netgen_content_browser.item_configurator.handler';

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(self::SERVICE_NAME)) {
            return;
        }

        $itemConfigurator = $container->findDefinition(self::SERVICE_NAME);
        $handlerServices = $container->findTaggedServiceIds(self::TAG_NAME);

        $handlers = array();
        foreach ($handlerServices as $serviceName => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['value_type'])) {
                    throw new RuntimeException(
                        "Configurator handler definition must have a 'value_type' attribute in its' tag."
                    );
                }

                $handlers[$tag['value_type']] = new Reference($serviceName);
            }
        }

        $itemConfigurator->replaceArgument(1, $handlers);
    }
}