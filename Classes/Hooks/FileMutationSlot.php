<?php
namespace In2code\FalGallery\Hooks;

/**
 *  Copyright notice
 *
 *  ⓒ 2015 Michiel Roos <michiel@maxserv.com>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is free
 *  software; you can redistribute it and/or modify it under the terms of the
 *  GNU General Public License as published by the Free Software Foundation;
 *  either version 2 of the License, or (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful, but
 *  WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 *  or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for
 *  more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 */

use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Slots that pick up signals when a folder is created, changed or removed.
 *
 * @author Michiel Roos <michiel@maxserv.com>
 */
class FileMutationSlot implements \TYPO3\CMS\Core\SingletonInterface
{
    /**
     * The database connection
     *
     * @var \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected $databaseConnection;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->setDatabaseConnection();
    }

    /**
     * Post file add
     *
     * @param FileInterface $file The file
     * @param Folder $folder The folder
     *
     * @return void
     */
    public function postFileAdd(FileInterface $file, Folder $folder)
    {
        $this->flushCacheForAffectedPages($folder);
    }

    /**
     * Post file copy
     *
     * @param FileInterface $file The file
     * @param Folder $folder The folder
     *
     * @return void
     */
    public function postFileCopy(FileInterface $file, Folder $folder)
    {
        $this->flushCacheForAffectedPages($folder);
    }

    /**
     * Post file create
     *
     * @param FileInterface $file The file
     * @param Folder $folder The folder
     *
     * @return void
     */
    public function postFileCreate(FileInterface $file, Folder $folder)
    {
        $this->flushCacheForAffectedPages($folder);
    }

    /**
     * Post file move
     *
     * @param FileInterface $file The file
     * @param Folder $targetFolder The folder
     * @param Folder $originalFolder The folder
     *
     * @return void
     */
    public function postFileMove(FileInterface $file, Folder $targetFolder, Folder $originalFolder)
    {
        $this->flushCacheForAffectedPages($originalFolder);
        $this->flushCacheForAffectedPages($targetFolder);
    }

    /**
     * Post file rename
     *
     * @param FileInterface $file The file
     * @param Folder $folder The folder
     *
     * @return void
     */
    public function postFileRename(FileInterface $file, Folder $folder)
    {
        $this->flushCacheForAffectedPages($folder);
    }

    /**
     * Post file replace
     *
     * @param FileInterface $file The file
     *
     * @return void
     */
    public function postFileReplace(FileInterface $file)
    {
        $this->flushCacheForAffectedPages($file->getParentFolder());
    }

    /**
     * Pre file delete
     *
     * @param FileInterface $file The file
     *
     * @return void
     */
    public function preFileDelete(FileInterface $file)
    {
        $this->flushCacheForAffectedPages($file->getParentFolder());
    }

    /**
     * Flush cache of pages containing gallery plugins with matching folders
     *
     * This is done two levels deep to take care of folders created inside a
     * category.
     *
     * @param Folder $folder The folder
     *
     * @return void
     */
    protected function flushCacheForAffectedPages(Folder $folder)
    {
        $this->flushCacheForPages(
            $this->getAffectedPageIds(
                $folder->getParentFolder()->getParentFolder()
            )
        );
    }

    /**
     * Flush cache for given page ids
     *
     * @param array $pids An array of page ids
     *
     * @return void
     */
    protected function flushCacheForPages(array $pids)
    {
        if (count($pids)) {
            /** @var \TYPO3\CMS\Core\Cache\CacheManager $cacheManager */
            $cacheManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Cache\\CacheManager');
            foreach ($pids as $pid) {
                $cacheManager->flushCachesByTag('pageId_' . $pid);
            }
        }
    }

    /**
     * Find the affected page ids by going through all the flexforms of all
     * active fal gallery content elements and checking if the current folder
     * is contained in the settings folder.
     *
     * @param Folder $folder The folder to check
     *
     * @return array
     */
    protected function getAffectedPageIds(Folder $folder)
    {
        $pids = array();

        if ($folder->getStorage()->getDriverType() === 'Local') {
            $res = $this->databaseConnection->sql_query(
                "
				SELECT
					pid,
					ExtractValue(pi_flexform, '/T3FlexForms/data/sheet[@index=''list'']/language/field[@index=''settings.default.folder'']/value') as folder
				FROM
					tt_content
				WHERE
					list_type = 'falgallery_pi1'
					AND deleted = 0
					AND hidden = 0
					AND ExtractValue(pi_flexform, '/T3FlexForms/data/sheet[@index=''list'']/language/field[@index=''settings.default.folder'']/value') LIKE 'file:"
                . $folder->getCombinedIdentifier() . "%'"
            );
            while (($row = $this->databaseConnection->sql_fetch_assoc($res))) {
                $pids[] = $row['pid'];
            }
            $this->databaseConnection->sql_free_result($res);
        }

        return $pids;
    }

    /**
     * Set the database connection
     *
     * @return void
     */
    private function setDatabaseConnection()
    {
        $this->databaseConnection = $GLOBALS['TYPO3_DB'];
    }
}
