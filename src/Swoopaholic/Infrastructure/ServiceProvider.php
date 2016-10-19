<?php
namespace Swoopaholic\Infrastructure;

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
use Swoopaholic\Application\AggregateRepository;
use Swoopaholic\Application\HandleEchoText;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['aggregate_repository'] = function() {
            return new AggregateRepository();
        };

        $pimple['echo_text.handler'] = function($c) {
            return new HandleEchoText(
                $c['aggregate_repository'],
                $c['event_bus']
            );
        };

        $pimple['echo_projector'] = function() {
            return new EchoProjector();
        };

        $pimple['command_bus'] = $this->buildCommandBus($pimple);

        $pimple['event_bus'] = $this->buildEventBus($pimple);
    }

    private function buildCommandBus($container)
    {
        $acclimator = new ContainerAcclimator();

        $commandBus = new CommandBus();

        $commandBus->utilize(new HandleCommandStrategy());

        $container = $acclimator->acclimate($container);
        $commandBus->utilize(new ServiceLocatorPlugin($container));

        $router = new CommandRouter();
        $router->route('Swoopaholic\Domain\EchoText')
            ->to('echo_text.handler');

        $commandBus->utilize($router);
        return $commandBus;
    }

    private function buildEventBus($container)
    {
        $acclimator = new ContainerAcclimator();

        $eventBus = new EventBus();
        $eventBus->utilize(new OnEventStrategy());

        $container = $acclimator->acclimate($container);
        $eventBus->utilize(new ServiceLocatorPlugin($container));

        $router = new EventRouter();
        $router->route('Swoopaholic\Domain\TextWasAddedToStream')
            ->to('echo_projector');

        $eventBus->utilize($router);
        return $eventBus;
    }
}
