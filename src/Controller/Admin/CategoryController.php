<?php
namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Category;
use App\Form\CategoryType;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/admin/categories", name="admin.categories.")
 */
class CategoryController extends AbstractController
{
  private $locales = ['ar','en'];

    /**
     * @Route("/index", name="index")
     */
    public function index()
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        return $this->render('admin/categories/index.html.twig',['categories'=>$categories]);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request)
    {
      // creates a task object and initializes some data for this example
      $category = new Category();

      $form = $this->createForm(CategoryType::class, $category);

      $form->handleRequest($request);
          if ($form->isSubmitted() && $form->isValid()) {

              $entityManager = $this->getDoctrine()->getManager();

              foreach ($this->locales as $locale) {
                  $category->translate($locale)->setName($request->request->get('name_'.$locale));
              }

              $entityManager->persist($category);

              $category->mergeNewTranslations();
              $entityManager->flush();


              return $this->redirectToRoute('admin.categories.index');
          }
        return $this->render('admin/categories/create.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit")
     */
    public function edit(Request $request,$id)
    {
      $entityManager = $this->getDoctrine()->getManager();

      $row = $entityManager->getRepository(Category::class)->find($id);

      $form = $this->createForm(CategoryType::class, $row);

      $form->handleRequest($request);
          if ($form->isSubmitted() && $form->isValid()) {


              foreach ($this->locales as $locale) {
                  $row->translate($locale)->setName($request->request->get('name_'.$locale));
              }
              //
              $entityManager->persist($row);
              //
              $row->mergeNewTranslations();
              $entityManager->flush();


              return $this->redirectToRoute('admin.categories.index');
          }

      return $this->render('admin/categories/edit.html.twig',[
        'row' => $row,
        'form' => $form->createView(),
      ]);
    }

    /**
     * @Route("/{id}/delete", name="delete")
     */
    public function destroy(TranslatorInterface $translator,$id)
    {
      $entityManager = $this->getDoctrine()->getManager();
      $row = $entityManager->getRepository(Category::class)->find($id);

      $entityManager->remove($row);
      $entityManager->flush();

      $this->addFlash('success',$translator->trans('delete_success',[],'admin'));
      return $this->redirectToRoute('admin.categories.index');
    }




}
