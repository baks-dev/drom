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

namespace BaksDev\Drom\UseCase\Admin\Delete\Tests;

use BaksDev\Drom\Board\UseCase\Delete\Tests\DromBoardMapperDeleteTest;
use BaksDev\Drom\Entity\DromToken;
use BaksDev\Drom\Entity\Event\DromTokenEvent;
use BaksDev\Drom\UseCase\Admin\Delete\DromTokenDeleteDTO;
use BaksDev\Drom\UseCase\Admin\Delete\DromTokenDeleteHandler;
use BaksDev\Drom\UseCase\Admin\NewEdit\Tests\DromTokenNewTest;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\DependsOnClass;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[Group('drom')]
#[Group('drom-usecase')]
final class DromTokenDeleteTest extends KernelTestCase
{
    #[DependsOnClass(DromBoardMapperDeleteTest::class)]
    public function testDelete(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        /** @var EntityManagerInterface $EntityManager */
        $EntityManager = $container->get(EntityManagerInterface::class);

        /** Находим токен по идентификатору профиля */
        $token = $EntityManager
            ->getRepository(DromToken::class)
            ->find('019bd5b4-d6bd-72ec-9d4c-033104554a7b');

        self::assertNotNull($token);

        /** Находим активное событие */
        $activeEvent = $EntityManager
            ->getRepository(DromTokenEvent::class)
            ->find($token->getEvent());

        self::assertNotNull($activeEvent);

        $deleteDTO = new DromTokenDeleteDTO();

        $activeEvent->getDto($deleteDTO);

        /** @var DromTokenDeleteHandler $DromTokenDeleteHandler */
        $DromTokenDeleteHandler = $container->get(DromTokenDeleteHandler::class);
        $deleteDromToken = $DromTokenDeleteHandler->handle($deleteDTO);
        self::assertTrue($deleteDromToken instanceof DromToken);

        $dromToken = $EntityManager
            ->getRepository(DromToken::class)
            ->find($deleteDromToken->getId());

        self::assertNull($dromToken);
    }

    public static function tearDownAfterClass(): void
    {
        DromTokenNewTest::setUpBeforeClass();
    }
}
