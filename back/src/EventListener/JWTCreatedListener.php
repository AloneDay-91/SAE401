<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\Security\Core\User\UserInterface;

class JWTCreatedListener
{
    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $user = $event->getUser();

        if (!$user instanceof UserInterface) {
            return;
        }

        // Modifier le payload du JWT pour inclure l'ID de l'utilisateur
        $payload = $event->getData();
        $payload['id'] = $user->getId();
        $payload['nom'] = $user->getNom();
        $payload['prenom'] = $user->getPrenom();
        $payload['roles'] = $user->getRoles();
        $payload['email'] = $user->getEmail();
        $payload['avatar'] = $user->getAvatar();
        $classe = $user->getIdClasses();
        $payload['classes'] = $classe ? ['id' => $classe->getId(), 'intitule' => $classe->getIntitule(), 'promo' => $classe->getPromo(), 'td' => $classe->getTd(), 'tp' => $classe->getTp()] : null;

        $event->setData($payload);
    }
}