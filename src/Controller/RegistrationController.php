<?php



namespace App\Controller;

use App\Security\LoginAuthenticator;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param EntityManagerInterface $entityManager
     * @param AbstractLoginFormAuthenticator $login
     * @param GuardAuthenticatorHandler $guard
     * @return Response
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder,
                             EntityManagerInterface $entityManager, LoginAuthenticator $login,
                             GuardAuthenticatorHandler $guard): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            /* @var UploadedFile $file */
            $file = $form['attachment']->getData();

            if ($file) {
                $filename = md5(uniqid()) . '.' . $file->guessClientExtension();

                $file->move($this->getParameter('profilepics_dir'), $filename);
                $user->setProfilePicture($filename);
            }
            else {
                $user ->setProfilePicture('blank.png');
            }

            $user->setPassword($passwordEncoder->encodePassword($user, $form->get('password')->getData()));

            $entityManager->persist($user);
            $entityManager->flush();

            return $guard->authenticateUserAndHandleSuccess($user,$request,$login,'main');
        }


        return $this->render('registration/index.html.twig', array('form' => $form->createView()));
    }
}
