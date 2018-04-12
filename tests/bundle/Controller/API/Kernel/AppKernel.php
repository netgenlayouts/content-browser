<?php

namespace Netgen\Bundle\ContentBrowserBundle\Tests\Controller\API\Kernel;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

final class AppKernel extends Kernel
{
    public function registerBundles()
    {
        return array(
            // Symfony

            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
            new \Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),

            // Netgen Content Browser

            new \Netgen\Bundle\ContentBrowserBundle\NetgenContentBrowserBundle(),
        );
    }

    public function getProjectDir()
    {
        return __DIR__;
    }

    public function getCacheDir()
    {
        return sys_get_temp_dir() . '/ngcb/cache';
    }

    public function getLogDir()
    {
        return sys_get_temp_dir() . '/ngcb/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config/config.yml');
    }

    protected function getContainerBaseClass()
    {
        return '\\' . MockerContainer::class;
    }

    protected function prepareContainer(ContainerBuilder $container)
    {
        parent::prepareContainer($container);

        if (Kernel::VERSION_ID < 30200) {
            // @deprecated Symfony 2.8 does not have kernel.project_dir parameter,
            // so we need to set the parameter to the container manually
            $container->setParameter('kernel.project_dir', __DIR__);
        }
    }
}