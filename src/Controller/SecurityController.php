<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Form\UserPasswordType;
use Symfony\Component\Mime\Email;
use App\Repository\UserRepository;
use App\Form\UserChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;




class SecurityController extends AbstractController
{
    private $limiterFactory;

    /**
     * Constructeur avec le rate limiter qui permet de limiter le nombre de tentatives de connexion
     *
     * @param RateLimiterFactory $loginAttemptLimiter
     */
    public function __construct(RateLimiterFactory $loginAttemptLimiter)
    {
        $this->limiterFactory = $loginAttemptLimiter;
    }

    /**
     * Connexion
     *
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    #[Route('/connexion', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(
        AuthenticationUtils $authenticationUtils,
    ): Response {

        /** @var User|null $user */
        $user = $this->getUser();
        if ($user && $user->getPasswordChangeRequired()) {
            return $this->redirectToRoute('app_first_password');
        }

        $limiter = $this->limiterFactory->create('login_attempt');

        if (false === $limiter->consume()->isAccepted()) {
            return $this->render('pages/security/login.html.twig', [
                'last_Username' => $authenticationUtils->getLastUsername(),
                'error' => 'Trop de tentatives de connexion, veuillez réessayer dans une minute.'
            ]);
        }

        if ($this->getUser()) {
            return $this->redirectToRoute('dashboard');
        }

        return $this->render('pages/security/login.html.twig', [
            'last_Username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError()
        ]);
    }

    /**
     * Deconnexion
     *
     * @return void
     */
    #[Route('/deconnexion', name: 'app_logout', methods: ['GET'])]
    public function logout(): void
    {
        // automatiquement géré par symfony
    }

    /**
     * Formulaire d'inscription
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/inscription', name: 'app_registration', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_CONSEIL')]
    public function registration(
        Request $request,
        EntityManagerInterface $manager,
        MailerInterface $mailer,
        VerifyEmailHelperInterface $verifyEmailHelper
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            if ($form->get('roleChoice')->getData() == 'ROLE_PROPRIETAIRE') {
                if ($form->get('roles')->getData() == ['ROLE_CONSEIL']) {
                    $user->setRoles(['ROLE_PROPRIETAIRE', 'ROLE_CONSEIL']);
                } else {
                    $user->setRoles(['ROLE_PROPRIETAIRE']);
                }
            } else {
                if ($form->get('roles')->getData() == ['ROLE_CONSEIL']) {
                    $this->addFlash('danger', 'Un locataire ne peut pas être membre du conseil.');
                } else {
                    $user->setRoles(['ROLE_LOCATAIRE']);
                }
            }

            $tempPassword = bin2hex(random_bytes(10));
            $user->setPlainPassword($tempPassword);
            $user->setPasswordChangeRequired(true);

            $manager->persist($user);
            $manager->flush();

            $signatureComponents = $verifyEmailHelper->generateSignature(
                'app_verify_email',
                $user->getId(),
                $user->getEmail(),
                ['id' => $user->getId()]
            );

            $confirmationUrl = $signatureComponents->getSignedUrl();

            $email = (new TemplatedEmail())
                ->from('no-reply@yourwebsite.com')
                ->to($user->getEmail())
                ->subject('Confirmation de votre compte')
                ->htmlTemplate('pages/emails/confirmation.html.twig')
                ->context([
                    'confirmationUrl' => $confirmationUrl,
                    'welcomeMessage' => 'Bienvenue sur l\'application de gestion de votre résidence. 
                    Votre compte a bien été créé. Veuillez cliquer sur ce lien pour entrer votre mot de passe 
                    et vous connecter :' . $confirmationUrl,
                ]);

            $mailer->send($email);

            $this->addFlash("success", "Le résident a bien été créé");
            return $this->redirectToRoute('app_registration');
        }

        return $this->render('pages/security/registration.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * Modifier le mot de passe
     *
     * @param Request $request
     * @param UserRepository $repository
     * @param VerifyEmailHelperInterface $verifyEmailHelper
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/verify/email', name: 'app_verify_email', methods: ['GET'])]
    public function verifyUserEmail(
        Request $request,
        UserRepository $repository,
        VerifyEmailHelperInterface $verifyEmailHelper,
        EntityManagerInterface $manager
    ): Response {
        $id = $request->query->get('id');
        $token = $request->query->get('token');
        
        try {
            $verifyEmailHelper->validateEmailConfirmation($request->getUri(), $id, $token);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('danger', $exception->getReason());
            return $this->redirectToRoute('app_login');
        }

        $user = $repository->find($id);
        
        if (null === $user) {
            $this->addFlash('danger', 'L\'utilisateur n\'existe pas.');
            return $this->redirectToRoute('app_login');
        }

        $user->setIsVerified(true);
        $manager->persist($user);
        $manager->flush();

        $this->addFlash('success', 'Votre email a été vérifié.');

        return $this->redirectToRoute('app_first_password', ['id' => $user->getId()]);
    }

    /**
     * Changer le mot de passe la première fois que l'utilisateur se connecte
     *
     * @param Request $request
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/changer-mot-de-passe/{id}', name: 'app_first_password', methods: ['GET', 'POST'])]
    public function editPassword(
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $manager,
        $id 
    ): Response {
        $id = $request->query->get('id');
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }
    
        $form = $this->createForm(UserChangePasswordType::class, $user);
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPasswordChangeRequired(false);
    
            $user->setPlainPassword($form->get('plainPassword')->getData());
            $user->setPasswordChangeRequired(false);
    
            $manager->persist($user);
            $manager->flush();
    
            $this->addFlash('success', 'Votre mot de passe a bien été modifié.');
    
            return $this->redirectToRoute('app_login');
        }
    
        return $this->render('pages/security/edit_password.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }
}
