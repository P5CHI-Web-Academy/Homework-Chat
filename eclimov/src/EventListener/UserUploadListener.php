<?php

namespace App\EventListener;

use \App\Service\FileUploader;
use Doctrine\ORM\Event\LifecycleEventArgs;
use \App\Entity\User;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UserUploadListener
{
    /**
     * @var FileUploader
     */
    private $uploader;

    /**
     * UserUploadListener constructor.
     * @param FileUploader $uploader
     */
    public function __construct(FileUploader $uploader)
    {
        $this->uploader = $uploader;
    }

    /**
     * @param $entity
     * @throws \Exception
     */
    private function uploadFile($entity): void
    {
        // For User entities only
        if ($entity instanceof User) {
            $logoFile = $entity->getAvatar();

            if ($logoFile instanceof UploadedFile) {
                $fileName = $this->uploader->upload($logoFile);
                $entity->setAvatar($fileName);
            }
        }
    }

    /**
     * @param LifecycleEventArgs $args
     * @throws \Exception
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        $this->uploadFile($entity);
    }

    /**
     * @param $entity
     */
    public function stringToFile($entity): void
    {
        if ($entity instanceof User) {
            $file = $this->uploader->getTargetDirectory() . '/' . $entity->getAvatar();
            if ($entity->getAvatar() && file_exists($file)) {
                $entity->setAvatar(new File($file));
            }
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postLoad(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        $this->stringToFile($entity);
    }

    /**
     * @param $entity
     */
    public function fileToString($entity): void
    {
        if ($entity instanceof User) {
            if (($file = $entity->getAvatar()) instanceof File) {
                $entity->setAvatar($file->getFilename());
            }
        }
    }

    /**
     * @param PreUpdateEventArgs $args
     * @throws \Exception
     */
    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getEntity();
        $this->uploadFile($entity);
        $this->fileToString($entity);
    }
}
