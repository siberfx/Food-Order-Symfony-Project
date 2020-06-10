<?php

namespace App\Controller\Admin;

use App\Entity\Restaurant;
use App\Form\RestaurantType;
use App\Repository\RestaurantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/restaurant")
 */
class RestaurantController extends AbstractController
{
    /**
     * @Route("/", name="admin_restaurant_index", methods={"GET"})
     */
    public function index(RestaurantRepository $restaurantRepository): Response
    {
        $restaurants=$restaurantRepository->getAllRestaurants();
        $slider = $restaurantRepository->findBy(['status'=>'True'],[],10);
        return $this->render('admin/restaurant/index.html.twig', [
            'restaurants' => $restaurants,
            'slider'=>$slider,
        ]);
    }

    /**
     * @Route("/new", name="admin_restaurant_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $restaurant = new Restaurant();
        $form = $this->createForm(RestaurantType::class, $restaurant);
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

            $entityManager->persist($restaurant);
            $entityManager->flush();

            return $this->redirectToRoute('admin_restaurant_index');
        }

        return $this->render('admin/restaurant/new.html.twig', [
            'restaurant' => $restaurant,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_restaurant_show", methods={"GET"})
     */
    public function show(Restaurant $restaurant,RestaurantRepository $restaurantRepository): Response
    {
        $slider = $restaurantRepository->findBy([],[],10);
        return $this->render('admin/restaurant/show.html.twig', [
            'restaurant' => $restaurant,
            'slider'=>$slider,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_restaurant_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Restaurant $restaurant): Response
    {
        $form = $this->createForm(RestaurantType::class, $restaurant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var file $file*/
            $file =$form['image']->getData();
            if($file){
                $fileName=$this->generateUniqueFileName().'.'. $file->guessExtension();
                //dosya adı+dosyanın uzantısı
                try{
                    $file->move(
                        $this->getParameter('images_directory'), //in servis.yaml
                        $fileName
                    );
                } catch (FileException $e){
                    //h
                }
                $restaurant->setImage($fileName);
            }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_restaurant_index');
        }

        return $this->render('admin/restaurant/edit.html.twig', [
            'restaurant' => $restaurant,
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
     * @Route("/{id}", name="admin_restaurant_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Restaurant $restaurant): Response
    {
        if ($this->isCsrfTokenValid('delete'.$restaurant->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($restaurant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_restaurant_index');
    }
}
