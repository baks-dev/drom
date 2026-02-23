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

use BaksDev\Core\Type\Modify\Modify\ModifyActionNew;
use BaksDev\Drom\Entity\DromToken;
use BaksDev\Drom\Entity\Event\DromTokenEvent;
use BaksDev\Drom\Type\Id\DromTokenUid;
use BaksDev\Drom\UseCase\Admin\NewEdit\Active\DromTokenActiveDTO;
use BaksDev\Drom\UseCase\Admin\NewEdit\DromTokenNewEditDTO;
use BaksDev\Drom\UseCase\Admin\NewEdit\DromTokenNewEditHandler;
use BaksDev\Drom\UseCase\Admin\NewEdit\Key\DromTokenKeyDTO;
use BaksDev\Drom\UseCase\Admin\NewEdit\Percent\DromTokenPercentDTO;
use BaksDev\Drom\UseCase\Admin\NewEdit\Pricelist\DromTokenPricelistDTO;
use BaksDev\Drom\UseCase\Admin\NewEdit\Profile\DromTokenProfileDTO;
use BaksDev\Products\Product\UseCase\Admin\NewEdit\Tests\ProductsProductNewAdminUseCaseTest;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\DependencyInjection\Attribute\When;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

#[When(env: 'test')]
#[Group('drom')]
#[Group('drom-controller')]
#[Group('drom-repository')]
#[Group('drom-usecase')]
final class DromTokenNewTest extends KernelTestCase
{
    public static function setUpBeforeClass(): void
    {
        // Бросаем событие консольной комманды
        $dispatcher = self::getContainer()->get(EventDispatcherInterface::class);
        $event = new ConsoleCommandEvent(new Command(), new StringInput(''), new NullOutput());
        $dispatcher->dispatch($event, 'console.command');

        /** @var EntityManagerInterface $EntityManager */
        $EntityManager = self::getContainer()->get(EntityManagerInterface::class);

        $dromToken = $EntityManager
            ->getRepository(DromToken::class)
            ->find(DromTokenUid::TEST);

        if($dromToken)
        {
            $EntityManager->remove($dromToken);
        }

        $dromTokenEvent = $EntityManager
            ->getRepository(DromTokenEvent::class)
            ->findBy(['main' => DromTokenUid::TEST]);

        foreach($dromTokenEvent as $event)
        {
            $EntityManager->remove($event);
        }

        $EntityManager->flush();
        $EntityManager->clear();

        /** Создаем тестовый продукт */
        ProductsProductNewAdminUseCaseTest::setUpBeforeClass();
        new ProductsProductNewAdminUseCaseTest('')->testUseCase();
    }

    public function testNew(): void
    {
        $dromTokenNewEditDTO = new DromTokenNewEditDTO();

        // DromTokenProfileDTO
        $dromTokenProfileDTO = new DromTokenProfileDTO();
        $dromTokenProfileDTO->setValue(new UserProfileUid(UserProfileUid::TEST));
        self::assertTrue($dromTokenProfileDTO->getValue()->equals(UserProfileUid::TEST));

        $dromTokenNewEditDTO->setProfile($dromTokenProfileDTO);

        // DromTokenActiveDTO
        $dromTokenActiveDTO = new DromTokenActiveDTO();
        $dromTokenActiveDTO->setValue(true);
        self::assertTrue($dromTokenActiveDTO->getValue());

        $dromTokenNewEditDTO->setActive($dromTokenActiveDTO);

        // DromTokenKeyDTO
        $dromTokenKeyDTO = new DromTokenKeyDTO();
        $dromTokenKeyDTO->setValue('DromTokenKeyDTO');
        self::assertSame('DromTokenKeyDTO', $dromTokenKeyDTO->getValue());

        $dromTokenNewEditDTO->setKey($dromTokenKeyDTO);

        // DromTokenPercentDTO
        $dromTokenPercentDTO = new DromTokenPercentDTO();
        $dromTokenPercentDTO->setValue('DromTokenPercentDTO');
        self::assertSame('DromTokenPercentDTO', $dromTokenPercentDTO->getValue());

        $dromTokenNewEditDTO->setPercent($dromTokenPercentDTO);


        // DromTokenPercentDTO
        $dromTokenPricelistDTO = new DromTokenPricelistDTO();
        $dromTokenPricelistDTO->setValue('DromTokenPricelistDTO');
        self::assertSame('DromTokenPricelistDTO', $dromTokenPricelistDTO->getValue());

        $dromTokenNewEditDTO->setPricelist($dromTokenPricelistDTO);


        /** @var DromTokenNewEditHandler $DromTokenNewEditHandler */
        $DromTokenNewEditHandler = self::getContainer()->get(DromTokenNewEditHandler::class);
        $newDromToken = $DromTokenNewEditHandler->handle($dromTokenNewEditDTO);
        self::assertInstanceOf(DromToken::class, $newDromToken);

        return;

        //        /** @var EntityManagerInterface $EntityManager */
        //        $EntityManager = self::getContainer()->get(EntityManagerInterface::class);
        //
        //        /** Проверка соответствия модификатора */
        //        $modifier = $EntityManager
        //            ->getRepository(DromTokenModify::class)
        //            ->find($newDromToken->getEvent());
        //
        //        self::assertTrue($modifier->equals(ModifyActionNew::ACTION));
    }
}
