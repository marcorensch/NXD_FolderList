<?php

/**
 * @package     Joomla.Plugin
 * @subpackage  Fields.nxdfolderlist
 *
 * @copyright   (C) 2024 NXD | nx-designs Marco Rensch <https://www.nx-designs.ch>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Plugin\Fields\NXDFolderList\Field;

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;

defined('_JEXEC') or die;

class NXDFolderListField extends FormField
{
    protected $type = 'nxdfolderlist';

    protected $assetsPath =  "plugins/fields/nxdfolderlist/src/Field/assets/";

    public function setup(\SimpleXMLElement $element, $value, $group = null)
    {
        return parent::setup($element, $value, $group);
    }

    /**
     * Method to get the field input markup.
     *
     * @return  string  The field input markup.
     *
     * @since   3.7.0
     */
    public function getInput(): string
    {
        // Check if the options have been set
        $readonly = $this->getAttribute('readonly', 'false') === 'true';
        $readonlyString = $readonly ? 'readonly' : '';

        $modalId = 'modal-nxdFolderSelect_' . $this->id;
        $loadingSpinner = '<div class="loading-spinner-container"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
        $modalBody = "<div class='p-3 nxd-modal-inner'>$loadingSpinner</div>";

        $modalParams['title'] = Text::_('PLG_FIELDS_NXD_FOLDERLIST_ROOT_FOLDER_SELECT_MODAL_TITLE');
        $modalParams['footer'] = '<button type="button" class="btn btn-success nxd-modal-save-btn" data-bs-dismiss="modal">' . Text::_('SAVE') . '</button> <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">' . Text::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</button>';
        $modalParams['height'] = '100%';
        $modalParams['width'] = '100%';
        $modalParams['bodyHeight'] = 70;
//      $modalParams['modalWidth'] = 50;
        echo HTMLHelper::_('bootstrap.renderModal', $modalId, $modalParams , $modalBody);

        $this->loadAssets();
        $inputFieldHtml = '<input type="text" name="' . $this->name . '" id="' . $this->id . '" value="'.$this->value.'" class="form-control nxd-folder-list-value-input" '.$readonlyString.' />';
        $btnHtml = '<button data-bs-toggle="modal" data-bs-target="#' . $modalId . '" type="button" class="btn btn-primary nxd-folder-list-btn" data-input-field-id="' . $this->id . '">' . Text::_('PLG_FIELDS_NXD_FOLDERLIST_ROOT_FOLDER_SELECT_BTN_LBL') . '</button>';
        $inputGridHtml = '<div class="nxd-folder-list-container row" id="' . $this->id . '-container"><div class="col-12 col-md-8 col-xl-10">' . $inputFieldHtml . '</div><div class="col nxd-btn-container">' . $btnHtml . '</div></div>';
        return $inputGridHtml;
    }

    /**
     * @throws Exception
     * @since 2.0.0
     */
    private function loadAssets(): void
    {
        $fullAssetsPath = Uri::root() . $this->assetsPath . 'js/nxd_folder_list_field.js';
        $app = Factory::getApplication();
        $isFieldPluginEdit = $app->input->get('option') === 'com_plugins' && $app->input->get('view') === 'field' && $app->input->get('layout') === 'edit';
        $definedRootFolder = "";
        $limitToParent = 0;

        if (!$isFieldPluginEdit) {
            $thisPlugin = PluginHelper::getPlugin('fields', 'nxdfolderlist');
            // Get plugin params
            $pluginParams = new Registry($thisPlugin->params);
            $definedRootFolder = $pluginParams->get('root-folder', '');
            $limitToParent = $pluginParams->get('limit-to-parent', 0);
        }


        $wa = $app->getDocument()->getWebAssetManager();
        $wa->useScript('jquery');
        // include the JS and CSS files
        $wa->registerAndUseStyle('nxd-folder-list-field-css', $this->assetsPath . 'css/nxd_folder_list_field.css', ['core']);

        // Add Inline JS
        $js = <<<JS
        import { nxdFolderListBtnClickHandler } from '{$fullAssetsPath}';
        jQuery(document).ready(function($) {
            console.log('NXD Folder List Field JQuery loaded');
            // Add Event Listener for the button
            $('#{$this->id}-container').on('click', '.nxd-folder-list-btn', function(e) {
                console.log('Folder list button clicked');
                // get the current input field id from the button's data attribute
                const params = {
                    inputFieldId: "{$this->id}",
                    definedRootFolder: "{$definedRootFolder}",
                    limitToParent: {$limitToParent}
                };
                
                // call the click handler function
                nxdFolderListBtnClickHandler(e, params);
            });
        });
        JS;

        $wa->addInlineScript($js, [], ['type' => 'module']);
    }
}