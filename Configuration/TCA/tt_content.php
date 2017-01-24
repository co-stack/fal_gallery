<?php
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'fal_gallery',
    'Pi1',
    'FAL Gallery'
);

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['falgallery_pi1'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'falgallery_pi1',
    'FILE:EXT:fal_gallery/Configuration/FlexForms/flexform_pi1.xml'
);
