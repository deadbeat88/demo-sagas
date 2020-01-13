<?php
/**
 * @author  Stepan Zolotarev <zsl88.logging@gmail.com>
 */

declare(strict_types = 1);

namespace Demo\Test\Command;

/**
 * @property-read string $requestId
 * @property-read string $id
 */
final class TestCmd
{
    /**
     * @var string
     */
    public $requestId;

    /**
     * @var string
     */
    public $id;

    /**
     * @param string $requestId
     * @param string $id
     */
    public function __construct(string $requestId, string $id)
    {
        $this->requestId = $requestId;
        $this->id        = $id;
    }
}