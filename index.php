<?php
namespace {
    require_once 'vendor/autoload.php';
}

namespace {
    use Pimple\Container;
    use Swoopaholic\Domain\AddText;
    use Swoopaholic\Infrastructure\DependencyInjection\ServiceProvider;

    $container = new Container();
    $container->register(new ServiceProvider());

    /** @var Prooph\ServiceBus\CommandBus $commandBus */
    $commandBus = $container['command_bus'];

    $id = 'c7a40ca6-e62a-4151-abbc-5a0a67db8f51';
    $commandBus->dispatch(new AddText($id, "It works\n"));
    $commandBus->dispatch(new AddText($id, "Like a charm!\n"));
}
