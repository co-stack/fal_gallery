<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'VerteXVaaR.FalGallery',
    'Pi1',
    array(
        'Gallery' => 'show, list, category',
    ),
    array(
        'Gallery' => '',
    )
);

$listTypeInfo = &$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['list_type_Info'];
$listTypeInfo['falgallery_pi1'][1485254974] = 'VerteXVaaR\\FalGallery\\Hooks\\PluginInformation->build';

// Flush page caches of affected pages when files or folders change
/** @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher $signalSlotDispatcher */
$signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
    'TYPO3\\CMS\\Extbase\\SignalSlot\\Dispatcher'
);
$signalSlotDispatcher->connect(
    'TYPO3\\CMS\\Core\\Resource\\ResourceStorage',
    \TYPO3\CMS\Core\Resource\ResourceStorageInterface::SIGNAL_PostFolderAdd,
    'VerteXVaaR\\FalGallery\\Hooks\\FolderMutationSlot',
    'postFolderMutation'
);
$signalSlotDispatcher->connect(
    'TYPO3\\CMS\\Core\\Resource\\ResourceStorage',
    \TYPO3\CMS\Core\Resource\ResourceStorageInterface::SIGNAL_PostFolderCopy,
    'VerteXVaaR\\FalGallery\\Hooks\\FolderMutationSlot',
    'postFolderMutation'
);
$signalSlotDispatcher->connect(
    'TYPO3\\CMS\\Core\\Resource\\ResourceStorage',
    \TYPO3\CMS\Core\Resource\ResourceStorageInterface::SIGNAL_PreFolderDelete,
    'VerteXVaaR\\FalGallery\\Hooks\\FolderMutationSlot',
    'postFolderMutation'
);
$signalSlotDispatcher->connect(
    'TYPO3\\CMS\\Core\\Resource\\ResourceStorage',
    \TYPO3\CMS\Core\Resource\ResourceStorageInterface::SIGNAL_PostFolderMove,
    'VerteXVaaR\\FalGallery\\Hooks\\FolderMutationSlot',
    'postFolderMove'
);
$signalSlotDispatcher->connect(
    'TYPO3\\CMS\\Core\\Resource\\ResourceStorage',
    \TYPO3\CMS\Core\Resource\ResourceStorageInterface::SIGNAL_PostFolderRename,
    'VerteXVaaR\\FalGallery\\Hooks\\FolderMutationSlot',
    'postFolderRename'
);
$signalSlotDispatcher->connect(
    'TYPO3\\CMS\\Core\\Resource\\ResourceStorage',
    \TYPO3\CMS\Core\Resource\ResourceStorageInterface::SIGNAL_PostFileAdd,
    'VerteXVaaR\\FalGallery\\Hooks\\FileMutationSlot',
    'postFileAdd'
);
$signalSlotDispatcher->connect(
    'TYPO3\\CMS\\Core\\Resource\\ResourceStorage',
    \TYPO3\CMS\Core\Resource\ResourceStorageInterface::SIGNAL_PostFileCopy,
    'VerteXVaaR\\FalGallery\\Hooks\\FileMutationSlot',
    'postFileCopy'
);
$signalSlotDispatcher->connect(
    'TYPO3\\CMS\\Core\\Resource\\ResourceStorage',
    \TYPO3\CMS\Core\Resource\ResourceStorageInterface::SIGNAL_PostFileCreate,
    'VerteXVaaR\\FalGallery\\Hooks\\FileMutationSlot',
    'postFileCreate'
);
$signalSlotDispatcher->connect(
    'TYPO3\\CMS\\Core\\Resource\\ResourceStorage',
    \TYPO3\CMS\Core\Resource\ResourceStorageInterface::SIGNAL_PreFileDelete,
    'VerteXVaaR\\FalGallery\\Hooks\\FileMutationSlot',
    'preFileDelete'
);
$signalSlotDispatcher->connect(
    'TYPO3\\CMS\\Core\\Resource\\ResourceStorage',
    \TYPO3\CMS\Core\Resource\ResourceStorageInterface::SIGNAL_PostFileMove,
    'VerteXVaaR\\FalGallery\\Hooks\\FileMutationSlot',
    'postFileMove'
);
$signalSlotDispatcher->connect(
    'TYPO3\\CMS\\Core\\Resource\\ResourceStorage',
    \TYPO3\CMS\Core\Resource\ResourceStorageInterface::SIGNAL_PostFileRename,
    'VerteXVaaR\\FalGallery\\Hooks\\FileMutationSlot',
    'postFileRename'
);
$signalSlotDispatcher->connect(
    'TYPO3\\CMS\\Core\\Resource\\ResourceStorage',
    \TYPO3\CMS\Core\Resource\ResourceStorageInterface::SIGNAL_PostFileReplace,
    'VerteXVaaR\\FalGallery\\Hooks\\FileMutationSlot',
    'postFileReplace'
);
