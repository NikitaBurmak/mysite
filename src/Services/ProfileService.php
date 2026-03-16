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

        // Проверка MIME типа
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $mimeType = $file->getMimeType();

        if (!in_array($mimeType, $allowedMimes, true)) {
            return ['error' => 'Недопустимый тип файла. Разрешены: jpg, jpeg, png, gif, webp'];
        }

        // Проверка размера файла (максимум 2MB = 2097152 байт)
        $maxSize = 2 * 1024 * 1024; // 2MB
        $fileSize = $file->getSize();

        if ($fileSize > $maxSize) {
            return ['error' => 'Файл слишком большой. Максимальный размер: 2MB'];
        }

        // Проверка размера изображения (опционально)
        $imageInfo = getimagesize($file->getPathname());
        if ($imageInfo === false) {
            return ['error' => 'Недопустимое изображение'];
        }

        // Ограничение на максимальные размеры изображения
        $maxWidth = 2000;
        $maxHeight = 2000;
        if ($imageInfo[0] > $maxWidth || $imageInfo[1] > $maxHeight) {
            return ['error' => 'Слишком большое разрешение изображения. Максимум: 2000x2000'];
        }

        $uploadDir = $this->projectDir . '/public/uploads/avatars';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Безопасное получение расширения
        $extension = $file->guessExtension() ?: 'png';
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!in_array($extension, $allowedExtensions, true)) {
            $extension = 'png';
        }

        $fileName = uniqid() . '.' . $extension;
        $file->move($uploadDir, $fileName);

        $avatarPath = '/uploads/avatars/' . $fileName;
        $user->setAvatarPath($avatarPath);
        $this->em->flush();

        return ['success' => true, 'avatarPath' => $avatarPath];
    }
}
