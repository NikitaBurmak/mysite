<?php

namespace App\Controller\Trait;

use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;

trait AuthenticationTrait
{
    protected function requireAuth(): void
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    }

    protected function requireAuthOrJson(): JsonResponse|null
    {
        $user = $this->getUser();
        if (!$user instanceof UserInterface) {
            return $this->json(['requireLogin' => true], 401);
        }

        return null;
    }

    protected function requireAdmin(): void
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
    }

    protected function requireAdminOrJson(): JsonResponse|null
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->json(['error' => 'Access denied. Admin required.'], 403);
        }

        return null;
    }

    protected function getAuthUser(): ?UserInterface
    {
        $user = $this->getUser();

        if ($user instanceof UserInterface) {
            return $user;
        }

        return null;
    }

    protected function getAppUser(): ?User
    {
        $user = $this->getUser();

        if ($user instanceof User) {
            return $user;
        }

        return null;
    }

    protected function requireAuthOrRedirect(string $redirectRoute = 'home'): RedirectResponse|JsonResponse|null
    {
        $user = $this->getUser();

        if (!$user instanceof UserInterface) {
            // Проверяем, что это AJAX запрос
            $request = $this->container->get('request_stack')->getCurrentRequest();
            if ($request && $request->isXmlHttpRequest()) {
                return $this->json(['requireLogin' => true], 401);
            }

            return $this->redirectToRoute($redirectRoute);
        }

        return null;
    }

    protected function hasRole(string $role): bool
    {
        return $this->isGranted($role);
    }

    protected function isAdmin(): bool
    {
        return $this->isGranted('ROLE_ADMIN');
    }

    protected function isAuthenticated(): bool
    {
        return $this->isGranted('IS_AUTHENTICATED_FULLY');
    }
}
