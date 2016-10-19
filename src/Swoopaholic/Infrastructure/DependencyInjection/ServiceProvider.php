<?php
namespace Swoopaholic\Infrastructure\DependencyInjection;

use Acclimate\Container\ContainerAcclimator;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Prooph\ServiceBus\CommandBus;
use Prooph\ServiceBus\EventBus;
use Prooph\ServiceBus\Plugin\InvokeStrategy\HandleCommandStrategy;
use Prooph\ServiceBus\Plugin\InvokeStrategy\OnEventStrategy;
use Prooph\ServiceBus\Plugin\Router\CommandRouter;
use Prooph\ServiceBus\Plugin\Router\EventRouter;
use Prooph\ServiceBus\Plugin\ServiceLocatorPlugin;
use Swoopaholic\Application\HandleAddText;
use Swoopaholic\Infrastructure\Projector\EchoProjector;
use Swoopaholic\Infrastructure\Repository\StreamRepository;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $acclimator = new ContainerAcclimator();
        $container = $acclimator->acclimate($pimple);

        $this->registerCommandHandlers($pimple);
        $this->registerEventHandlers($pimple);

        $this->buildCommandRouter($pimple);
        $this->registerCommandRoutes($pimple);
        $this->buildCommandBus($pimple, $container);

        $this->buildEventRouter($pimple);
        $this->registerEventRoutes($pimple);
        $this->buildEventBus($pimple, $container);

        $this->registerRepositories($pimple);
    }

    private function buildCommandRouter($pimple)
    {
        $pimple['command_router'] = function($c) {
            $router = new CommandRouter($c['command_routes']);
            return $router;
        };
    }

    private function registerCommandRoutes($pimple)
    {
        $pimple['command_routes'] = function($c) {
            return [
                'Swoopaholic\Domain\AddText' => 'add_text.handler'
            ];
        };
    }

    private function buildCommandBus($pimple, $container)
    {
        $pimple['command_bus'] = function($c) use ($container) {
            $commandBus = new CommandBus();
            $commandBus->utilize(new HandleCommandStrategy());
            $commandBus->utilize(new ServiceLocatorPlugin($container));
            $commandBus->utilize($c['command_router']);
            return $commandBus;
        };
    }

    private function buildEventRouter($pimple)
    {
        $pimple['event_router'] = function($c) {
            $router = new EventRouter($c['event_routes']);
            return $router;
        };
    }

    private function registerEventRoutes($pimple)
    {
        $pimple['event_routes'] = function($c) {
            return [
                'Swoopaholic\Domain\TextWasAddedToStream' => 'echo_projector'
            ];
        };
    }

    private function buildEventBus($pimple, $container)
    {
        $pimple['event_bus'] = function($c) use ($container) {
            $eventBus = new EventBus();
            $eventBus->utilize(new OnEventStrategy());
            $eventBus->utilize(new ServiceLocatorPlugin($container));
            $eventBus->utilize($c['event_router']);
            return $eventBus;
        };
    }

    private function registerCommandHandlers($pimple)
    {
        $pimple['add_text.handler'] = function($c) {
            return new HandleAddText(
                $c['stream_repository']
            );
        };
    }

    private function registerEventHandlers($pimple)
    {
        $pimple['echo_projector'] = function() {
            return new EchoProjector();
        };
    }

    private function registerRepositories($pimple)
    {
        $pimple['stream_repository'] = function($c) {
            return new StreamRepository($c['event_bus']);
        };
    }
}
