<?php
/**
 * @author  Stepan Zolotarev <zsl88.logging@gmail.com>
 */

declare(strict_types = 1);

namespace Demo\Test\Command;

/**
 * @property-read string $requestId
 * @property-read int    $order
 */
final class ExternalTestCmd
{
    /**
     * @var string
     */
    public $requestId;

    /**
     * @var int
     */
    public $order;

    /**
     * @param string $requestId
     * @param int    $order
     */
    public function __construct(string $requestId, int $order)
    {
        $this->requestId = $requestId;
        $this->order     = $order;
    }
}