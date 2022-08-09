<?php

declare(strict_types=1);

namespace App\Tools;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\UrlHelper;

class FileHandler
{
    private const MEGABYTE = 1000000;

    private string $relativeUploadsDir;

    public function __construct(
        string $publicPath,
        private string $uploadPath,
        private SluggerInterface $slugger,
        private UrlHelper $urlHelper,
        private Filesystem $fileSystem,
    ) {
        $this->relativeUploadsDir = str_replace($publicPath, '', $this->uploadPath).'/';
    }

    /**
     * @param UploadedFile[] $files
     * @return string[]
     */
    public function uploadMultiple(array $files): array
    {
        $filenames = [];
        foreach($files as $file) {

            if($file->getSize() !== null && self::MEGABYTE < $file->getSize()){
                $this->removeMultiple($filenames);
                throw new Exception('File "' . $file->getClientOriginalName() . '" is bigger than 1Mb :(');
            }

            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $this->slugger->slug($originalFilename);
            $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

            try {
                $file->move($this->getuploadPath(), $fileName);
            } catch (FileException $e) {
                throw new Exception('Failed to upload "' . $file->getClientOriginalName() . '" file. Error: '. $e);
            }

            $filenames[] = $fileName;
            unset($file);
        }

        return $filenames;
    }

    /**
     * @param string[] $fileNames
     */
    public function removeMultiple(array $fileNames): void
    {
        foreach($fileNames as $fileName) {
            $this->fileSystem->remove($this->getFileUploadPath($fileName));
        }
    }

    public function getuploadPath()
    {
        return $this->uploadPath;
    }

    public function getFileUploadPath($fileName): string
    {
        return $this->uploadPath . '/' . $fileName;
    }

    public function getUrl(?string $fileName, bool $absolute = true)
    {
        if (empty($fileName)) return null;

        if ($absolute) {
            return $this->urlHelper->getAbsoluteUrl($this->relativeUploadsDir.$fileName);
        }

        return $this->urlHelper->getRelativePath($this->relativeUploadsDir.$fileName);
    }
}