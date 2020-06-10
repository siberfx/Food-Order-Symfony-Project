<?php

namespace App\Controller;

use App\Entity\Admin\Comment;
use App\Entity\Admin\Reservation;
use App\Entity\User;
use App\Form\Admin\CommentType;
use App\Form\Admin\ReservationType;
use App\Form\UserType;
use App\Repository\Admin\CommentRepository;
use App\Repository\Admin\FoodRepository;
use App\Repository\Admin\ReservationRepository;
use App\Repository\RestaurantRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(RestaurantRepository $restaurantRepository): Response
    {
        $slider = $restaurantRepository->findBy(['status'=>'True'],[],3);
        return $this->render('user/show.html.twig', [
            'slider'=>$slider,


        ]);
    }
    /**
     * @Route("/comments", name="user_comments", methods={"GET"})
     */
    public function comments(RestaurantRepository $restaurantRepository, CommentRepository $commentRepository): Response
    {
        $user=$this->getUser();
        $slider = $restaurantRepository->findBy(['status'=>'True'],[],3);
        $comments = $commentRepository->getAllCommentsUser($user->getId());
        return $this->render('user/comments.html.twig', [
            'slider'=>$slider,
            'comments'=>$comments,
        ]);
    }
    /**
     * @Route("/restaurants", name="user_restaurants", methods={"GET"})
     */
    public function restaurants(RestaurantRepository $restaurantRepository): Response
    {
        $slider = $restaurantRepository->findBy(['status'=>'True'],[],3);
        return $this->render('user/restaurants.html.twig', [
            'slider'=>$slider,

        ]);
    }

    /**
     * @Route("/reservations", name="user_reservations", methods={"GET"})
     */
    public function reservations(ReservationRepository $reservationRepository,RestaurantRepository $restaurantRepository): Response
    {
        $slider = $restaurantRepository->findBy(['status'=>'True'],[],3);
        $user = $this->getUser();
      //  $reservations = $reservationRepository->findBy(['userid'=>$user->getId()]);
        $reservations=$reservationRepository->getUserReservation($user->getId());

        return $this->render('user/reservations.html.twig', [
            'reservations'=>$reservations,
            'slider'=>$slider,

        ]);
    }



    /**
     * @Route("/reservation/{id}", name="user_reservation_show", methods={"GET"})
     */
    public function reservationshow($id,ReservationRepository $reservationRepository,RestaurantRepository $restaurantRepository): Response
    {
        $slider = $restaurantRepository->findBy(['status'=>'True'],[],3);
        $user = $this->getUser();
        //  $reservations = $reservationRepository->findBy(['userid'=>$user->getId()]);
        $reservation=$reservationRepository->getReservation($id);

        return $this->render('user/reservation_show.html.twig', [
            'reservation'=>$reservation,
            'slider'=>$slider,

        ]);
    }


    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
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
                $user->setImage($fileName);
            }
            //********file upload**>>>>>>>

            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request,$id,User $user,UserPasswordEncoderInterface $passwordEncoder,RestaurantRepository $restaurantRepository): Response
    {
        $slider = $restaurantRepository->findBy(['status'=>'True'],[],3);
        $user=$this->getUser();
        if($user->getId() != $id)
        {
            return $this->redirectToRoute('home');
        }
        $form = $this->createForm(UserType::class, $user);
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
                $user->setImage($fileName);
            }
            //********file upload**>>>>>>>

            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'slider' => $slider,
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
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }

    /**
     * @Route("/newcomment/{id}", name="user_new_comment", methods={"GET","POST"})
     */
    public function newcomment(Request $request,$id): Response
    {

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        $submittedToken=$request->request->get('token');

        if ($form->isSubmitted() ) {
            if($this->isCsrfTokenValid('comment',$submittedToken)) {
                $entityManager = $this->getDoctrine()->getManager();

                $comment->setStatus('New');
                $comment->setIp($_SERVER['REMOTE_ADDR']);
                $comment->setRestaurantid($id);
                $user=$this->getUser();
                $comment->setUserid($user->getId());

                $entityManager->persist($comment);
                $entityManager->flush();

                $this->addFlash('success','Your comment has been sent successfully ');

                return $this->redirectToRoute('restaurant_show', ['id' => $id]);
            }
            }

        return $this->redirectToRoute('restaurant_show', ['id' => $id]);
    }

    /**
     * @Route("/reservation/{rid}/{fid}", name="user_reservation_new", methods={"GET","POST"})
     */
    public function newreservation(Request $request,$rid,$fid,RestaurantRepository $restaurantRepository,FoodRepository $foodRepository): Response
    {
        $piece=$_REQUEST["piece"];
        $restaurant=$restaurantRepository->findOneBy(['id'=>$rid]);
        $food=$foodRepository->findOneBy(['id'=>$fid]);
        $data["total"]=$piece*$food->getPrice();
        $data["piece"]=$piece;



        $reservation = new Reservation();
        $slider = $restaurantRepository->findBy(['status'=>'True'],[],3);
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        $submittedToken=$request->request->get('token');

        if ($form->isSubmitted() ) {
            if($this->isCsrfTokenValid('form-reservation',$submittedToken)) {
                $entityManager = $this->getDoctrine()->getManager();

                $reservation->setStatus('New');
                $reservation->setIp($_SERVER['REMOTE_ADDR']);
                $reservation->setRestaurantid($rid);
                $reservation->setFoodid($fid);
                $user=$this->getUser();
                $reservation->setUserid($user->getId());
                $reservation->setPiece($piece);
                $reservation->setTotal($data["total"]);
                $reservation->setCreatedAt(new \DateTime());

                $entityManager->persist($reservation);
                $entityManager->flush();

                return $this->redirectToRoute('user_reservations');
            }
        }

        return $this->render('user/newreservation.html.twig', [
            'reservation' => $reservation,
            'restaurant' => $restaurant,
            'food' => $food,
            'slider'=>$slider,
            'data'=>$data,

            'form' => $form->createView(),

        ]);
    }

}
