<?php

namespace My\Bundle\Tests\Controller;

use App\Tools\FileHandler;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileHandlerTest extends KernelTestCase
{

    public function testFileUploadSuccess()
    {
        self::bootKernel();
        $container = static::getContainer();

        $fileHandler = $container->get(FileHandler::class);
        $fileNames = $fileHandler->uploadMultiple([$this->createUploadedFile(false)]);

        $this->assertFileExists($fileHandler->getFileUploadPath($fileNames[0]));
    }

    public function testFileUploadFailure()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('File "emptyfile.txt" is bigger than 1Mb :(');
        self::bootKernel();
        $container = static::getContainer();

        $fileHandler = $container->get(FileHandler::class);
        $fileHandler->uploadMultiple([$this->createUploadedFile(true)]);
    }

    public function testRemoveMultipleSuccess()
    {
        self::bootKernel();
        $container = static::getContainer();

        $fileHandler = $container->get(FileHandler::class);

        $fileNames = $fileHandler->uploadMultiple([$this->createUploadedFile(false)]);
        $this->assertFileExists($fileHandler->getFileUploadPath($fileNames[0]));

        $fileHandler->removeMultiple($fileNames);
        $this->assertFileDoesNotExist($fileHandler->getFileUploadPath($fileNames[0]));
    }


    /**
     * @param bool $moreThanMb
     * @return UploadedFile
     */
    private function createUploadedFile(bool $moreThanMb): UploadedFile
    {
        $file = tempnam(sys_get_temp_dir(), 'upl');
        $text = '';

        if($moreThanMb) {
            $text .= str_repeat('This is some random text.', 500000);
        } else {
            $text = 'This is some random text.';
        }

        file_put_contents($file, $text);

        return new UploadedFile(
            $file,
            'emptyfile.txt',
            null,
            null,
            true
        );
    }
}