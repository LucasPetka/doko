<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Uploader;
use App\Tools\Compressors\ZipCompressor;
use App\Tools\FileHandler;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api_')]
class UploadController extends AbstractFOSRestController
{
    public function __construct(
        private FileHandler $fileHandler,
        private ZipCompressor $zipCompressor,
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('/filezip', name: 'file_zip', methods: ['POST'])]
    public function upload(Request $request): Response
    {
        if (empty($request->files->getIterator()->getArrayCopy())) {
            return $this->handleView($this->view(['error'=>'I need some files'],Response::HTTP_OK));
        }

        try {
            $uploadedFiles = $this->fileHandler->uploadMultiple($request->files->getIterator()->getArrayCopy());
        } catch (Exception $exception){
            return $this->handleView($this->view(['error'=> $exception->getMessage()],Response::HTTP_OK));
        }

        $uploader = new Uploader();
        $uploader->setId(Uuid::uuid1());
        $uploader->setIpAddress($request->getClientIp());
        $uploader->setFileCount(count($uploadedFiles));

        $this->entityManager->persist($uploader);
        $this->entityManager->flush();


        $zip = $this->zipCompressor->compress($uploadedFiles);
        $this->fileHandler->removeMultiple($uploadedFiles);

        return $this->handleView($this->view($this->file($zip)->deleteFileAfterSend(true)));
    }

}
