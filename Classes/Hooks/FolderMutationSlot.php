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

use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Slots that pick up signals when a folder is created, changed or removed.
 *
 * @author Michiel Roos <michiel@maxserv.com>
 */
class FolderMutationSlot implements \TYPO3\CMS\Core\SingletonInterface
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
     * Flush cache of pages containing gallery plugins with matching folders
     * when folder is added, removed or changed.
     *
     * This is done two levels deep to take care of folders created inside a
     * category.
     *
     * @param Folder $folder The folder
     * @param object $some Some
     * @param object $other Other
     * @param object $parameters Parameters
     *
     * @return void
     */
    public function postFolderMutation(Folder $folder, $some = null, $other = null, $parameters = null)
    {
        $this->flushCacheForAffectedPages($folder);
    }

    /**
     * Post folder move
     *
     * @param Folder $folder The folder
     * @param Folder $targetFolder The target folder
     * @param string $newName The new name
     * @param Folder $originalFolder The original folder
     *
     * @return void
     */
    public function postFolderMove(Folder $folder, Folder $targetFolder, $newName, Folder $originalFolder)
    {
        $this->flushCacheForAffectedPages($originalFolder);
        $this->flushCacheForAffectedPages($targetFolder);
    }

    /**
     * Post folder rename
     *
     * @param Folder $folder The folder
     * @param string $newName The new name
     *
     * @return void
     */
    public function postFolderRename(Folder $folder, $newName)
    {
        $this->flushCacheForAffectedPages($folder);
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
        $evaluate = $folder->getStorage()->getEvaluatePermissions();
        $folder->getStorage()->setEvaluatePermissions(false);
        $this->flushCacheForPages(
            $this->getAffectedPageIds(
                $folder->getParentFolder()->getParentFolder()
            )
        );
        $folder->getStorage()->setEvaluatePermissions($evaluate);
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
