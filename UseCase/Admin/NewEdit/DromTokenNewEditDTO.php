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

use BaksDev\Drom\Entity\Event\DromTokenEventInterface;
use BaksDev\Drom\Type\Event\DromTokenEventUid;
use BaksDev\Drom\UseCase\Admin\NewEdit\Active\DromTokenActiveDTO;
use BaksDev\Drom\UseCase\Admin\NewEdit\Key\DromTokenKeyDTO;
use BaksDev\Drom\UseCase\Admin\NewEdit\Kit\DromTokenKitDTO;
use BaksDev\Drom\UseCase\Admin\NewEdit\Percent\DromTokenPercentDTO;
use BaksDev\Drom\UseCase\Admin\NewEdit\Pricelist\DromTokenPricelistDTO;
use BaksDev\Drom\UseCase\Admin\NewEdit\Profile\DromTokenProfileDTO;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/** @see DromTokenEvent */
final class DromTokenNewEditDTO implements DromTokenEventInterface
{
    /** Идентификатор события */
    #[Assert\Uuid]
    private ?DromTokenEventUid $id = null;

    /** ID настройки (профиль пользователя) */
    #[Assert\Valid]
    private DromTokenProfileDTO $profile;

    #[Assert\Valid]
    private DromTokenActiveDTO $active;

    #[Assert\Valid]
    private DromTokenPercentDTO $percent;

    #[Assert\Valid]
    private DromTokenKeyDTO $key;

    #[Assert\Valid]
    private DromTokenPricelistDTO $pricelist;


    /**
     * Настройка количества товаров в объявлении
     * @var ArrayCollection<int, DromTokenKitDTO> $kit
     */
    #[Assert\Valid]
    private ArrayCollection $kit;

    public function __construct()
    {
        $this->profile = new DromTokenProfileDTO();
        $this->active = new DromTokenActiveDTO;
        $this->percent = new DromTokenPercentDTO;
        $this->key = new DromTokenKeyDTO;
        $this->kit = new ArrayCollection();
        $this->pricelist = new DromTokenPricelistDTO();
    }

    public function setId(?DromTokenEventUid $id): void
    {
        $this->id = $id;
    }

    public function getEvent(): ?DromTokenEventUid
    {
        return $this->id;
    }

    public function getProfile(): DromTokenProfileDTO
    {
        return $this->profile;
    }

    public function setProfile(DromTokenProfileDTO $profile): self
    {
        $this->profile = $profile;
        return $this;
    }

    public function getActive(): DromTokenActiveDTO
    {
        return $this->active;
    }

    public function getPercent(): DromTokenPercentDTO
    {
        return $this->percent;
    }

    /**
     * @return ArrayCollection<int, DromTokenKitDTO>
     */
    public function getKit(): ArrayCollection
    {
        return $this->kit;
    }

    public function addKit(DromTokenKitDTO $kit): void
    {
        $this->kit->add($kit);
    }

    public function removeKit(DromTokenKitDTO $kit): void
    {
        $this->kit->removeElement($kit);
    }

    public function setActive(DromTokenActiveDTO $active): void
    {
        $this->active = $active;
    }

    public function setPercent(DromTokenPercentDTO $percent): void
    {
        $this->percent = $percent;
    }

    public function getKey(): DromTokenKeyDTO
    {
        return $this->key;
    }

    public function setKey(DromTokenKeyDTO $key): self
    {
        $this->key = $key;
        return $this;
    }

    public function getPricelist(): DromTokenPricelistDTO
    {
        return $this->pricelist;
    }

    public function setPricelist(DromTokenPricelistDTO $pricelist): self
    {
        $this->pricelist = $pricelist;
        return $this;
    }
}
