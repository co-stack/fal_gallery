<?php
namespace VerteXVaaR\FalGallery\Service;

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

use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ResourceResolver
 */
class ResourceResolver implements SingletonInterface
{
    /**
     * @var ResourceResolver|\TYPO3\CMS\Core\LinkHandling\LinkService
     */
    protected $resolver = null;

    /**
     * @var array
     */
    protected $resolved = array();

    /**
     * ResourceResolver constructor.
     */
    public function __construct()
    {
        // if the current version is lower than 8.3 LinkService is not available
        if (1 === version_compare('8.3', TYPO3_branch)) {
            $this->resolver = $this;
        } else {
            $this->resolver = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\LinkHandling\\LinkService');
        }
    }

    /**
     * @param array $parameter
     * @param string $action
     * @return bool
     */
    public function isValid(array $parameter, $action)
    {
        if ('showAction' === $action) {
            $parameterKey = 'image';
        } elseif (in_array($action, array('listAction', 'categoryAction'), true)) {
            $parameterKey = 'folder';
        } else {
            return false;
        }
        $value = $parameter[$parameterKey];

        if (0 === strpos($value, 't3://')) {
            return (false !== ($urn = parse_url($value)) && isset($urn['scheme'], $urn['host'], $urn['query']));
        } else {
            $parts = explode(':', $value);
            if (count($parts) >= 2 && 'file' === $parts[0] && is_numeric($parts[1])) {
                if (in_array($action, array('listAction', 'categoryAction'), true)) {
                    return !empty($parts[2]);
                }
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $linkParameter
     * @return array
     */
    public function resolveResource($linkParameter)
    {
        $resolvedFile = $this->resolver->resolve($linkParameter);
        return $resolvedFile[$resolvedFile['type']];
    }

    /**
     * @param string $linkParameter
     * @return ResourceStorage
     */
    public function resolveStorage($linkParameter)
    {
        if (0 === strpos($linkParameter, 't3://')) {
            $urn = parse_url($linkParameter);
            parse_str($urn['query'], $data);
            $uid = $data['storage'];
        } else {
            $parts = explode(':', $linkParameter);
            $uid = $parts[1];
        }
        return ResourceFactory::getInstance()->getStorageObject($uid);
    }

    /**
     * Fallback method for TYPO3 < 8.3 where LinkService is not available.
     * This resembles the original method used in former versions of fal_gallery.
     *
     * @param string $parameter
     * @return array
     */
    protected function resolve($parameter)
    {
        return array(
            'file' => ResourceFactory::getInstance()->retrieveFileOrFolderObject($parameter),
            'type' => 'file',
        );
    }
}
