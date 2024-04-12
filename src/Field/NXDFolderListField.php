<?php

/**
 * @package     Joomla.Plugin
 * @subpackage  Fields.nxdfolderlist
 *
 * @copyright   (C) 2024 NXD | nx-designs Marco Rensch <https://www.nx-designs.ch>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Plugin\Fields\NXDFolderList\Administrator\Field;

use Joomla\CMS\Form\FormField;

defined('_JEXEC') or die;

class NXDFolderListField extends FormField
{
    protected $type = 'nxdfolderlist';

    public function getInput()
    {
        return 'Hello World I am a custom field';
    }
}