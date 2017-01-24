<?php
namespace In2code\FalGallery\Controller;

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
use TYPO3\CMS\Core\Resource\Filter\FileExtensionFilter;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Controller\Arguments;
use TYPO3\CMS\Extbase\Property\TypeConverter\FileConverter;

/**
 * INFO:
 *    1: Storage must not change between Plugins
 *
 * Class GalleryController
 */
class GalleryController extends ActionController
{
    /**
     * @var \TYPO3\CMS\Core\Resource\StorageRepository
     * @inject
     */
    protected $storageRepository;

    /**
     * @var \TYPO3\CMS\Core\Resource\ResourceFactory
     * @inject
     */
    protected $resourceFactory;

    /**
     * @var ResourceStorage
     */
    protected $selectedStorage;

    /**
     * @var array
     */
    protected $storageInformation = array();

    /**
     * @var Folder
     */
    protected $selectedFolder;

    /**
     * A filter class used for filtering storage results
     *
     * @var FileExtensionFilter
     */
    protected $fileExtensionFilter;

    /**
     * @var string
     */
    protected $imageFileExtensions = '';

    /**
     * @var bool
     */
    protected $configurationInvalid = false;

    /**
     * @var string
     */
    protected $configErrorMessage = 'The FAL Gallery Plugin configuration is not correct.'
                                    . ' Check the %s Plugin config. Error: "%s"';

    /**
     * @var array
     */
    protected $errorMessageArray = array(
        'current' => 0,
        0 => 'Unknown Error',
        10 => 'It seems you forgot to specify a default Image',
        11 => 'You might have forgot to configure a folder to display',
    );

    /**
     * Set all the stuff needed for any plugin of this extension
     *
     * @throws \Exception
     * @return void
     */
    public function initializeAction()
    {
        if ($this->validateConfiguration()) {
            $this->setImageFileExtension();
            $this->setImageSizes();
        } else {
            $this->configurationInvalid = true;
        }
    }

    /*******************************
     *
     *  ACTIONS
     *
     ******************************/

    /**
     * The shipped FileConverter does not work because in AbstractFileFolderConverter@54 (6.2.4 core)
     * constructor arguments are not passed to OM->get(File) which results in an exception
     *
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
     * @return void
     */
    public function initializeShowAction()
    {
        if ($this->configurationInvalid) {
            $this->arguments = $this->objectManager->get(Arguments::class);
            return;
        }
        $this->setFileTypeConverterFor('image');
    }

    /**
     * @param File $image
     * @return string|null
     */
    public function showAction(File $image = null)
    {
        if ($this->configurationInvalid) {
            return $this->getErrorMessageForActionName('Show');
        }
        // when this plugin is standalone or no image has been selected in the list view
        if ($image === null) {
            $image = $this->resourceFactory->retrieveFileOrFolderObject($this->settings['default']['image']);
        }
        if ($image instanceof File) {
            $this->view->assign('image', $image);
            $localCopy = $image->getForLocalProcessing();
            if ($this->settings['show']['exif']
                && function_exists('exif_read_data')
                && function_exists('exif_imagetype')
                && exif_imagetype($localCopy)
            ) {
                $this->view->assign('exifInformation', exif_read_data($localCopy, '', true));
                unlink($localCopy);
            }
        }
        return null;
    }

    /**
     * @return void
     */
    public function initializeListAction()
    {
        if ($this->configurationInvalid) {
            $this->arguments = $this->objectManager->get(Arguments::class);
            return;
        }
        $this->setFileTypeConverterFor('image');
        $this->setFileTypeConverterFor('listFolder');
        $this->setFileTypeConverterFor('categoryFolder');
        $this->resolveStorageInformation();
        $this->setFileExtensionFilter();
    }

    /**
     * @param File $image
     * @param File $listFolder
     * @param File $categoryFolder
     * @param int $listPage
     * @param int $categoryPage
     * @return string|null
     */
    public function listAction(
        File $image = null,
        File $listFolder = null,
        File $categoryFolder = null,
        $listPage = 1,
        $categoryPage = 1
    ) {
        if ($this->configurationInvalid) {
            return $this->getErrorMessageForActionName('List');
        }
        $this->view->assign('currentImage', $image);
        $this->view->assign('currentCategoryPage', $categoryPage);
        $this->view->assign('currentCategoryFolder', $categoryFolder);

        // overwrite $selectedFolder when a image from category is clicked
        $selectedFolder = $this->selectedFolder;
        if ($listFolder !== null) {
            /** @var Folder $parentFolder */
            $parentFolder = $listFolder->getParentFolder();
            if ($this->folderIsInsideSelectedStorage($parentFolder)) {
                $selectedFolder = $parentFolder;
            }
        }

        // get all items to display
        $itemsToPaginate = $this->selectedStorage->getFilesInFolder($selectedFolder);

        $this->view->assign('currentListFolder', $this->getFolderImage($selectedFolder));
        $this->assignPaginationParams($itemsToPaginate, $listPage);

        if ($this->settings['list']['useLightBox']) {
            /** @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer $cObj */
            $cObj = $this->configurationManager->getContentObject();
            // lightbox rel attribute is taken from global constants, see typoscript setup
            $this->view->assign(
                'lightboxRelAttribute',
                $cObj->cObjGetSingle(
                    $this->settings['lightboxRelAttribute']['_typoScriptNodeValue'],
                    $this->settings['lightboxRelAttribute']
                )
            );
        }

        // maxImageWidth is taken from tt_content, see typoscript setup
        $this->view->assign('maxImageWidth', $this->settings['maxImageWidth']['_typoScriptNodeValue']);
        return null;
    }

