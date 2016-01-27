<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'In2code.' . $_EXTKEY,
    'Pi1',
    array(
        'Gallery' => 'show, list, category',
    ),
    // non-cacheable actions
    array(
        'Gallery' => '',
    )
);

// Flush page caches of affected pages when files or folders change
/** @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher $signalSlotDispatcher */
$signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
    'TYPO3\\CMS\\Extbase\\SignalSlot\\Dispatcher'
);
$signalSlotDispatcher->connect(
    'TYPO3\\CMS\\Core\\Resource\\ResourceStorage',
    \TYPO3\CMS\Core\Resource\ResourceStorageInterface::SIGNAL_PostFolderAdd,
    'In2code\\FalGallery\\Hooks\\FolderMutationSlot',
    'postFolderMutation'
);
$signalSlotDispatcher->connect(
    'TYPO3\\CMS\\Core\\Resource\\ResourceStorage',
    \TYPO3\CMS\Core\Resource\ResourceStorageInterface::SIGNAL_PostFolderCopy,
    'In2code\\FalGallery\\Hooks\\FolderMutationSlot',
    'postFolderMutation'
);
$signalSlotDispatcher->connect(
    'TYPO3\\CMS\\Core\\Resource\\ResourceStorage',
    \TYPO3\CMS\Core\Resource\ResourceStorageInterface::SIGNAL_PreFolderDelete,
    'In2code\\FalGallery\\Hooks\\FolderMutationSlot',
    'postFolderMutation'
);
$signalSlotDispatcher->connect(
    'TYPO3\\CMS\\Core\\Resource\\ResourceStorage',
    \TYPO3\CMS\Core\Resource\ResourceStorageInterface::SIGNAL_PostFolderMove,
    'In2code\\FalGallery\\Hooks\\FolderMutationSlot',
    'postFolderMove'
);
$signalSlotDispatcher->connect(
    'TYPO3\\CMS\\Core\\Resource\\ResourceStorage',
    \TYPO3\CMS\Core\Resource\ResourceStorageInterface::SIGNAL_PostFolderRename,
    'In2code\\FalGallery\\Hooks\\FolderMutationSlot',
    'postFolderRename'
);
$signalSlotDispatcher->connect(
    'TYPO3\\CMS\\Core\\Resource\\ResourceStorage',
    \TYPO3\CMS\Core\Resource\ResourceStorageInterface::SIGNAL_PostFileAdd,
    'In2code\\FalGallery\\Hooks\\FileMutationSlot',
    'postFileAdd'
);
$signalSlotDispatcher->connect(
    'TYPO3\\CMS\\Core\\Resource\\ResourceStorage',
    \TYPO3\CMS\Core\Resource\ResourceStorageInterface::SIGNAL_PostFileCopy,
    'In2code\\FalGallery\\Hooks\\FileMutationSlot',
    'postFileCopy'
);
$signalSlotDispatcher->connect(
    'TYPO3\\CMS\\Core\\Resource\\ResourceStorage',
    \TYPO3\CMS\Core\Resource\ResourceStorageInterface::SIGNAL_PostFileCreate,
    'In2code\\FalGallery\\Hooks\\FileMutationSlot',
    'postFileCreate'
);
$signalSlotDispatcher->connect(
    'TYPO3\\CMS\\Core\\Resource\\ResourceStorage',
    \TYPO3\CMS\Core\Resource\ResourceStorageInterface::SIGNAL_PreFileDelete,
    'In2code\\FalGallery\\Hooks\\FileMutationSlot',
    'preFileDelete'
);
$signalSlotDispatcher->connect(
    'TYPO3\\CMS\\Core\\Resource\\ResourceStorage',
    \TYPO3\CMS\Core\Resource\ResourceStorageInterface::SIGNAL_PostFileMove,
    'In2code\\FalGallery\\Hooks\\FileMutationSlot',
    'postFileMove'
);
$signalSlotDispatcher->connect(
    'TYPO3\\CMS\\Core\\Resource\\ResourceStorage',
    \TYPO3\CMS\Core\Resource\ResourceStorageInterface::SIGNAL_PostFileRename,
    'In2code\\FalGallery\\Hooks\\FileMutationSlot',
    'postFileRename'
);
$signalSlotDispatcher->connect(
    'TYPO3\\CMS\\Core\\Resource\\ResourceStorage',
    \TYPO3\CMS\Core\Resource\ResourceStorageInterface::SIGNAL_PostFileReplace,
    'In2code\\FalGallery\\Hooks\\FileMutationSlot',
    'postFileReplace'
);
