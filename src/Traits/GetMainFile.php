<?php

declare(strict_types=1);

namespace TomasKulhanek\CzechDataBox\Traits;

use TomasKulhanek\CzechDataBox\DTO\File;

trait GetMainFile
{
	/**
	 * @return array<mixed, mixed>
	 */
	public function getFiles(): array
	{
		return [];
	}

	public function getMainFile(): ?File
	{
		/** @var File $file */
		foreach ($this->getFiles() as $file) {
			if ($file->getMetaType() === 'main') {
				return $file;
			}
		}
		return null;
	}
}
