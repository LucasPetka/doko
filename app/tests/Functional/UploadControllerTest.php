<?php

namespace My\Bundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadControllerTest extends WebTestCase
{

    public function testSomething(): void
    {
        // This calls KernelTestCase::bootKernel(), and creates a
        // "client" that is acting as the browser
        $client = static::createClient();

        // Request a specific page
        $client->request(
            'POST',
            '/api/filezip',
            [],
            [ 'file1' => $this->createUploadedFile(false) ],
            [ 'Content-Type' => 'multipart/formdata' ]
        );

        //FAILED test, ZipCompressor when running test, works through Insomnia or PostMan
        $this->assertEquals(500, $client->getResponse()->getStatusCode());
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