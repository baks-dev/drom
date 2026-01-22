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

namespace BaksDev\Drom\Repository\AllUserProfilesByActiveToken;

use BaksDev\Drom\Entity\DromToken;
use BaksDev\Drom\Entity\Active\DromTokenActive;
use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Drom\Entity\Profile\DromTokenProfile;
use BaksDev\Users\Profile\UserProfile\Entity\Event\Info\UserProfileInfo;
use BaksDev\Users\Profile\UserProfile\Entity\Event\Personal\UserProfilePersonal;
use BaksDev\Users\Profile\UserProfile\Entity\UserProfile;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use BaksDev\Users\Profile\UserProfile\Type\UserProfileStatus\Status\UserProfileStatusActive;
use BaksDev\Users\Profile\UserProfile\Type\UserProfileStatus\UserProfileStatus;
use Generator;

final readonly class AllUserProfilesByActiveTokenRepository implements AllUserProfilesByActiveTokenInterface
{
    public function __construct(private DBALQueryBuilder $DBALQueryBuilder) {}

    /**
     * Возвращает идентификаторы всех АКТИВНЫХ Drom профилей
     * @return Generator<UserProfileUid>
     */
    public function findProfilesByActiveToken(): Generator
    {
        $dbal = $this->DBALQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();

        $dbal->from(DromToken::class, 'drom_token');

        $dbal->join(
            'drom_token',
            DromTokenProfile::class,
            'drom_token_profile',
            'drom_token_profile.event = drom_token.event'
        );

        $dbal
            ->join(
                'drom_token',
                DromTokenActive::class,
                'drom_token_active',
                'drom_token_active.event = drom_token.event AND drom_token_active.value IS TRUE');


        /** Информация о профиле */
        $dbal
            ->leftJoin(
                'drom_token',
                UserProfile::class,
                'users_profile',
                'users_profile.id = drom_token_profile.value'
            );

        $dbal
            ->join(
                'drom_token',
                UserProfileInfo::class,
                'users_profile_info',
                'users_profile_info.profile = drom_token_profile.value AND users_profile_info.status = :status',
            )
            ->setParameter(
                'status',
                UserProfileStatusActive::class,
                UserProfileStatus::TYPE
            );

        $dbal->leftJoin(
            'users_profile',
            UserProfilePersonal::class,
            'personal',
            'personal.event = users_profile.event',
        );


        /** Параметры конструктора объекта гидрации */
        $dbal->select('drom_token_profile.value as value');
        $dbal->addSelect('personal.username AS attr');

        return $dbal
            ->enableCache('drom', '1 minutes')
            ->fetchAllHydrate(UserProfileUid::class);
    }
}
