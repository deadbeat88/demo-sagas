<?php

/**
 * @author  Stepan Zolotarev <zsl88.logging@gmail.com>
 */

declare(strict_types = 1);

include_once __DIR__ . '/../vendor/autoload.php';

$publisher = new \ToolsPublisher(__DIR__ . '/../.env');

for ($i=0; $i < 50; $i++)
{
    $command = new \Demo\Test\Command\StartTestSaga(
        \ServiceBus\Common\uuid()
    );

    $publisher->sendMessage($command);

    echo \sprintf("Команда \"%s\" отправлена\n", \get_class($command));
}
