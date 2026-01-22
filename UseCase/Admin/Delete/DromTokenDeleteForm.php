<?php

declare(strict_types=1);

namespace BaksDev\Drom\UseCase\Admin\Delete;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class DromTokenDeleteForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'drom_token_delete',
            SubmitType::class,
            ['label' => 'Save', 'label_html' => true, 'attr' => ['class' => 'btn-primary']],
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DromTokenDeleteDTO::class,
            'method' => 'POST',
            'attr' => ['class' => 'w-100'],
        ]);
    }
}
