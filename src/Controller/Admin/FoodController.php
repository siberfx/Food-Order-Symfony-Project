<?php

namespace App\Controller\Admin;

use App\Entity\Admin\Food;
use App\Form\Admin\FoodType;
use App\Repository\Admin\FoodRepository;
use App\Repository\RestaurantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\File;


/**
 * @Route("/admin/food")
 */
class FoodController extends AbstractController
{
    /**
     * @Route("/", name="admin_food_index", methods={"GET"})
     */
    public function index(FoodRepository $foodRepository): Response
    {
        return $this->render('admin/food/index.html.twig', [
            'foods' => $foodRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new/{id}", name="admin_food_new", methods={"GET","POST"})
     */
    public function new(Request $request,$id,RestaurantRepository $restaurantRepository,FoodRepository $foodRepository): Response
    {
        $foods=$foodRepository->findBy(['restaurantid'=>$id]);
        $restaurant=$restaurantRepository->findOneBy(['id'=>$id]);

        $food = new Food();
        $form = $this->createForm(FoodType::class, $food);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //** file upload*****>>>>>>

            /** @var file $file */
            $file = $form['image']->getData();
            if ($file) {
                $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();
                //rastgele bir dosya adı oluşturur g
                try {
                    $file->move(
                        $this->getParameter('images_directory'),
                        $fileName
                    );
                } catch (FileException $e) {
                    //h
                }
                $food->setImage($fileName);
            }
            //********file upload**>>>>>>>

            $entityManager = $this->getDoctrine()->getManager();
            $food->setRestaurantid($restaurant->getId());

            $entityManager->persist($food);
            $entityManager->flush();

            return $this->redirectToRoute('admin_food_new',['id' => $id]);
        }

        return $this->render('admin/food/new.html.twig', [
            'foods' => $foods,
            'restaurant' => $restaurant,
            'food' => $food,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        return md5(uniqid());
    }

    /**
     * @Route("/{id}", name="admin_food_show", methods={"GET"})
     */
    public function show(Food $food): Response
    {
        return $this->render('admin/food/show.html.twig', [
            'food' => $food,
        ]);
    }

    /**
     * @Route("/{id}/edit/{rid}", name="admin_food_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, $rid, Food $food): Response
    {
        $form = $this->createForm(FoodType::class, $food);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //** file upload*****>>>>>>

            /** @var file $file */
            $file = $form['image']->getData();
            if ($file) {
                $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();
                //rastgele bir dosya adı oluşturur g
                try {
                    $file->move(
                        $this->getParameter('images_directory'),
                        $fileName
                    );
                } catch (FileException $e) {
                    //h
                }
                $food->setImage($fileName);
            }
            //********file upload**>>>>>>>

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_food_new',['id' => $rid]);
        }

        return $this->render('admin/food/edit.html.twig', [
            'food' => $food,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/{rid}", name="admin_food_delete", methods={"DELETE"})
     */
    public function delete(Request $request,$rid, Food $food): Response
    {
        if ($this->isCsrfTokenValid('delete'.$food->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($food);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_food_new',['id' => $rid]);
    }
}
