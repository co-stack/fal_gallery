<?php
namespace CoStack\FalGallery\Hooks;

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

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\View\PageLayoutView;
use TYPO3\CMS\Core\LinkHandling\Exception\UnknownLinkHandlerException;
use TYPO3\CMS\Core\LinkHandling\Exception\UnknownUrnException;
use TYPO3\CMS\Core\LinkHandling\LinkService;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Service\FlexFormService;

/**
 * Class PluginInformation
 */
class PluginInformation
{
    /**
     * TODO: replace with TYPO3\CMS\Core\Service\FlexFormService when dropping TYPO3 v8
     *
     * This class has an alias in TYPO3 v9
     * @var FlexFormService
     */
    protected $flexFormService = null;

    /**
     * @var LinkService
     */
    protected $linkService = null;

    /**
     * PluginInformation constructor.
     */
    public function __construct()
    {
        $this->flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
        $this->linkService = GeneralUtility::makeInstance(LinkService::class);
    }

    /**
     * @param array $contentElement
     * @param PageLayoutView $pageLayoutView In core PageLayoutView in TemplaVoilà! Plus BackendLayoutController
     *
     * @return string
     */
    public function build(array $contentElement, $pageLayoutView)
    {
        $row = $contentElement['row'];
        $label = BackendUtility::getLabelFromItemListMerged($row['pid'], 'tt_content', 'list_type', $row['list_type']);
        $config = $this->flexFormService->convertFlexFormContentToArray($row['pi_flexform']);
        $pluginType = $this->getSelectedPluginType($config);

        $defaultSource = '&gt;none&lt;';
        $defaultStorage = '&gt;none&lt;';
        switch ($pluginType) {
            case 'show':
                $this->resolveFileInformation($config, $defaultSource, $defaultStorage);
                break;
            case 'list':
                $this->resolveFolderInformation($config, $defaultSource, $defaultStorage);
                break;
            case 'category':
                $this->resolveFolderInformation($config, $defaultSource, $defaultStorage);
                break;
            default:
                break;
        }
        $pluginType = ucfirst($pluginType);

        $pluginDescription = <<<HTML
<strong>$label</strong> - $pluginType<br/>
Default source: $defaultSource<br/>
File storage: $defaultStorage
HTML;
        if ($pageLayoutView instanceof PageLayoutView) {
            return $pageLayoutView->linkEditContent($pluginDescription, $row);
        }
        return $pluginDescription;
    }

    /**
     * @param array $config
     * @return string
     */
    protected function getSelectedPluginType(array $config)
    {
        preg_match('~Gallery\->(?P<type>\w+);~', $config['switchableControllerActions'], $matches);
        return $matches['type'];
    }

    /**
     * @param $config
     * @param string $defaultSource
     * @param string $defaultStorage
     */
    protected function resolveFolderInformation(array $config, &$defaultSource, &$defaultStorage)
    {
        if (isset($config['settings']['default']['folder'])) {
            $folderIdentifier = $config['settings']['default']['folder'];
            /** @var Folder $folder */
            try {
                $folder = $this->linkService->resolveByStringRepresentation($folderIdentifier)['folder'];
            } catch (UnknownLinkHandlerException $e) {
                return;
            } catch (UnknownUrnException $e) {
                return;
            }
            $defaultSource = $folder->getReadablePath();
            $defaultStorage = $folder->getStorage()->getName();
        }
    }

    /**
     * @param $config
     * @param string $defaultSource
     * @param string $defaultStorage
     */
    protected function resolveFileInformation(array $config, &$defaultSource, &$defaultStorage)
    {
        if (isset($config['settings']['default']['image'])) {
            $imageIdentifier = $config['settings']['default']['image'];
            /** @var File $image */
            try {
                $image = $this->linkService->resolveByStringRepresentation($imageIdentifier)['file'];
            } catch (UnknownLinkHandlerException $e) {
                return;
            } catch (UnknownUrnException $e) {
                return;
            }
            $defaultSource = $image->getIdentifier();
            $defaultStorage = $image->getStorage()->getName();
        }
    }
}
