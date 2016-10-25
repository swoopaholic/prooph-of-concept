<?php
declare(strict_types=1);

namespace {
    require_once 'vendor/autoload.php';
}

namespace {

    use Pimple\Container;
    use Prooph\ServiceBus\Exception\CommandDispatchException;
    use Swoopaholic\Infrastructure\DependencyInjection\ServiceProvider;

    use Swoopaholic\Application\AddText;
    use Swoopaholic\Domain\StreamId;
    use Swoopaholic\Domain\Text;

    $container = new Container();
    $container->register(new ServiceProvider());

    /** @var Prooph\ServiceBus\CommandBus $commandBus */
    $commandBus = $container['command_bus'];

    $id = new StreamId('c7a40ca6-e62a-4151-abbc-5a0a67db8f51');
    $commandBus->dispatch(new AddText($id, new Text("It works\n")));
    $commandBus->dispatch(new AddText($id, new Text("Like a charm!\n")));

    try {
        $commandBus->dispatch(new AddText($id, new Text("")));
    } catch (CommandDispatchException $e) {
        echo "Application constraints work: " . $e->getPrevious()->getMessage() . "\n";
    }
}
