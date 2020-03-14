<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Validator\Constraints\File;



class ProductType extends AbstractType
{
    private $categoryRepository;
    private $translator;
    public function __construct(CategoryRepository $categoryRepository,TranslatorInterface $translator)
    {
        $this->categoryRepository = $categoryRepository;
        $this->translator = $translator;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category_id',ChoiceType::class,[
              'label'  => $this->translator->trans('categories',[],'category'),
              'constraints' => [new NotBlank()],
              'required' => false,
                'choice_label' => function(Category $category) {
                    return $category->translate($this->translator->getLocale())->getName();
                },
                'choice_value' => function (?Category $entity) {
                    return $entity ? $entity->getId() : '';
                },
                'placeholder' => $this->translator->trans('choose_category',[],'category'),
              'choices' => $this->categoryRepository->findAll(),
              'attr' => [
                'class' => 'form-control'
              ]
            ])
            ->add('title_ar', TextType::class, [
                'label'  => $this->translator->trans('ar.title',[],'product'),
                'attr'   => ['class' => 'form-control','placeholder' => $this->translator->trans('ar.title',[],'product')],
                'mapped' => false,
                'required' => false,
                'constraints' => [new NotBlank()],

              ])
            ->add('title_en', TextType::class, [
                'label'  => $this->translator->trans('en.title',[],'product'),
                'attr' => ['class' => 'form-control','placeholder' => $this->translator->trans('en.title',[],'product')],
                'mapped' => false,
                'required' => false,
                'constraints' => [new NotBlank()],
              ])
              ->add('description_ar', TextareaType::class, [
                  'label'  => $this->translator->trans('ar.description',[],'product'),
                  'attr'   => ['class' => 'form-control ckeditor','placeholder' => $this->translator->trans('ar.description',[],'product')],
                  'mapped' => false,
                  'required' => false,
                  'constraints' => [new NotBlank()],

                ])
              ->add('description_en', TextareaType::class, [
                  'label'  => $this->translator->trans('en.description',[],'product'),
                  'attr' => ['class' => 'form-control ckeditor','placeholder' => $this->translator->trans('en.description',[],'product')],
                  'mapped' => false,
                  'required' => false,
                  'constraints' => [new NotBlank()],
                ])
              ->add('image',FileType::class,[
                  'label'  => $this->translator->trans('image',[],'product'),
                  'attr' => ['class' => 'form-control','placeholder' => $this->translator->trans('image',[],'product')],
                  'required' => false,
                  'mapped' => false,
                  'constraints' => [
                      new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpg',
                            'image/jpeg',
                            'image/png',
                        ],
                      ])
                  ]
              ])
              ->add('purchase_price',IntegerType::class,[
                'label'  => $this->translator->trans('purchase_price',[],'product'),
                'attr' => ['class' => 'form-control','placeholder' => $this->translator->trans('purchase_price',[],'product')],
                'required' => false,
                'constraints' => [new NotBlank()],
              ])
              ->add('sale_price',IntegerType::class,[
                'label'  => $this->translator->trans('sale_price',[],'product'),
                'attr' => ['class' => 'form-control','placeholder' => $this->translator->trans('sale_price',[],'product')],
                'required' => false,
                'constraints' => [new NotBlank()],
              ])
              ->add('store',IntegerType::class,[
                'label'  => $this->translator->trans('store',[],'product'),
                'attr' => ['class' => 'form-control','placeholder' => $this->translator->trans('store',[],'product')],
                'required' => false,
                'constraints' => [new NotBlank()],
              ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'create-product',
        ]);
    }

    public function getBlockPrefix()
    {
        return false;
    }
}
