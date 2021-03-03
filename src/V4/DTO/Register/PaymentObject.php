<?php

declare(strict_types=1);

namespace Lamoda\AtolClient\V4\DTO\Register;

use MyCLabs\Enum\Enum;

/**
 * @method static self COMMODITY()
 * @method static self EXCISE()
 * @method static self JOB()
 * @method static self SERVICE()
 * @method static self GAMBLING_BET()
 * @method static self GAMBLING_PRIZE()
 * @method static self LOTTERY()
 * @method static self LOTTERY_PRIZE()
 * @method static self INTELLECTUAL_ACTIVITY()
 * @method static self PAYMENT()
 * @method static self AGENT_COMMISSION()
 * @method static self COMPOSITE()
 * @method static self ANOTHER()
 * @method static self PROPERTY_RIGHT()
 * @method static self NON_OPERATING_GAIN()
 * @method static self INSURANCE_PREMIUM()
 * @method static self SALES_TAX()
 * @method static self RESORT_FEE()
 */
final class PaymentObject extends Enum
{
    protected const COMMODITY = 'commodity';
    protected const EXCISE = 'excise';
    protected const JOB = 'job';
    protected const SERVICE = 'service';
    protected const GAMBLING_BET = 'gambling_bet';
    protected const GAMBLING_PRIZE = 'gambling_prize';
    protected const LOTTERY = 'lottery';
    protected const LOTTERY_PRIZE = 'lottery_prize';
    protected const INTELLECTUAL_ACTIVITY = 'intellectual_activity';
    protected const PAYMENT = 'payment';
    protected const AGENT_COMMISSION = 'agent_commission';
    protected const COMPOSITE = 'composite';
    protected const ANOTHER = 'another';
    protected const PROPERTY_RIGHT = 'property_right';
    protected const NON_OPERATING_GAIN = 'non-operating_gain';
    protected const INSURANCE_PREMIUM = 'insurance_premium';
    protected const SALES_TAX = 'sales_tax';
    protected const RESORT_FEE = 'resort_fee';
}
