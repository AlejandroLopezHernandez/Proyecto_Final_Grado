<?php

namespace App\Form;

use App\Entity\Reserva;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class ReservaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombreCliente', TextType::class, [
                'label' => 'Nombre del cliente',
                'attr' => ['class' => 'form-control']
            ])
            ->add('emailCliente', EmailType::class, [
                'label' => 'Email del cliente',
                'attr' => ['class' => 'form-control']
            ])
            ->add('telefonoCliente', TextType::class, [
                'label' => 'Teléfono del cliente',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('numeroComensales', IntegerType::class, [
                'label' => 'Número de comensales',
                'attr' => [
                    'class' => 'form-control',
                    'min' => 1,
                    'max' => 10
                ]
            ])
            ->add('fechaHoraReserva', DateTimeType::class, [
                'label' => 'Fecha y hora de la reserva',
                'widget' => 'single_text',
                'html5' => true,
                'attr' => [
                    'class' => 'form-control datetimepicker',
                    'min' => (new \DateTime())->format('Y-m-d\TH:i')
                ]
            ])
            ->add('infoAdicional', TextareaType::class, [
                'label' => 'Información adicional',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 3
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reserva::class,
        ]);
    }
}
