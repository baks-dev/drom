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

namespace BaksDev\Drom\Controller\Admin;

use BaksDev\Drom\Entity\DromToken;
use BaksDev\Drom\Entity\Event\DromTokenEvent;
use BaksDev\Drom\Type\Event\DromTokenEventUid;
use BaksDev\Drom\UseCase\Admin\NewEdit\DromTokenNewEditDTO;
use BaksDev\Drom\UseCase\Admin\NewEdit\DromTokenNewEditForm;
use BaksDev\Drom\UseCase\Admin\NewEdit\DromTokenNewEditHandler;
use BaksDev\Core\Controller\AbstractController;
use BaksDev\Core\Listeners\Event\Security\RoleSecurity;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[RoleSecurity('ROLE_DROM_TOKEN_EDIT')]
final class EditController extends AbstractController
{
    #[Route('/admin/drom/token/edit/{id}', name: 'admin.newedit.edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        #[MapEntity] DromTokenEvent $dromTokenEvent,
        DromTokenNewEditHandler $DromTokenNewEditHandler
    ): Response
    {
        $dromTokenNewEditDTO = new DromTokenNewEditDTO();

        /** Запрещаем редактировать чужой токен */
        if($this->isAdmin() === true || $dromTokenEvent->getProfile()->equals($this->getProfileUid()) === true)
        {
            $dromTokenEvent->getDto($dromTokenNewEditDTO);
        }

        if($request->getMethod() === 'GET')
        {
            $dromTokenNewEditDTO
                ->getKey()
                ->hiddenSecret();
        }

        $form = $this
            ->createForm(
                type: DromTokenNewEditForm::class,
                data: $dromTokenNewEditDTO,
                options: ['action' => $this->generateUrl(
                    'drom:admin.newedit.edit',
                    ['id' => $dromTokenNewEditDTO->getEvent() ?: new DromTokenEventUid()]
                )],
            )
            ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() && $form->has('drom_token'))
        {
            $this->refreshTokenForm($form);

            /** Запрещаем редактировать чужой токен */
            if(
                $this->isAdmin() === false &&
                $this->getProfileUid()?->equals($dromTokenNewEditDTO->getProfile()->getValue()) !== true
            )
            {
                $this->addFlash('breadcrumb.edit', 'danger.edit', 'drom.admin', '404');
                return $this->redirectToReferer();
            }

            $dromToken = $DromTokenNewEditHandler->handle($dromTokenNewEditDTO);

            if($dromToken instanceof DromToken)
            {
                $this->addFlash('breadcrumb.edit', 'success.edit', 'drom.admin');

                return $this->redirectToRoute('drom:admin.index');
            }

            $this->addFlash('breadcrumb.edit', 'danger.edit', 'drom.admin', $dromToken);

            return $this->redirectToReferer();
        }

        return $this->render(['form' => $form->createView()]);
    }
}
