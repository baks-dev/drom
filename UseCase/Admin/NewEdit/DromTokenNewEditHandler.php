<?php
/*
 *  Copyright 2025.  Baks.dev <admin@baks.dev>
 *  
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is furnished
 *  to do so, subject to the following conditions:
 *  
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *  
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 */

declare(strict_types=1);

namespace BaksDev\Drom\UseCase\Admin\NewEdit;

use BaksDev\Drom\Entity\DromToken;
use BaksDev\Drom\Entity\Event\DromTokenEvent;
use BaksDev\Drom\Messenger\DromTokenMessage;
use BaksDev\Core\Entity\AbstractHandler;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(public: true)]
final class DromTokenNewEditHandler extends AbstractHandler
{
    public function handle(DromTokenNewEditDTO $command): string|DromToken
    {
        $this
            ->setCommand($command)
            ->preEventPersistOrUpdate(new DromToken(), DromTokenEvent::class);

        /** Валидация всех объектов */
        if($this->validatorCollection->isInvalid())
        {
            return $this->validatorCollection->getErrorUniqid();
        }

        $this->flush();

        $this->messageDispatch
            ->addClearCacheOther('drom-board')
            ->addClearCacheOther('drom-products')
            ->dispatch(
                message: new DromTokenMessage($this->main->getId(), $this->main->getEvent(), $command->getEvent()),
                transport: 'drom',
            );

        return $this->main;
    }

}
