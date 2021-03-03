<?php

declare(strict_types=1);

namespace Lamoda\AtolClient\V4\DTO\Register;

use Paillechat\Enum\Enum;

/**
 * @method static self OSN()
 * @method static self USN_INCOME()
 * @method static self USN_INCOME_OUTCOME()
 * @method static self ENVD()
 * @method static self ESN()
 * @method static self PATENT()
 */
final class Sno extends Enum
{
    protected const OSN = 'osn';
    protected const USN_INCOME = 'usn_income';
    protected const USN_INCOME_OUTCOME = 'usn_income_outcome';
    protected const ENVD = 'envd';
    protected const ESN = 'esn';
    protected const PATENT = 'patent';
}
