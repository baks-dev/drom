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

namespace BaksDev\Drom\Entity\Event;

use BaksDev\Core\Entity\EntityEvent;
use BaksDev\Drom\Entity\DromToken;
use BaksDev\Drom\Entity\Active\DromTokenActive;
use BaksDev\Drom\Entity\Kit\DromTokenKit;
use BaksDev\Drom\Entity\Percent\DromTokenPercent;
use BaksDev\Drom\Entity\Key\DromTokenKey;
use BaksDev\Drom\Entity\Modify\DromTokenModify;
use BaksDev\Drom\Entity\Pricelist\DromTokenPricelist;
use BaksDev\Drom\Entity\Profile\DromTokenProfile;
use BaksDev\Drom\Type\Event\DromTokenEventUid;
use BaksDev\Drom\Type\Id\DromTokenUid;
use BaksDev\Users\Profile\UserProfile\Type\Id\UserProfileUid;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Validator\Constraints as Assert;

/** @see DromTokenNewEditDTO */
#[ORM\Entity]
#[ORM\Table(name: 'drom_token_event')]
class DromTokenEvent extends EntityEvent
{
    /** Идентификатор События */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Id]
    #[ORM\Column(type: DromTokenEventUid::TYPE)]
    private DromTokenEventUid $id;

    /** ID профиля пользователя */
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[ORM\Column(type: DromTokenUid::TYPE)]
    private DromTokenUid $main;

    /** Идентификатор профиля владельца */
    #[ORM\OneToOne(targetEntity: DromTokenProfile::class, mappedBy: 'event', cascade: ['all'])]
    private ?DromTokenProfile $profile = null;

    /** Ключ авторизации */
    #[ORM\OneToOne(targetEntity: DromTokenKey::class, mappedBy: 'event', cascade: ['all'])]
    private ?DromTokenKey $key = null;

    /** Id прайс-листа */
    #[ORM\OneToOne(targetEntity: DromTokenPricelist::class, mappedBy: 'event', cascade: ['all'])]
    private ?DromTokenPricelist $pricelist = null;

    /** Настройка для администратора - вкл/выкл токен */
    #[ORM\OneToOne(targetEntity: DromTokenActive::class, mappedBy: 'event', cascade: ['all'])]
    private ?DromTokenActive $active = null;

    /** Торговая наценка площадки */
    #[ORM\OneToOne(targetEntity: DromTokenPercent::class, mappedBy: 'event', cascade: ['all'])]
    private ?DromTokenPercent $percent = null;

    #[ORM\OneToOne(targetEntity: DromTokenModify::class, mappedBy: 'event', cascade: ['all'])]
    private DromTokenModify $modify;

    /** Настройка количества товаров в объявлении */
    #[Assert\Valid]
    #[ORM\OneToMany(targetEntity: DromTokenKit::class, mappedBy: 'event', cascade: ['all'], fetch: 'EAGER')]
    private Collection $kit;

    public function __construct()
    {
        $this->id = new DromTokenEventUid();
        $this->modify = new DromTokenModify($this);
    }

    public function __clone()
    {
        $this->id = clone new DromTokenEventUid();
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }

    public function getId(): DromTokenEventUid
    {
        return $this->id;
    }

    public function getProfile(): UserProfileUid
    {
        return $this->profile?->getValue();
    }

    public function setMain(DromToken|DromTokenUid $main): self
    {
        $this->main = $main instanceof DromToken ? $main->getId() : $main;

        return $this;
    }

    public function getDto($dto): mixed
    {
        if($dto instanceof DromTokenEventInterface)
        {
            return parent::getDto($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }

    public function setEntity($dto): mixed
    {
        if($dto instanceof DromTokenEventInterface || $dto instanceof self)
        {
            return parent::setEntity($dto);
        }

        throw new InvalidArgumentException(sprintf('Class %s interface error', $dto::class));
    }
}
