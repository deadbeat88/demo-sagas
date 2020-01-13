<?php

/**
 * @author  Stepan Zolotarev <zsl88.logging@gmail.com>
 */

declare(strict_types = 1);

namespace Demo\Test\Service;

use Demo\Test\Command\ExternalTestCmd;
use Demo\Test\Command\StartTestSaga;
use Demo\Test\Command\TestCmd;
use Demo\Test\Event\TestEvent;
use Demo\Test\Identifier\TestSagaId;
use Demo\Test\TestSaga;
use Psr\Log\LogLevel;
use ServiceBus\Common\Context\ServiceBusContext;
use ServiceBus\Sagas\Module\SagasProvider;
use ServiceBus\Services\Annotations;

class TestService
{
    /**
     * @Annotations\CommandHandler()
     *
     * @param StartTestSaga     $command
     * @param ServiceBusContext $context
     * @param SagasProvider     $sagasProvider
     *
     * @return \Generator
     */
    public function startTest(
        StartTestSaga $command,
        ServiceBusContext $context,
        SagasProvider $sagasProvider
    ): \Generator {
        yield $sagasProvider->start(
            new TestSagaId($command->requestId, TestSaga::class),
            $command,
            $context
        );

        $context->logContextMessage("Start saga for request ID = {$command->requestId}", [], LogLevel::NOTICE);
    }

    /**
     * @Annotations\CommandHandler()
     *
     * @param TestCmd           $command
     * @param ServiceBusContext $context
     *
     * @return \Generator
     * @throws \Throwable
     */
    public function testCmd(
        TestCmd $command,
        ServiceBusContext $context
    ): \Generator {
        $res = \random_int(0, 1000);

        $context->logContextMessage(
            "Event {$command->id} for requestId = {$command->requestId} with res = {$res}",
            [],
            LogLevel::NOTICE
        );

        yield $context->delivery(
            new TestEvent($command->requestId, $command->id, $res)
        );
    }

    /**
     * @Annotations\CommandHandler()
     *
     * @param ExternalTestCmd   $command
     * @param ServiceBusContext $context
     * @param SagasProvider     $sagasProvider
     *
     * @return \Generator
     * @throws \Throwable
     */
    public function externalTestCmd(
        ExternalTestCmd $command,
        ServiceBusContext $context,
        SagasProvider $sagasProvider
    ): \Generator {
        /**
         * @var TestSaga|null $saga
         */
        $saga = yield $sagasProvider->obtain(
            new TestSagaId($command->requestId, TestSaga::class),
            $context
        );

        if (null !== $saga) {
            if (null === $saga->closedAt()) {
                $saga->externalUpdate(\bin2hex(\random_bytes(16)));

                $context->logContextMessage(
                    "Update of saga {$saga->id()->toString()} for external cmd with order = {$command->order}",
                    [],
                    LogLevel::NOTICE
                );
            } else {
                $context->logContextMessage(
                    "Saga {$saga->id()->toString()} already closed, cmd order = {$command->order}",
                    [],
                    LogLevel::NOTICE
                );
            }

            // Always need to save after obtain to release mutex
            yield $sagasProvider->save($saga, $context);

            return;
        }

        throw new \RuntimeException(
            "Saga with id {$command->requestId} not found with external cmd with order = {$command->order}"
        );
    }
}
