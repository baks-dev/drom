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

namespace BaksDev\Drom\Repository\AllDromToken\Tests;

use BaksDev\Drom\Products\UseCase\NewEdit\Images\Tests\DromProductImagesNewTest;
use BaksDev\Drom\Products\UseCase\NewEdit\Tests\DromProductNewTest;
use BaksDev\Drom\Repository\AllDromToken\AllDromTokenInterface;
use BaksDev\Drom\Repository\AllDromToken\AllDromTokenRepository;
use BaksDev\Drom\Repository\AllDromToken\DromTokensResult;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use PHPUnit\Framework\Attributes\DependsOnClass;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[Group('drom')]
#[Group('drom-repository')]
final class AllDromTokenInterfaceRepositoryTest extends KernelTestCase
{
    public function testRepository(): void
    {
        /** @var AllDromTokenRepository $AllDromTokenRepository */
        $AllDromTokenRepository = self::getContainer()->get(AllDromTokenInterface::class);

        $profileUid = $_SERVER['TEST_PROFILE'] ?? UserProfileUid::TEST;

        $result = $AllDromTokenRepository
            ->profile($profileUid)
            ->findPaginator()
            ->getData();

        foreach ($result as $item) {
            self::assertInstanceOf(DromTokensResult::class, $item);

            // Вызываем все геттеры
            $reflectionClass = new ReflectionClass(DromTokensResult::class);
            $methods = $reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);

            foreach($methods as $method)
            {
                // Методы без аргументов
                if($method->getNumberOfParameters() === 0)
                {
                    // Вызываем метод
                    $data = $method->invoke($item);
                    //dump($data);
                }
            }

            return;
        }
    }
}