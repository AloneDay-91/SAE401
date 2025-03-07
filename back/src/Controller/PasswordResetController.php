<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\PasswordResetToken;
use App\Repository\UserRepository;
use App\Repository\PasswordResetTokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mime\Address;

#[Route('/api')]
class PasswordResetController extends AbstractController
{
    #[Route('/password-reset', methods: ['POST'])]
    public function requestReset(
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? '';

        if (!$email) {
            return $this->json(['message' => 'Veuillez fournir un email.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $user = $userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            return $this->json(['message' => 'Si un compte existe avec cet email, un mail de réinitialisation a été envoyé.']);
        }

        // Suppression des anciens tokens associés à l'utilisateur
        $tokenRepository = $entityManager->getRepository(PasswordResetToken::class);
        $existingTokens = $tokenRepository->findBy(['user' => $user]);
        foreach ($existingTokens as $token) {
            $entityManager->remove($token);
        }

        // Génération d'un nouveau token
        $passwordResetToken = new PasswordResetToken($user);
        $entityManager->persist($passwordResetToken);
        $entityManager->flush();

        // Génération du lien de réinitialisation
        $resetUrl = 'http://localhost:5174/reset-password?token=' . $passwordResetToken->getToken();

        // Création de l'email avec un template Twig
        $emailMessage = (new TemplatedEmail())
            ->from(new Address('no-reply@votresite.com', 'Votre Site'))
            ->to($user->getEmail())
            ->subject('Réinitialisation de votre mot de passe')
            ->htmlTemplate('emails/password_reset.html.twig')
            ->context([
                'user' => $user,
                'resetUrl' => $resetUrl,
            ]);

        try {
            $mailer->send($emailMessage);
        } catch (\Exception $e) {
            return $this->json(['message' => 'Erreur lors de l\'envoi de l\'email.'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json(['message' => 'Si un compte existe avec cet email, un mail de réinitialisation a été envoyé.']);
    }

    #[Route('/password-reset/confirm', methods: ['POST'])]
    public function resetPassword(
        Request $request,
        PasswordResetTokenRepository $tokenRepository,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $token = $data['token'] ?? '';
        $newPassword = $data['newPassword'] ?? '';

        if (!$token || !$newPassword) {
            return $this->json(['message' => 'Données manquantes.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $passwordResetToken = $tokenRepository->findOneBy(['token' => $token]);

        if (!$passwordResetToken || $passwordResetToken->isExpired()) {
            return $this->json(['message' => 'Token invalide ou expiré.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $user = $passwordResetToken->getUser();
        $user->setPassword($passwordHasher->hashPassword($user, $newPassword));

        $entityManager->persist($user);
        $entityManager->remove($passwordResetToken);
        $entityManager->flush();

        return $this->json(['message' => 'Mot de passe mis à jour avec succès.']);
    }
}
