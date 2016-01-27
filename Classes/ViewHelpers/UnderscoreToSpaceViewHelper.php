<?php
namespace In2code\FalGallery\ViewHelpers;

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

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class UnderscoreToSpaceViewHelper
 *
 * @package In2code\FalGallery\ViewHelpers
 * @author Michiel Roos <michiel@maxserv.com>
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
