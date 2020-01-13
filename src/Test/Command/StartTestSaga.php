<?php
/**
 * @author  Stepan Zolotarev <zsl88.logging@gmail.com>
 */

declare(strict_types = 1);

namespace Demo\Test\Command;

/**
 * @property-read string $requestId
 */
final class StartTestSaga
{
    /**
     * @var string
     */
    public $requestId;


    public function __construct(string $requestId)
    {
        $this->requestId = $requestId;
    }
}