<?php

declare(strict_types=1);

namespace App\Tools\Compressors;

use App\Enum\CompressorTypeEnum;
use ZipArchive;

class ZipCompressor implements FileCompressorInterface
{
    public function compressorTypeSupports(CompressorTypeEnum $type): bool
    {
        return CompressorTypeEnum::ZIP() === $type;
    }

    public function compress(array $fileNames): string
    {
        $zip = new ZipArchive();

        $filename = 'dokobit-'. uniqid() .'.zip';

        if ($zip->open($filename, ZipArchive::CREATE) == TRUE)
        {
            foreach ($fileNames as $fileName) {
                $zip->addFile( 'uploads/' .$fileName);
            }
        }

        return $filename;
    }
}