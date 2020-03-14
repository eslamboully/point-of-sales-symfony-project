<?php
namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Client;
use App\Form\ClientType;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/admin/clients", name="admin.clients.")
 */
class ClientController extends AbstractController
{
    /**
     * @Route("/index", name="index")
     */
    public function index()
    {
        $clients = $this->getDoctrine()->getRepository(Client::class)->findAll();

        return $this->render('admin/clients/index.html.twig',['clients'=>$clients]);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request)
    {
      // creates a task object and initializes some data for this example
      $client = new Client();

      $form = $this->createForm(ClientType::class, $client);

      $form->handleRequest($request);
          if ($form->isSubmitted() && $form->isValid()) {

              $entityManager = $this->getDoctrine()->getManager();


                  $client->setName($request->request->get('name'));
                  $client->setPhone($request->request->get('phone'));
                  $client->setAddress($request->request->get('address'));

              $entityManager->persist($client);

              $entityManager->flush();


              return $this->redirectToRoute('admin.clients.index');
          }
        return $this->render('admin/clients/create.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit")
     */
    public function edit(Request $request,$id)
    {
      $entityManager = $this->getDoctrine()->getManager();

      $row = $entityManager->getRepository(Client::class)->find($id);

      $form = $this->createForm(ClientType::class, $row);

      $form->handleRequest($request);
          if ($form->isSubmitted() && $form->isValid()) {

              $row->setName($request->request->get('name'));
              $row->setPhone($request->request->get('phone'));
              $row->setAddress($request->request->get('address'));

              $entityManager->persist($row);
              $entityManager->flush();


              return $this->redirectToRoute('admin.clients.index');
          }

      return $this->render('admin/clients/edit.html.twig',[
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
      $row = $entityManager->getRepository(Client::class)->find($id);

      $entityManager->remove($row);
      $entityManager->flush();

      $this->addFlash('success',$translator->trans('delete_success',[],'admin'));
      return $this->redirectToRoute('admin.clients.index');
    }




}
