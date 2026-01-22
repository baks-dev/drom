<?php
/*
 *  Copyright 2026.  Baks.dev <admin@baks.dev>
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

namespace BaksDev\Drom\Repository\DromAuthorizationByProfile;

use BaksDev\Drom\Entity\DromToken;
use BaksDev\Drom\Entity\Active\DromTokenActive;
use BaksDev\Drom\Entity\Key\DromTokenKey;
use BaksDev\Drom\Entity\Percent\DromTokenPercent;
use BaksDev\Drom\Entity\Pricelist\DromTokenPricelist;
use BaksDev\Drom\Entity\Profile\DromTokenProfile;
use BaksDev\Drom\Type\Authorization\DromTokenAuthorization;
use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Users\Profile\UserProfile\Entity\Event\Info\UserProfileInfo;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use BaksDev\Users\Profile\UserProfile\Type\UserProfileStatus\Status\UserProfileStatusActive;
use BaksDev\Users\Profile\UserProfile\Type\UserProfileStatus\UserProfileStatus;

final class DromAuthorizationByProfileRepository implements DromAuthorizationByProfileInterface
{
    public function __construct(
        private readonly DBALQueryBuilder $DBALQueryBuilder,
    ) {}

    public function getAuthorization(UserProfileUid $profile): DromTokenAuthorization|false
    {
        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        $dbal
            ->from(DromTokenProfile::class, 'drom_token_profile')
            ->where('drom_token_profile.value = :profile')
            ->setParameter('profile', $profile, UserProfileUid::TYPE);

        $dbal
            ->join(
                'drom_token_profile',
                DromToken::class,
                'drom_token',
                'drom_token_profile.event = drom_token.event'
            );

        $dbal->join(
            'drom_token',
            DromTokenActive::class,
            'drom_token_active',
            '
            drom_token_active.event = drom_token.event AND 
            drom_token_active.value IS TRUE',
        );

        $dbal->join(
            'drom_token',
            DromTokenPercent::class,
            'drom_token_percent',
            'drom_token_percent.event = drom_token.event',
        );

        $dbal
            ->join(
                'drom_token',
                DromTokenPricelist::class,
                'drom_token_pricelist',
                'drom_token_pricelist.event = drom_token.event',
            );

        $dbal
            ->join(
                'drom_token',
                DromTokenKey::class,
                'drom_token_key',
                'drom_token_key.event = drom_token.event',
            );

        $dbal
            ->join(
                'drom_token',
                UserProfileInfo::class,
                'info',
                'info.profile = drom_token_profile.value AND info.status = :status',
            )
            ->setParameter(
                'status',
                UserProfileStatusActive::class,
                UserProfileStatus::TYPE,
            );

        $dbal
            ->select('drom_token.id AS profile')
            ->addSelect('drom_token_pricelist AS pricelist')
            ->addSelect('drom_token_key AS key')
            ->addSelect('drom_token_percent.value AS percent');

        /* Кешируем результат ORM */
        return $dbal
            ->enableCache('drom')
            ->fetchHydrate(DromTokenAuthorization::class);
    }
}