    /**
     * @return void
     */
    public function initializeCategoryAction()
    {
        if ($this->configurationInvalid) {
            $this->arguments = $this->objectManager->get(Arguments::class);
            return;
        }
        $this->setFileTypeConverterFor('image');
        $this->setFileTypeConverterFor('listFolder');
        $this->setFileTypeConverterFor('categoryFolder');
        $this->resolveStorageInformation();
        $this->setFileExtensionFilter();
    }

    /**
     * The selected Folder gets identified by its first file,
     * because folders don't have UIDs and the identifier contains slashes
     * which must not be GET params
     *
     * @param File $image
     * @param File $listFolder
     * @param File $categoryFolder
     * @param int $categoryPage
     * @param int $listPage
     * @return string|null
     */
    public function categoryAction(
        File $image = null,
        File $listFolder = null,
        File $categoryFolder = null,
        $categoryPage = 1,
        $listPage = 1
    ) {
        if ($this->configurationInvalid) {
            return $this->getErrorMessageForActionName('Category');
        }
        $this->view->assign('currentCategoryFolder', $categoryFolder);
        $this->view->assign('currentListFolder', $listFolder);
        $this->view->assign('currentListPage', $listPage);
        $this->view->assign('currentImage', $image);

        $itemsToPaginate = null;
        if ($categoryFolder !== null) {
            /** @var Folder $parentFolder */
            $parentFolder = $categoryFolder->getParentFolder();
            if ($this->folderIsInsideSelectedStorage($parentFolder)) {
                /** @var Folder $folderUpwards */
                $folderUpwards = $parentFolder->getParentFolder();
                $itemsToPaginate = $this->getSubFoldersWithImage($parentFolder);
                if ($this->folderIsInsideSelectedStorage($folderUpwards)) {
                    if ($folderUpwards->getHashedIdentifier() === $this->selectedFolder->getHashedIdentifier()) {
                        $this->view->assign('upwardsIsSelectedFolder', true);
                    } else {
                        $this->view->assign('parentFolderImage', $this->getFolderImage($folderUpwards));
                    }
                }
            }
        }
        if ($itemsToPaginate === null) {
            $itemsToPaginate = $this->getSubFoldersWithImage($this->selectedFolder);
        }

        $this->assignPaginationParams($itemsToPaginate, $categoryPage);
        return null;
    }

    /*******************************
     *
     *  GENERAL METHODS
     *
     ******************************/

    /**
     * @param array $allItems All items that should be paginated
     * @param int $currentPage The page which should be displayed
     * @return void
     */
    protected function assignPaginationParams(array $allItems, $currentPage)
    {
        if ($this->settings['rows'] === '0') {
            $this->settings['rows'] = 241543903;
        }
        $numberOfImages = count($allItems);
        $imagesPerPage = $this->settings['rows'] * $this->settings['cols'];
        $numberOfPages = (int)ceil($numberOfImages / $imagesPerPage);

        // set current page to last page if it goes beyond
        $currentPage = min($currentPage, $numberOfPages);

        $offset = ($currentPage - 1) * $imagesPerPage;
        $imagesToDisplay = array_slice($allItems, $offset, $imagesPerPage, true);

        $this->view->assignMultiple(
            array(
                'numberOfImages' => $numberOfImages,
                'numberOfPages' => $numberOfPages,
                'itemsOnThisPage' => count($imagesToDisplay),
                'firstImage' => ($numberOfPages > 0 ? ($offset + 1) : ($numberOfImages > 0 ? 1 : 0)),
                'lastImage' => min(($offset + $imagesPerPage), $numberOfImages),

                'imageGrid' => $this->getImageGrid($imagesToDisplay),

                'isFirstPage' => ($currentPage === 1),
                'isLastPage' => ($currentPage === $numberOfPages),

                'currentPage' => $currentPage,
                'nextPage' => $currentPage + 1,
                'previousPage' => $currentPage - 1,
            )
        );
    }

    /**
     * @param $argumentName
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\InvalidArgumentNameException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
     * @return void
     */
    protected function setFileTypeConverterFor($argumentName)
    {
        if ($this->arguments->hasArgument($argumentName)) {
            if ($this->request->hasArgument($argumentName)) {
                if ($this->request->getArgument($argumentName) == 0) {
                    $this->request->setArgument($argumentName, null);
                    return;
                }
            }
            /** @var FileConverter $fileConverter */
            $fileConverter = $this->objectManager->get('In2code\\FalGallery\\Property\\TypeConverter\\FileConverter');
            $this->arguments->getArgument($argumentName)->getPropertyMappingConfiguration()->setTypeConverter(
                $fileConverter
            );
        }
    }

