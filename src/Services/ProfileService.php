<?php

namespace App\Services;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProfileService
{
    private string $projectDir;

    public function __construct(
        private EntityManagerInterface $em,
        private UserRepository $repo,
        private UserPasswordHasherInterface $hasher,
        string $projectDir
    ) {
        $this->projectDir = $projectDir;
    }

    public function getProfile(User $user): array
    {
        return [
            'email' => $user->getEmail(),
            'nickname' => $user->getNickname(),
            'avatar' => $user->getAvatarPath()
        ];
    }

    public function updateEmail(User $user, array $data): array
    {
        $newEmail = $data['email'] ?? null;

        if (!$newEmail) {
            return ['error' => 'Email обязателен'];
        }

        $existing = $this->repo->findOneBy(['email' => $newEmail]);

        if ($existing && $existing->getId() !== $user->getId()) {
            return ['error' => 'Email уже занят'];
        }

        $user->setEmail($newEmail);
        $this->em->flush();

        return ['success' => true];
    }

    public function updatePassword(User $user, array $data): array
    {
        $oldPassword = $data['oldPassword'] ?? null;
        $newPassword = $data['newPassword'] ?? null;

        if (!$this->hasher->isPasswordValid($user, $oldPassword)) {
            return ['error' => 'Старый пароль неверный'];
        }

        $hashed = $this->hasher->hashPassword($user, $newPassword);
        $user->setPassword($hashed);
        $this->em->flush();

        return ['success' => true];
    }

    public function updateNickname(User $user, array $data): array
    {
        $nickname = $data['nickname'] ?? null;

        if (!$nickname) {
            return ['error' => 'Никнейм обязателен'];
        }

        $user->setNickname($nickname);
        $this->em->flush();

        return ['success' => true];
    }

    public function updateAvatar(User $user, ?UploadedFile $file): array
    {
        if (!$file) {
            return ['error' => 'Файл не загружен'];
        }

        $uploadDir = $this->projectDir . '/public/uploads/avatars';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $extension = $file->getClientOriginalExtension() ?: 'png';
        $fileName = uniqid() . '.' . $extension;
        $file->move($uploadDir, $fileName);

        $avatarPath = '/uploads/avatars/' . $fileName;
        $user->setAvatarPath($avatarPath);
        $this->em->flush();

        return ['success' => true, 'avatarPath' => $avatarPath];
    }
}
