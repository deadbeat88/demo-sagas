<?php

/**
 * @author  Stepan Zolotarev <zsl88.logging@gmail.com>
 */

declare(strict_types = 1);

namespace Demo\Test;

use Demo\Test\Command\ExternalTestCmd;
use Demo\Test\Command\StartTestSaga;
use Demo\Test\Command\TestCmd;
use Demo\Test\Event\TestEvent;
use ServiceBus\Sagas\Configuration\Annotations;
use ServiceBus\Sagas\Saga;
use function ServiceBus\Common\uuid;

/**
 * @Annotations\SagaHeader(
 *     idClass="Demo\Test\Identifier\TestSagaId",
 *     containingIdProperty="requestId",
 *     expireDateModifier="+1 day"
 * )
 */
final class TestSaga extends Saga
{
    /**
     * @var string
     */
    private $cmdOneId;

    /**
     * @var string
     */
    private $cmdTwoId;

    /**
     * @var int|null
     */
    private $eventOneResult;

    /**
     * @var int|null
     */
    private $eventTwoResult;

    /**
     * @var string|null
     */
    private $externalResult1;

    /**
     * @var string|null
     */
    private $externalResult2;

    /**
     * @var string|null
     */
    private $externalResult3;

    /**
     * @inheritdoc
     */
    public function start(object $command): void
    {
        /** @var StartTestSaga $command */
        $this->cmdOneId = uuid();
        $this->cmdTwoId = uuid();

        $this->doSendTestCmd($this->cmdOneId);
        $this->doSendTestCmd($this->cmdTwoId);
    }

    /**
     * @param string $data
     */
    public function externalUpdate(string $data): void
    {
        if (null === $this->externalResult1) {
            $this->externalResult1 = $data;
        } elseif (null === $this->externalResult2) {
            $this->externalResult2 = $data;
        } else {
            $this->externalResult3 = $data;
        }

        if (null === $this->eventOneResult || null === $this->eventTwoResult) {
            return;
        }

        if (null === $this->externalResult1 || null === $this->externalResult2 || null === $this->externalResult3) {
            return;
        }

        $sum = $this->eventOneResult + $this->eventTwoResult;

        $this->makeCompleted(
            "Sum of internal results = {$sum} 
            with external results = [{$this->externalResult1}, {$this->externalResult2}, {$this->externalResult3}]"
        );
    }

    /**
     * @noinspection PhpUnusedPrivateMethodInspection
     *
     * @Annotations\SagaEventListener()
     *
     * @param TestEvent $event
     *
     * @return void
     */
    private function onTestEvent(TestEvent $event): void
    {
        if ($event->id === $this->cmdOneId) {
            $this->eventOneResult = $event->res;
        }

        if ($event->id === $this->cmdTwoId) {
            $this->eventTwoResult = $event->res;
        }

        if (null === $this->eventOneResult || null === $this->eventTwoResult) {
            return;
        }

        for ($i = 0; $i < 5; $i++) {
            $this->fire(
                new ExternalTestCmd($this->id()->toString(), $i)
            );
        }
    }

    /**
     * @param string $id
     *
     * @return void
     */
    private function doSendTestCmd(string $id): void
    {
        $this->fire(
            new TestCmd($this->id()->toString(), $id)
        );
    }
}