    /**
     * @param Folder $parentFolder
     * @return bool
     */
    protected function folderIsInsideSelectedStorage(Folder $parentFolder)
    {
        $parentFolderId = $parentFolder->getIdentifier();
        $selectedFolderId = $this->selectedFolder->getIdentifier();
        if (substr($parentFolderId, 0, strlen($selectedFolderId)) === $selectedFolderId) {
            return true;
        }
        return false;
    }

    /**
     * @param Folder $folder
     * @return File
     */
    protected function getFolderImage(Folder $folder)
    {
        $fileInArray = $this->selectedStorage->getFilesInFolder($folder, 0, 1);
        $folderImage = null;
        if (count($fileInArray) > 0) {
            $folderImage = reset($fileInArray);
        }
        return $folderImage;
    }

    /**
     * @param Folder $folder
     * @return array
     */
    protected function getSubFoldersWithImage(Folder $folder)
    {
        $allFoldersInFolder = $folder->getSubfolders();
        $foldersToDisplay = array();
        /** @var Folder $folder */
        foreach ($allFoldersInFolder as $identifier => $folder) {
            $folderImage = $this->getFolderImage($folder);
            $foldersToDisplay[$identifier] = array(
                'folder' => $folder,
                'folderImage' => $folderImage,
            );
        }
        return $foldersToDisplay;
    }

    /**
     * @param $imagesToDisplay
     * @return array
     */
    protected function getImageGrid($imagesToDisplay)
    {
        // ImageGrid[row][column] = image
        $imagesGrid = array();
        for ($i = 0; $i < $this->settings['rows']; $i++) {
            for ($j = 0; $j < $this->settings['cols']; $j++) {
                if (count($imagesToDisplay) < 1) {
                    return $imagesGrid;
                }
                $imagesGrid[$i][$j] = array_shift($imagesToDisplay);
            }
        }
        return $imagesGrid;
    }

    /*******************************
     *
     *  INITIALIZING METHODS
     *
     ******************************/

    /**
     * @return bool
     */
    public function validateConfiguration()
    {
        $isValid = true;
        if ($this->actionMethodName === 'listAction' || $this->actionMethodName === 'categoryAction') {
            if (!isset($this->settings['default']['folder'])
                || count(explode(':', $this->settings['default']['folder'])) !== 3
            ) {
                $this->errorMessageArray['current'] = 11;
                $isValid = false;
            }
        }
        if ($this->actionMethodName === 'showAction') {
            if (empty($this->settings['default']['image'])) {
                $this->errorMessageArray['current'] = 10;
                $isValid = false;
            }
        }
        return $isValid;
    }

    /**
     * Set the storage and folder to use for the Plugins
     *
     * @throws \Exception
     * @throws \TYPO3\CMS\Core\Resource\Exception\InsufficientFolderAccessPermissionsException
     * @return void
     */
    protected function resolveStorageInformation()
    {
        $selectedFolderParts = explode(':', $this->settings['default']['folder']);
        $this->selectedStorage = $this->storageRepository->findByUid($selectedFolderParts[1]);
        $this->selectedFolder = $this->selectedStorage->getFolder($selectedFolderParts[2]);
    }

    /**
     * Sets $this->imageFileExtensions for later use of filtering file lists in $this->selectedFolder
     *
     * @return void
     */
    protected function setImageFileExtension()
    {
        if (!empty($this->settings['images']['extension'])) {
            $this->imageFileExtensions = $this->settings['images']['extension'];
        } else {
            if ($this->hasImageFileExt()) {
                $this->imageFileExtensions = $this->getImageFileExt();
            } else {
                $this->imageFileExtensions = 'jpg,png,gif,bmp';
            }
        }
    }

    /**
     * @return void
     */
    protected function setFileExtensionFilter()
    {
        // Don't inject the filter, because it's a prototype
        $this->fileExtensionFilter = $this->objectManager->get(
            'TYPO3\\CMS\\Core\\Resource\\Filter\\FileExtensionFilter'
        );
        $this->fileExtensionFilter->setAllowedFileExtensions($this->imageFileExtensions);
        $this->selectedStorage->addFileAndFolderNameFilter(
            array(
                $this->fileExtensionFilter,
                'filterFileList',
            )
        );
    }

    /**
     * @return void
     */
    protected function setImageSizes()
    {
        if ($this->settings['cropping'] && $this->settings['size'][$this->settings['cropping']] > 0) {
            $this->settings['size'][$this->settings['cropping']] .= 'c';
        }
    }

    /**
     * @param $actionName
     * @return string
     */
    protected function getErrorMessageForActionName($actionName)
    {
        return sprintf(
            $this->configErrorMessage,
            $actionName,
            $this->errorMessageArray[$this->errorMessageArray['current']]
        );
    }

    /**
     * @return array
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function getImageFileExt()
    {
        return $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'];
    }

    /**
     * @return bool
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function hasImageFileExt()
    {
        return !empty($GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']);
    }
}
