<?php
namespace {
    require_once 'vendor/autoload.php';
}

namespace {

    use Pimple\Container;
    use Prooph\ServiceBus\CommandBus;
    use Swoopaholic\Domain\EchoText;
    use Swoopaholic\Infrastructure\ServiceProvider;

    $container = new Container();
    $container->register(new ServiceProvider());

    /** @var CommandBus $commandBus */
    $commandBus = $container['command_bus'];

    $id = 1;
    $commandBus->dispatch(new EchoText($id, "It works\n"));
}
