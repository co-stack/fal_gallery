<?php
namespace VerteXVaaR\FalGallery\Hooks;

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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Service\FlexFormService;

/**
 * Class PluginInformation
 */
class PluginInformation
{
    /**
     * @var FlexFormService
     */
    protected $flexFormService = null;

    /**
     * PluginInformation constructor.
     */
    public function __construct()
    {
        $this->flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
    }

    /**
     * @param array $contentElement
     * @param PageLayoutView $pageLayoutView
     *
     * @return string
     */
    public function build(array $contentElement, PageLayoutView $pageLayoutView)
    {
        $row = $contentElement['row'];
        $label = BackendUtility::getLabelFromItemListMerged($row['pid'], 'tt_content', 'list_type', $row['list_type']);
        $config = $this->flexFormService->convertFlexFormContentToArray($row['pi_flexform']);
        preg_match('~Gallery\->(?P<type>\w+);~', $config['switchableControllerActions'], $matches);
        return $pageLayoutView->linkEditContent(
            '<strong>' . $label . '</strong>' . ' - ' . ucfirst($matches['type']),
            $row
        );
    }
}
