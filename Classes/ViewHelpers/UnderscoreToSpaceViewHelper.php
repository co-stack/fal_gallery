<?php
namespace CoStack\FalGallery\ViewHelpers;

/*
 * (c) 2015 Michiel Roos <michiel@maxserv.com>
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

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class UnderscoreToSpaceViewHelper
 */
class UnderscoreToSpaceViewHelper extends AbstractViewHelper
{
    /**
     * Replace underscore with a space
     *
     * @param string $value The value
     *
     * @return string
     */
    public function render($value = null)
    {
        if ($value === null) {
            $value = $this->renderChildren();
            if ($value === null) {
                return '';
            }
        }
        return str_replace('_', ' ', $value);
    }
}
