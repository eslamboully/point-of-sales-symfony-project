<?php
namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Product;
use App\Entity\Category;
use App\Form\ProductType;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


/**
 * @Route("/admin/products", name="admin.products.")
 */
class ProductController extends AbstractController
{
  private $locales = ['ar','en'];

    /**
     * @Route("/index", name="index")
     */
    public function index()
    {
        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();

        return $this->render('admin/products/index.html.twig',['products'=>$products]);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request)
    {
       // dd($request->request->all());

       $product = new Product();

      $form = $this->createForm(ProductType::class, $product);

      $form->handleRequest($request);
          if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['image']->getData();

              if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();


                try {
                    $file->move(
                        $this->getParameter('products_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                  throw $this->createNotFoundException('The product does not save error photo');
                }

                $product->setImage($newFilename);
              }

            $entityManager = $this->getDoctrine()->getManager();

            foreach ($this->locales as $locale) {
                $product->translate($locale)->setTitle($request->request->get('title_'.$locale));
                $product->translate($locale)->setDescription($request->request->get('description_'.$locale));
            }
            // dd($request->request->all());
            $category = $this->getDoctrine()->getRepository(Category::class)->findOneBy(['id'=>$request->request->get('category_id')]);

            $product->setCategoryId($category);
            $product->setPurchasePrice($request->request->get('purchase_price'));
            $product->setSalePrice($request->request->get('sale_price'));
            $product->setStore($request->request->get('store'));

            $entityManager->persist($product);

            $product->mergeNewTranslations();
            $entityManager->flush();
            return $this->redirectToRoute('admin.products.index');
      }
      return $this->render('admin/products/create.html.twig',[
          'form' => $form->createView(),
      ]);
    }

    /**
     * @Route("/{id}/edit", name="edit")
     */

    public function edit(Request $request,$id)
    {
      $entityManager = $this->getDoctrine()->getManager();

      $row = $entityManager->getRepository(Product::class)->find($id);

      $form = $this->createForm(ProductType::class, $row);

      $form->handleRequest($request);
          if ($form->isSubmitted() && $form->isValid()) {


              foreach ($this->locales as $locale) {
                  $row->translate($locale)->setTitle($request->request->get('title_'.$locale));
                  $row->translate($locale)->setDescription($request->request->get('description_'.$locale));
              }

              $category = $this->getDoctrine()->getRepository(Category::class)->findOneBy(['id'=>$request->request->get('category_id')]);

              $row->setCategoryId($category);
              $row->setPurchasePrice($request->request->get('purchase_price'));
              $row->setSalePrice($request->request->get('sale_price'));
              $row->setStore($request->request->get('store'));
              //
              $entityManager->persist($row);
              //
              $row->mergeNewTranslations();
              $entityManager->flush();


              return $this->redirectToRoute('admin.products.index');
          }

      return $this->render('admin/products/edit.html.twig',[
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
      $row = $entityManager->getRepository(Product::class)->find($id);

      $entityManager->remove($row);
      $entityManager->flush();

      $this->addFlash('success',$translator->trans('delete_success',[],'admin'));
      return $this->redirectToRoute('admin.products.index');
    }




}
