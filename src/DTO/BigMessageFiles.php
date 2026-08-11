<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\DTO;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Element dmFiles velkoobjemové datové zprávy (VoDZ). Kombinuje odkazy
 * na dříve nahrané přílohy (dmExtFile) a malé inline přílohy (dmFile),
 * proto jsou obě kolekce serializované inline v jednom wrapperu.
 */
#[Serializer\XmlRoot(namespace: 'http://isds.czechpoint.cz/v20', name: 'dmFiles')]
#[Serializer\XmlNamespace(uri: 'http://isds.czechpoint.cz/v20', prefix: 'p')]
#[Serializer\AccessorOrder(order: 'custom', custom: ['extFiles', 'files'])]
class BigMessageFiles
{
    /**
     * @var ExtFile[]
     */
    #[Serializer\Type('array<TomasKulhanek\CzechDataBox\DTO\ExtFile>')]
    #[Serializer\XmlList(entry: 'dmExtFile', inline: true, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Assert\All([
        new Assert\Type(ExtFile::class)
    ])]
    #[Assert\Valid()]
    protected array $extFiles = [];

    /**
     * @var File[]
     */
    #[Serializer\Type('array<TomasKulhanek\CzechDataBox\DTO\File>')]
    #[Serializer\XmlList(entry: 'dmFile', inline: true, namespace: 'http://isds.czechpoint.cz/v20')]
    #[Assert\All([
        new Assert\Type(File::class)
    ])]
    #[Assert\Valid()]
    protected array $files = [];

    /**
     * @return ExtFile[]
     */
    public function getExtFiles(): array
    {
        return $this->extFiles;
    }

    /**
     * @param ExtFile[] $extFiles
     */
    public function setExtFiles(array $extFiles): BigMessageFiles
    {
        $this->extFiles = $extFiles;
        return $this;
    }

    public function addExtFile(ExtFile $extFile): BigMessageFiles
    {
        $this->extFiles[] = $extFile;
        return $this;
    }

    /**
     * @return File[]
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * @param File[] $files
     */
    public function setFiles(array $files): BigMessageFiles
    {
        $this->files = $files;
        return $this;
    }

    public function addFile(File $file): BigMessageFiles
    {
        $this->files[] = $file;
        return $this;
    }
}
