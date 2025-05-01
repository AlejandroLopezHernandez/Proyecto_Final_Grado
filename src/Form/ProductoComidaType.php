<?php

namespace App\Form;

use App\Entity\Comida;
use App\Entity\Producto;
use App\Entity\ProductoComida;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductoComidaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Comida', EntityType::class, [
                'class' => Comida::class,
                'choice_label' => 'id',
            ])
            ->add('Producto', EntityType::class, [
                'class' => Producto::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductoComida::class,
        ]);
    }
}
