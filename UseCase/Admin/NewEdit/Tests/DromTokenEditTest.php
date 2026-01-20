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

namespace BaksDev\Drom\UseCase\Admin\NewEdit\Tests;

use BaksDev\Drom\Entity\DromToken;
use BaksDev\Drom\Entity\Event\DromTokenEvent;
use BaksDev\Drom\Entity\Modify\DromTokenModify;
use BaksDev\Drom\UseCase\Admin\NewEdit\DromTokenNewEditDTO;
use BaksDev\Drom\UseCase\Admin\NewEdit\DromTokenNewEditHandler;
use BaksDev\Core\Type\Modify\Modify\ModifyActionUpdate;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\DependsOnClass;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[Group('drom')]
#[Group('drom-controller')]
#[Group('drom-usecase')]
final class DromTokenEditTest extends KernelTestCase
{
    #[DependsOnClass(DromTokenNewTest::class)]
    public function testEdit(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        /** @var EntityManagerInterface $EntityManager */
        $EntityManager = $container->get(EntityManagerInterface::class);

        /** Находим токен по тестовому идентификатору профиля */
        $token = $EntityManager
            ->getRepository(DromToken::class)
            ->find('019bd5b4-d6bd-72ec-9d4c-033104554a7b');

        self::assertNotNull($token);

        /** Находим активное событие */
        $activeEvent = $EntityManager
            ->getRepository(DromTokenEvent::class)
            ->find($token->getEvent());

        self::assertNotNull($activeEvent);

        $dromTokenNewEditDTO = new DromTokenNewEditDTO();

        $activeEvent->getDto($dromTokenNewEditDTO);

        /** @var DromTokenNewEditHandler $DromTokenNewEditHandler */
        $DromTokenNewEditHandler = $container->get(DromTokenNewEditHandler::class);
        $editDromToken = $DromTokenNewEditHandler->handle($dromTokenNewEditDTO);
        self::assertTrue($editDromToken instanceof DromToken);

        $modifier = $EntityManager
            ->getRepository(DromTokenModify::class)
            ->find($editDromToken->getEvent());

        self::assertTrue($modifier->equals(ModifyActionUpdate::ACTION));
    }
}
