<?php
namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Category;
use App\Entity\Client;
use App\Entity\Order;
use App\Entity\Product;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/admin/orders", name="admin.orders.")
 */
class OrderController extends AbstractController
{
    /**
     * @Route("/index", name="index")
     */
    public function index()
    {
        $orders = $this->getDoctrine()->getRepository(Order::class)->findAll();

        return $this->render('admin/orders/index.html.twig',['orders'=>$orders]);
    }

    /**
     * @Route("/{id}/create", name="create")
     */
    public function create(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $categories = $entityManager->getRepository(Category::class)->findAll();

        return $this->render('admin/orders/create.html.twig',[
            'categories' => $categories
        ]);
    }
    /**
     * @Route("/show", name="show")
     */

     public function show(Request $request)
     {
        dd($request);
     }
    /**
     * @Route("/{id}/store", name="store")
     */
    public function store(Request $request,$id)
    {

        $entityManager = $this->getDoctrine()->getManager();
        $order = new Order();
        $quantity = [];
        $client = $entityManager->getRepository(Client::class)->findOneBy(['id' => $id]);
        if ($client !== null && $request->request->get('quantity')[0] !== 0) {

            // get total price
            $total_price = 0;
            for ($i=0; $i < count($request->request->get('ids')); $i++) {
                for ($i=0; $i < count($request->request->get('quantity')); $i++) {
                    $product = $entityManager->getRepository(Product::class)
                      ->findOneBy(['id'=>$request->request->get('ids')[$i]]);

                      $total_price += $product->getSalePrice() * $request->request->get('quantity')[$i];

                      // insert into many to many relation order_product

                      $order->addProduct($product);
                      $quantity[$product->getId()] = $request->request->get('quantity')[$i];

                      // update the quantity in product
                      $product->setStore($product->getStore() - $request->request->get('quantity')[$i]);
                }
            }

          $order->setQuantity(json_encode($quantity));
          $order->setClientId($client);
          $order->setTotalPrice($total_price);
          $order->setCreatedAt(new \DateTime());

          $entityManager->persist($order);
          $entityManager->flush();
        }else{
          return $this->redirectToRoute('admin.orders.create',['id'=>$id]);
        }

        return $this->redirectToRoute('admin.orders.index');
    }

    /**
     * @Route("/{id}/delete", name="delete")
     */
    public function destroy(TranslatorInterface $translator,$id)
    {
      $entityManager = $this->getDoctrine()->getManager();
      $order = $entityManager->getRepository(Order::class)->find($id);
      $productIdWithQuantity = json_decode($order->getQuantity(),true);

      foreach ($productIdWithQuantity as $id => $quantity) {
          $product = $entityManager->getRepository(Product::class)
            ->findOneBy(['id'=>$id]);

          $product->setStore($product->getStore() + $quantity);
      }

      $entityManager->remove($order);
      $entityManager->flush();

      $this->addFlash('success',$translator->trans('delete_success',[],'admin'));
      return $this->redirectToRoute('admin.orders.index');
    }




}
