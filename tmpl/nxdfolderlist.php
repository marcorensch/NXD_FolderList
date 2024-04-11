<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Fields.NXDFolderList
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @link        https://www.nx-designs.ch
 * @since       1.0.0
 * @version     1.0.0
 * @var         $fieldParams  Registry  The field parameters
 */

namespace NXD\Plugins\Fields\NXDFolderList;

defined('_JEXEC') or die;

$value = $field->value;
$showOnPage     = $fieldParams->get('pagedisplay', 0);

if ($value && $showOnPage)
{
    echo $value;
}