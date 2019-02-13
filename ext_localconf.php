<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'CoStack.FalGallery',
    'Pi1',
    ['Gallery' => 'show, list, category'],
    ['Gallery' => '']
);

$listTypeInfo = &$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['list_type_Info'];
$listTypeInfo['falgallery_pi1'][1485254974] = \CoStack\FalGallery\Hooks\PluginInformation::class . '->build';

// Flush page caches of affected pages when files or folders change
/** @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher $signalSlotDispatcher */
$signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
    \TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class
);
$signalSlotDispatcher->connect(
    \TYPO3\CMS\Core\Resource\ResourceStorage::class,
    \TYPO3\CMS\Core\Resource\ResourceStorageInterface::SIGNAL_PostFolderAdd,
    \CoStack\FalGallery\Hooks\FolderMutationSlot::class,
    'postFolderMutation'
);
$signalSlotDispatcher->connect(
    \TYPO3\CMS\Core\Resource\ResourceStorage::class,
    \TYPO3\CMS\Core\Resource\ResourceStorageInterface::SIGNAL_PostFolderCopy,
    \CoStack\FalGallery\Hooks\FolderMutationSlot::class,
    'postFolderMutation'
);
$signalSlotDispatcher->connect(
    \TYPO3\CMS\Core\Resource\ResourceStorage::class,
    \TYPO3\CMS\Core\Resource\ResourceStorageInterface::SIGNAL_PreFolderDelete,
    \CoStack\FalGallery\Hooks\FolderMutationSlot::class,
    'postFolderMutation'
);
$signalSlotDispatcher->connect(
    \TYPO3\CMS\Core\Resource\ResourceStorage::class,
    \TYPO3\CMS\Core\Resource\ResourceStorageInterface::SIGNAL_PostFolderMove,
    \CoStack\FalGallery\Hooks\FolderMutationSlot::class,
    'postFolderMove'
);
$signalSlotDispatcher->connect(
    \TYPO3\CMS\Core\Resource\ResourceStorage::class,
    \TYPO3\CMS\Core\Resource\ResourceStorageInterface::SIGNAL_PostFolderRename,
    \CoStack\FalGallery\Hooks\FolderMutationSlot::class,
    'postFolderRename'
);
$signalSlotDispatcher->connect(
    \TYPO3\CMS\Core\Resource\ResourceStorage::class,
    \TYPO3\CMS\Core\Resource\ResourceStorageInterface::SIGNAL_PostFileAdd,
    \CoStack\FalGallery\Hooks\FileMutationSlot::class,
    'postFileAdd'
);
$signalSlotDispatcher->connect(
    \TYPO3\CMS\Core\Resource\ResourceStorage::class,
    \TYPO3\CMS\Core\Resource\ResourceStorageInterface::SIGNAL_PostFileCopy,
    \CoStack\FalGallery\Hooks\FileMutationSlot::class,
    'postFileCopy'
);
$signalSlotDispatcher->connect(
    \TYPO3\CMS\Core\Resource\ResourceStorage::class,
    \TYPO3\CMS\Core\Resource\ResourceStorageInterface::SIGNAL_PostFileCreate,
    \CoStack\FalGallery\Hooks\FileMutationSlot::class,
    'postFileCreate'
);
$signalSlotDispatcher->connect(
    \TYPO3\CMS\Core\Resource\ResourceStorage::class,
    \TYPO3\CMS\Core\Resource\ResourceStorageInterface::SIGNAL_PreFileDelete,
    \CoStack\FalGallery\Hooks\FileMutationSlot::class,
    'preFileDelete'
);
$signalSlotDispatcher->connect(
    \TYPO3\CMS\Core\Resource\ResourceStorage::class,
    \TYPO3\CMS\Core\Resource\ResourceStorageInterface::SIGNAL_PostFileMove,
    \CoStack\FalGallery\Hooks\FileMutationSlot::class,
    'postFileMove'
);
$signalSlotDispatcher->connect(
    \TYPO3\CMS\Core\Resource\ResourceStorage::class,
    \TYPO3\CMS\Core\Resource\ResourceStorageInterface::SIGNAL_PostFileRename,
    \CoStack\FalGallery\Hooks\FileMutationSlot::class,
    'postFileRename'
);
$signalSlotDispatcher->connect(
    \TYPO3\CMS\Core\Resource\ResourceStorage::class,
    \TYPO3\CMS\Core\Resource\ResourceStorageInterface::SIGNAL_PostFileReplace,
    \CoStack\FalGallery\Hooks\FileMutationSlot::class,
    'postFileReplace'
);
