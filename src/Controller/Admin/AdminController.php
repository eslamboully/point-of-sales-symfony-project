<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Admin;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Validator\Validation;
use Symfony\Contracts\Translation\TranslatorInterface;


/**
 * @Route("/admin/admins", name="admin.admins.")
 */
class AdminController extends AbstractController
{
  use Validation;
    /**
     * @Route("/index", name="index")
    */
    public function index()
    {
        $admins = $this->getDoctrine()->getRepository(Admin::class)->findAll();

        return $this->render('admin/admins/index.html.twig',['admins'=>$admins]);
    }

    /**
     * @Route("/create", name="create")
    */
    public function create()
    {

        return $this->render('admin/admins/create.html.twig');
    }

    /**
     * @Route("/store", name="store", methods={"post"})
    */
    public function store(Request $request,UserPasswordEncoderInterface $encoder,ValidatorInterface $validator,TranslatorInterface $translator)
    {

        $errors = $this->validate($request->request->all(),[
            'name'             => 'required',
            'email'            => 'required|email|unique:admin',
            'password'         => 'required',
            'confirm_password' => 'required|same:password'
        ]);
        //dd($errors);
        if(!empty($errors))
        {
          $this->addFlashArray('errors',$errors);
          // return $this->redirectToRoute('admin.admins.create',['errors' => $errors]);
          return $this->redirectToRoute('admin.admins.create');
        }

        //////////// end validation ////////////////

        $entityManager = $this->getDoctrine()->getManager();
        //
        $admin = new Admin();
        $admin->setName($request->request->get('name'));
        $admin->setEmail($request->request->get('email'));




        $encoded = $encoder->encodePassword($admin, $request->request->get('password'));

        $admin->setPassword($encoded);

        $entityManager->persist($admin);
        $entityManager->flush();

        $this->addFlash('success',$translator->trans('add_success',[],'admin'));

         return $this->redirectToRoute('admin.admins.index');
    }


    /**
     * @Route("/{id}/edit", name="edit")
    */
    public function edit($id)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $row = $entityManager->getRepository(Admin::class)->find($id);

        return $this->render('admin/admins/edit.html.twig',[
          'row' => $row
        ]);
    }

    /**
     * @Route("/{id}/update", name="update")
    */
    public function update(Request $request,UserPasswordEncoderInterface $encoder,ValidatorInterface $validator,TranslatorInterface $translator,$id)
    {
      $errors = $this->validate($request->request->all(),[
          'name'             => 'required',
          'email'            => 'required|email',
      ]);
      //dd($errors);
      if(!empty($errors))
      {
        $this->addFlashArray('errors',$errors);
        // return $this->redirectToRoute('admin.admins.create',['errors' => $errors]);
        return $this->redirectToRoute('admin.admins.edit',['id'=>$id]);
      }

      //////////// end validation ////////////////

      $entityManager = $this->getDoctrine()->getManager();
      //
      $row = $entityManager->getRepository(Admin::class)->find($id);
      $row->setName($request->request->get('name'));
      $row->setEmail($request->request->get('email'));

      if($request->request->get('password') !== null && $request->request->get('password') != ''){
        $encoded = $encoder->encodePassword($row, $request->request->get('password'));
        $row->setPassword($encoded);
      }

      $entityManager->flush();

      $this->addFlash('success',$translator->trans('edit_success',[],'admin'));

       return $this->redirectToRoute('admin.admins.index');
    }


    /**
     * @Route("/{id}/delete", name="delete")
    */
    public function destroy(TranslatorInterface $translator,$id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $row = $entityManager->getRepository(Admin::class)->find($id);

        $entityManager->remove($row);
        $entityManager->flush();

        $this->addFlash('success',$translator->trans('delete_success',[],'admin'));
        return $this->redirectToRoute('admin.admins.index');
    }
}
