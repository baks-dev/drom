<?php

declare(strict_types=1);

namespace BaksDev\Drom\Entity\Modify;

use BaksDev\Core\Type\Modify\ModifyAction;

interface DromTokenModifyInterface
{
    public function getAction(): ModifyAction;
}
