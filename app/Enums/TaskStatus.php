<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static TODO()
 * @method static static DONE()
 */
final class TaskStatus extends Enum
{
    const TODO = 'todo';
    const DONE = 'done';
}
