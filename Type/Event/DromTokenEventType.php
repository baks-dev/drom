<?php

declare(strict_types=1);

namespace BaksDev\Drom\Type\Event;

use BaksDev\Core\Type\UidType\UidType;

final class DromTokenEventType extends UidType
{
    public function getClassType(): string
    {
        return DromTokenEventUid::class;
    }

    public function getName(): string
    {
        return DromTokenEventUid::TYPE;
    }
}
