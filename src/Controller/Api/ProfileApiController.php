<?php

namespace App\Controller\Api;

use App\Controller\Trait\AuthenticationTrait;
use App\Services\ProfileService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/profile')]
class ProfileApiController extends AbstractController
{
    use AuthenticationTrait;

    public function __construct(
        private ProfileService $profileService
    )
    {
    }

    #[Route('', methods: ['GET'])]
    public function getProfile(): JsonResponse
    {
        $this->requireAuth();

        return $this->json(
            $this->profileService->getProfile($this->getUser())
        );
    }

    #[Route('/email', methods: ['PUT'])]
    public function updateEmail(Request $request): JsonResponse
    {
        $this->requireAuth();

        return $this->json(
            $this->profileService->updateEmail(
                $this->getUser(),
                json_decode($request->getContent(), true)
            )
        );
    }

    #[Route('/password', methods: ['PUT'])]
    public function updatePassword(Request $request): JsonResponse
    {
        $this->requireAuth();

        return $this->json(
            $this->profileService->updatePassword(
                $this->getUser(),
                json_decode($request->getContent(), true)
            )
        );
    }

    #[Route('/nickname', methods: ['PUT'])]
    public function updateNickname(Request $request): JsonResponse
    {
        $this->requireAuth();

        return $this->json(
            $this->profileService->updateNickname(
                $this->getUser(),
                json_decode($request->getContent(), true)
            )
        );
    }

    #[Route('/avatar', methods: ['POST'])]
    public function updateAvatar(Request $request): JsonResponse
    {
        $this->requireAuth();

        return $this->json(
            $this->profileService->updateAvatar(
                $this->getUser(),
                $request->files->get('avatar')
            )
        );
    }
}
