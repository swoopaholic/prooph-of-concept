<?php
namespace Swoopaholic\Infrastructure\DependencyInjection;

use Acclimate\Container\ContainerAcclimator;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Prooph\Common\Event\ProophActionEventEmitter;
use Prooph\EventStore\Adapter\InMemoryAdapter;
use Prooph\EventStore\Aggregate\AggregateType;
use Prooph\EventStore\Aggregate\ConfigurableAggregateTranslator;
use Prooph\EventStore\EventStore;
use Prooph\EventStore\Stream\Stream;
use Prooph\EventStore\Stream\StreamName;
use Prooph\ServiceBus\CommandBus;
use Prooph\ServiceBus\EventBus;
use Prooph\ServiceBus\Plugin\InvokeStrategy\HandleCommandStrategy;
use Prooph\ServiceBus\Plugin\InvokeStrategy\OnEventStrategy;
use Prooph\ServiceBus\Plugin\Router\CommandRouter;
use Prooph\ServiceBus\Plugin\Router\EventRouter;
use Prooph\ServiceBus\Plugin\ServiceLocatorPlugin;
use Swoopaholic\Application\HandleAddText;
use Swoopaholic\Domain\Serializable;
use Swoopaholic\Infrastructure\EventStore\EventPublisher;
use Swoopaholic\Infrastructure\EventStore\Message;
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

        $this->registerCommandRouter($pimple);
        $this->registerCommandRoutes($pimple);
        $this->registerCommandBus($pimple, $container);

        $this->registerEventRouter($pimple);
        $this->registerEventRoutes($pimple);
        $this->registerEventBus($pimple, $container);

        $this->registerEventStore($pimple);
        $this->registerRepositories($pimple);
    }

    private function registerCommandRouter($pimple)
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

    private function registerCommandBus($pimple, $container)
    {
        $pimple['command_bus'] = function($c) use ($container) {
            $commandBus = new CommandBus();
            $commandBus->utilize(new HandleCommandStrategy());
            $commandBus->utilize(new ServiceLocatorPlugin($container));
            $commandBus->utilize($c['command_router']);
            return $commandBus;
        };
    }

    private function registerEventRouter($pimple)
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

    private function registerEventBus($pimple, $container)
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
            /** @var EventStore $eventStore */
            $eventStore = $c['event_store'];
            $eventStore->beginTransaction();
            $eventStore->create(new Stream(new StreamName('event_stream'), new \ArrayIterator()));
            $eventStore->commit();

            return new StreamRepository(
                $eventStore,
                AggregateType::fromAggregateRootClass('Swoopaholic\Domain\Stream'),
                $c['event_store.aggregate_translator']
            );
        };
    }

    private function registerEventStore($container)
    {
        $container['event_store'] = function($c) {
            $eventStore = new EventStore(new InMemoryAdapter(), new ProophActionEventEmitter());

            $publisher = new EventPublisher($c['event_bus']);
            $publisher->setUp($eventStore);

            return $eventStore;
        };

        $container['event_store.aggregate_translator'] = function($c) {
            $factoryFunction = function($message) {
                return new Message(get_class($message), $message->serialize());
            };

            $convertFunction = function(Message $message) {
                /** @var Serializable $class */
                $class = $message->messageName();
                return $class::fromSerializedData($message->payload());
            };

            return new ConfigurableAggregateTranslator(
                null,
                null,
                null,
                null,
                null,
                $factoryFunction,
                $convertFunction
            );
        };
    }
}
