<?php

declare(strict_types=1);

namespace App\Tools\Compressors;

use App\Enum\CompressorTypeEnum;

interface FileCompressorInterface
{
    public function compressorTypeSupports(CompressorTypeEnum $type): bool;

    /**
     * @param string[] $fileNames
     */
    public function compress(array $fileNames): string;
}