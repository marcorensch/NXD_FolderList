<?php

/**
 * @package     Joomla.Plugin
 * @subpackage  Fields.nxdfolderlist
 *
 * @copyright   (C) 2024 NXD | nx-designs Marco Rensch <https://www.nx-designs.ch>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Plugin\Fields\NXDFolderList\Extension;

use Joomla\CMS\Form\Form;
use Joomla\Component\Fields\Administrator\Plugin\FieldsPlugin;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Fields NXD FolderList Plugin
 *
 * @since  3.7.0
 */
final class NXDFolderList extends FieldsPlugin
{
    /**
     * Transforms the field into a DOM XML element and appends it as a child on the given parent.
     *
     * @param   stdClass    $field   The field.
     * @param   \DOMElement  $parent  The field node parent.
     * @param   Form        $form    The form.
     *
     * @return  \DOMElement
     *
     * @since   3.7.0
     */
    public function onCustomFieldsPrepareDom($field, \DOMElement $parent, Form $form)
    {
        $fieldNode = parent::onCustomFieldsPrepareDom($field, $parent, $form);

        if (!$fieldNode) {
            return $fieldNode;
        }

//        $fieldNode->setAttribute('buttons', $field->fieldparams->get('buttons', $this->params->get('buttons', 0)) ? 'true' : 'false');
//        $fieldNode->setAttribute('hide', implode(',', $field->fieldparams->get('hide', [])));

        return $fieldNode;
    }
}
