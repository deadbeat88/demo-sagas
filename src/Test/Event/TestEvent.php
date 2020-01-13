<?php
/**
 * @author  Stepan Zolotarev <zsl88.logging@gmail.com>
 */

declare(strict_types = 1);

namespace Demo\Test\Event;

/**
 * @property-read string $requestId
 * @property-read string $id
 * @property-read int    $res
 */
class TestEvent
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
     * @var int
     */
    public $res;

    /**
     * @param string $requestId
     * @param string $id
     * @param int    $res
     */
    public function __construct(string $requestId, string $id, int $res)
    {
        $this->requestId = $requestId;
        $this->id        = $id;
        $this->res       = $res;
    }
}