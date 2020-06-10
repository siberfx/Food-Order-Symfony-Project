<?php

namespace App\Controller;

use App\Entity\Restaurant;
use App\Form\Restaurant1Type;
use App\Repository\RestaurantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user/restaurant")
 */
class RestaurantController extends AbstractController
{
    /**
     * @Route("/", name="user_restaurant_index", methods={"GET"})
     */
    public function index(RestaurantRepository $restaurantRepository): Response
    {   $user=$this->getUser();
        $slider = $restaurantRepository->findBy(['status'=>'True'],[],3);
        return $this->render('restaurant/index.html.twig', [
            'restaurants' => $restaurantRepository->findBy(['userid'=>$user->getId()]),
            'slider'=>$slider,
        ]);
    }

    /**
     * @Route("/new", name="user_restaurant_new", methods={"GET","POST"})
     */
    public function new(Request $request,RestaurantRepository $restaurantRepository): Response
    {
        $restaurant = new Restaurant();
        $form = $this->createForm(Restaurant1Type::class, $restaurant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            //********file upload**>>>>>>>
            /** @var file $file*/
            $file =$form['image']->getData();
            if($file){
                $fileName=$this->generateUniqueFileName() .'.'. $file->guessExtension();
                //rastgele bir dosya adı oluşturur g
                try{
                    $file->move(
                        $this->getParameter('images_directory'),
                        $fileName
                    );
                } catch (FileException $e){
                    //h
                }
                $restaurant->setImage($fileName);
            }
            //********file upload**>>>>>>>

            $user=$this->getUser();
            $restaurant->setUserid($user->getId());
            $restaurant->setStatus("New");
            $entityManager->persist($restaurant);
            $entityManager->flush();

            return $this->redirectToRoute('user_restaurant_index');
        }
        $slider = $restaurantRepository->findBy(['status'=>'True'],[],3);
        return $this->render('restaurant/new.html.twig', [
            'restaurant' => $restaurant,
            'form' => $form->createView(),
            'slider'=>$slider,
        ]);
    }

    /**
     * @Route("/{id}", name="user_restaurant_show", methods={"GET"})
     */
    public function show(Restaurant $restaurant,RestaurantRepository $restaurantRepository): Response
    {
        $slider = $restaurantRepository->findBy(['status'=>'True'],[],3);
        return $this->render('restaurant/show.html.twig', [
            'restaurant' => $restaurant,
            'slider'=>$slider,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_restaurant_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Restaurant $restaurant,RestaurantRepository $restaurantRepository): Response
    {
        $form = $this->createForm(Restaurant1Type::class, $restaurant);

        $slider = $restaurantRepository->findBy(['status'=>'True'],[],3);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //********file upload**>>>>>>>
            /** @var file $file*/
            $file =$form['image']->getData();
            if($file){
                $fileName=$this->generateUniqueFileName() .'.'. $file->guessExtension();
                //rastgele bir dosya adı oluşturur g
                try{
                    $file->move(
                        $this->getParameter('images_directory'),
                        $fileName
                    );
                } catch (FileException $e){
                    //h
                }
                $restaurant->setImage($fileName);
            }
            //********file upload**>>>>>>>
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_restaurant_index');
        }

        return $this->render('restaurant/edit.html.twig', [
            'restaurant' => $restaurant,
            'slider'=>$slider,
            'form' => $form->createView(),
        ]);
    }

    /**
     * *@return string
     */
    private function generateUniqueFileName(){
        //benzersiz dosya sonra bunu new ve editte çağırdık dosya adı oluşturmak için
        return md5(uniqid());
    }

    /**
     * @Route("/{id}", name="user_restaurant_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Restaurant $restaurant): Response
    {
        if ($this->isCsrfTokenValid('delete'.$restaurant->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($restaurant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_restaurant_index');
    }
}
