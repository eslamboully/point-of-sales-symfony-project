<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\NotBlank;


class CategoryType extends AbstractType
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
      $this->translator = $translator;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('name_ar', TextType::class, [
            'label'  => $this->translator->trans('ar.name',[],'category'),
            'attr'   => ['class' => 'form-control','placeholder' => $this->translator->trans('ar.name',[],'category')],
            'mapped' => false,
            'required' => false,
            'constraints' => [new NotBlank()],

          ])
        ->add('name_en', TextType::class, [
            'label'  => $this->translator->trans('en.name',[],'category'),
            'attr' => ['class' => 'form-control','placeholder' => $this->translator->trans('en.name',[],'category')],
            'mapped' => false,
            'required' => false,
            'constraints' => [new NotBlank()],
          ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'create-category',
        ]);
    }

    public function getBlockPrefix()
    {
        return false;
    }
}
