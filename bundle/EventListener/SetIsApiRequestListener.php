<?php

declare(strict_types=1);

namespace Netgen\Bundle\ContentBrowserBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use function str_starts_with;

final class SetIsApiRequestListener implements EventSubscriberInterface
{
    public const API_FLAG_NAME = 'ngcb_is_api_request';

    private const API_ROUTE_PREFIX = 'ngcb_api_';

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::REQUEST => ['onKernelRequest', 30]];
    }

    /**
     * Sets the self::API_FLAG_NAME flag if this is a REST API request.
     *
     * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
     */
    public function onKernelRequest($event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();
        $currentRoute = $request->attributes->get('_route', '');
        if (!str_starts_with($currentRoute, self::API_ROUTE_PREFIX)) {
            return;
        }

        $request->attributes->set(self::API_FLAG_NAME, true);
    }
}
