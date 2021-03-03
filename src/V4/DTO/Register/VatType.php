<?php

declare(strict_types=1);

namespace Lamoda\AtolClient\V4\DTO\Register;

use MyCLabs\Enum\Enum;

/**
 * @method static self NONE()
 * @method static self VAT0()
 * @method static self VAT10()
 * @method static self VAT18()
 * @method static self VAT20()
 * @method static self VAT110()
 * @method static self VAT118()
 * @method static self VAT120()
 */
final class VatType extends Enum
{
    protected const NONE = 'none';
    protected const VAT0 = 'vat0';
    protected const VAT10 = 'vat10';
    protected const VAT18 = 'vat18';
    protected const VAT20 = 'vat20';
    protected const VAT110 = 'vat110';
    protected const VAT118 = 'vat118';
    protected const VAT120 = 'vat120';
}
