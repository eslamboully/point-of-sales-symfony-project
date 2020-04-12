<?php

namespace App\Form;

use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class ClientType extends AbstractType
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
      $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label'  => $this->translator->trans('name',[],'client'),
                'attr' => ['class' => 'form-control','placeholder' => $this->translator->trans('name',[],'client')],
                'required' => false,
                'constraints' => [new NotBlank()],
              ])
              ->add('phone', IntegerType::class, [
                  'label'  => $this->translator->trans('phone',[],'client'),
                  'attr' => ['class' => 'form-control','placeholder' => $this->translator->trans('phone',[],'client')],
                  'required' => false,
                  'constraints' => [new NotBlank()],
                ])
              ->add('address', TextareaType::class, [
                  'label'  => $this->translator->trans('address',[],'client'),
                  'attr' => ['class' => 'form-control','placeholder' => $this->translator->trans('address',[],'client')],
                  'required' => false,
                  'constraints' => [new NotBlank()],
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'create-client',
        ]);
    }

    public function getBlockPrefix()
    {
        return false;
    }
}
