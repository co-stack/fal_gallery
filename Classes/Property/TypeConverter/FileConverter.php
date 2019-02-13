<?php
namespace CoStack\FalGallery\Property\TypeConverter;

/*
 * (c) Oliver Eglseder <php@vxvr.de>
 *
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\SingletonInterface;

/**
 * Class FileConverter
 */
class FileConverter extends AbstractFileFolderConverter implements SingletonInterface
{
    /**
     * @var array<string>
     */
    protected $sourceTypes = ['integer', 'string'];

    /**
     * @var string
     */
    protected $targetType = File::class;

    /**
     * @var string
     */
    protected $expectedObjectType = File::class;

    /**
     * @param string|integer $source
     *
     * @return FileInterface|Folder
     */
    protected function getOriginalResource($source)
    {
        return $this->fileFactory->retrieveFileOrFolderObject($source);
    }
}
