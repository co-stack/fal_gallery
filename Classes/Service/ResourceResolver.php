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

use TYPO3\CMS\Core\LinkHandling\LinkService;
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
     * @var LinkService
     */
    protected $resolver = null;

    /**
     * @var array
     */
    protected $resolved = [];

    /**
     * ResourceResolver constructor.
     */
    public function __construct()
    {
        $this->resolver = GeneralUtility::makeInstance(LinkService::class);
    }

    /**
     * @param array $parameter
     * @param string $action
     *
     * @return bool
     */
    public function isValid($parameter, $action)
    {
        if (!is_array($parameter)) {
            return false;
        }
        if ('showAction' === $action) {
            $parameterKey = 'image';
        } elseif (in_array($action, ['listAction', 'categoryAction'], true)) {
            $parameterKey = 'folder';
        } else {
            return false;
        }
        $value = $parameter[$parameterKey];

        if (0 === strpos($value, 't3://')) {
            return (false !== ($urn = parse_url($value)) && isset($urn['scheme'], $urn['host'], $urn['query']));
        }

        return false;
    }

    /**
     * @param string $linkParameter
     *
     * @return array
     */
    public function resolveResource($linkParameter)
    {
        $resolvedFile = $this->resolver->resolve($linkParameter);
        return $resolvedFile[$resolvedFile['type']];
    }

    /**
     * @param string $linkParameter
     *
     * @return ResourceStorage
     */
    public function resolveStorage($linkParameter)
    {
        $urn = parse_url($linkParameter);
        parse_str($urn['query'], $data);
        return ResourceFactory::getInstance()->getStorageObject($data['storage']);
    }
}
