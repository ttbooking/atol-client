<?php

declare(strict_types=1);

namespace Lamoda\AtolClient\V4\DTO\Shared;

use MyCLabs\Enum\Enum;

/**
 * @method static self NONE()
 * @method static self UNKNOWN()
 * @method static self SYSTEM()
 * @method static self DRIVER()
 * @method static self TIMEOUT()
 * @method static self AGENT()
 */
final class ErrorType extends Enum
{
    protected const NONE = 'none';
    protected const UNKNOWN = 'unknown';
    protected const SYSTEM = 'system';
    protected const DRIVER = 'driver';
    protected const TIMEOUT = 'timeout';
    protected const AGENT = 'agent';
}
