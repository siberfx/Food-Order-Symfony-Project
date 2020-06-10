<?php

namespace App\Controller;
use App\Entity\Admin\Messages;
use App\Entity\Restaurant;
use App\Form\Admin\MessagesType;
use App\Repository\Admin\CommentRepository;
use App\Repository\Admin\FoodRepository;
use App\Repository\ImageRepository;
use App\Repository\RestaurantRepository;
use App\Repository\SettingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Bridge\Google\Smtp\GmailTransport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(SettingRepository $settingRepository,RestaurantRepository $restaurantRepository)
    {
        $setting=$settingRepository->findAll();
        $slider = $restaurantRepository->findBy(['status'=>'True'],['title'=>'ASC'],3);
        $restaurants = $restaurantRepository->findBy(['status'=>'True'],['title'=>'DESC'],3);
        $newrestaurants = $restaurantRepository->findBy(['status'=>'True'],['title'=>'ASC'],3);


        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'setting'=>$setting,
            'slider'=>$slider,
            'restaurants'=>$restaurants,
            'newrestaurants'=>$newrestaurants,

        ]);
    }

    /**
     * @Route("/restaurant/{id}", name="restaurant_show", methods={"GET"})
     */
    public function show(Restaurant $restaurant,$id,RestaurantRepository $restaurantRepository,ImageRepository $imageRepository,
                         CommentRepository $commentRepository,FoodRepository $foodRepository): Response
    {
        $slider = $restaurantRepository->findBy(['status'=>'True'],[],3);
        $images = $imageRepository->findBy(['restaurant'=>$id]);
        $comment = $commentRepository->findBy(['restaurantid'=>$id, 'status'=>'True']);

$foods=$foodRepository->findBy(['restaurantid'=>$id, 'status'=>'True']);

        return $this->render('home/restaurantshow.html.twig', [
            'restaurant' => $restaurant,
            'slider'=>$slider,
            'images'=>$images,
            'comment'=>$comment,
            'foods'=>$foods,
        ]);
    }
    /**
     * @Route("/about", name="home_about")
     */
    public function about(SettingRepository $settingRepository,RestaurantRepository $restaurantRepository): Response
    {   $setting=$settingRepository->findAll();
        $slider = $restaurantRepository->findBy([],['title'=>'DESC'],10);


        return $this->render('home/aboutus.html.twig', [
            'setting' =>$setting,
            'slider'=>$slider,

        ]);
    }

    /**
     * @Route("/contact", name="home_contact",  methods={"GET","POST"})
     */
    public function contact(SettingRepository $settingRepository,RestaurantRepository $restaurantRepository, Request $request): Response
    {
        $message = new Messages();
        $form = $this->createForm(MessagesType::class, $message);
        $form->handleRequest($request);
        $submittedToken=$request->request->get('token');
        $setting = $settingRepository->findAll();
        if ($form->isSubmitted()) {
            if($this->isCsrfTokenValid('form-message',$submittedToken)){
                $entityManager = $this->getDoctrine()->getManager();
                $message->setStatus('New');
                $message->setIp($_SERVER['REMOTE_ADDR']);
                $entityManager->persist($message);
                $entityManager->flush();
                $this->addFlash('success','Your message has been sent successfully ');

                ///*************************SEND EMAİL *****************///
                ///*************************You must also get the setting data by findALL func *****************>>>>>>>>>>>>>

                $email = (new Email())
                    ->from($setting[0]->getSmtpemail())
                    ->to($form['email']->getData())
                    ->subject('Just Eat Your Request')
                    //->text('Simple Text')
                    ->html("Dear ".$form['name']->getData() ."<br>
                                 <p>We will evaluate your requests and contact you as soon as possible</p>
                                  Thank You for your message. <br>
                                  Regards... <br>
                                  =====================================================
                                  <br>".$setting[0]->getCompany()."  <br>
                                  Address : ".$setting[0]->getAddress()."<br>
                                  Phone   : ".$setting[0]->getPhone()."<br>"
                    );
                $transport = new GmailTransport($setting[0]->getSmtpemail(), $setting[0]->getSmtppassword());
                $mailer = new Mailer($transport);
                $mailer->send($email);
                //*************************SEND EMAİL *****************//
                //*************************SEND EMAİL *****************//

                return $this->redirectToRoute('home_contact');
            }
        }



        $setting=$settingRepository->findAll();
        $slider = $restaurantRepository->findBy(['status'=>'True'],[],3);

        return $this->render('home/contact.html.twig', [
            'setting' =>$setting,
            'slider'=>$slider,
            'form' => $form->createView(),
        ]);
    }


}
