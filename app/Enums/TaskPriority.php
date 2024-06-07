<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static LOW()
 * @method static static MEDIUM()
 * @method static static HIGH()
 * @method static static VERY_HIGH()
 * @method static static CRITICAL()
 */
final class TaskPriority extends Enum
{
    const LOW = 1;
    const MEDIUM = 2;
    const HIGH = 3;
    const VERY_HIGH = 4;
    const CRITICAL = 5;
}
