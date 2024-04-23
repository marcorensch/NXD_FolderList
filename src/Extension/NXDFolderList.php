<?php

/**
 * @package     Joomla.Plugin
 * @subpackage  Fields.nxdfolderlist
 *
 * @copyright   (C) 2024 NXD | nx-designs Marco Rensch <https://www.nx-designs.ch>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Plugin\Fields\NXDFolderList\Extension;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Component\Fields\Administrator\Plugin\FieldsPlugin;
use Joomla\Registry\Registry;
use stdClass;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Fields NXD FolderList Plugin
 *
 * @since  4.4.0
 */

final class NXDFolderList extends FieldsPlugin
{
    public function __construct($subject, $config)
    {
        parent::__construct($subject, $config);
        FormHelper::addFieldPath(JPATH_PLUGINS . '/' . $this->_type . '/' . $this->_name . '/src/Field');
    }

    /**
     * Transforms the field into a DOM XML element and appends it as a child on the given parent.
     *
     * @param stdClass $field The field.
     * @param \DOMElement $parent The field node parent.
     * @param Form $form The form.
     *
     * @return  \DOMElement | null The field node.
     *
     * @since   3.7.0
     */
    public function onCustomFieldsPrepareDom($field, \DOMElement $parent, Form $form): false|\DOMElement|null
    {
        $fieldNode = parent::onCustomFieldsPrepareDom($field, $parent, $form);
        if (!$fieldNode)
        {
            return false;
        }

        $fieldNode->setAttribute('hide_default', true);
        $fieldNode->setAttribute('hide_none', false);
        $fieldNode->setAttribute('recursive', true);

        return $fieldNode;
    }

    // Ajax call to get the folder list
    public function onAjaxNxdfolderlist(): string
    {
        $input = Factory::getApplication()->input;
        $data = $input->get('data', '{}', 'string');
        $data = json_decode($data, true);
        $path = $this->createFullPath($data['path']);
        $response = new stdClass();
        $response->error = false;

        $response->requestPath = $path;


        try{
            $response->children = $this->getFolderList($path);
        }catch(\Exception $e){
            $response->error = $e->getMessage();
        }

        // @ToDo: Check if the parent folder is the root folder if enabled in settings
        $thisPlugin = PluginHelper::getPlugin('fields', 'nxdfolderlist');
        // Get plugin params
        $pluginParams = new Registry($thisPlugin->params);
        $definedRootFolder = $this->createFullPath($pluginParams->get('root-folder', ''));
        $limitToParent = $pluginParams->get('limit-to-parent', 0);

        if(!$limitToParent){
            $response->parentFolderPath = $path === JPATH_ROOT ? false : dirname($path);
        }else{
            $response->parentFolderPath = $path === $definedRootFolder ? false : dirname($path);
        }

        // Get the relative path to the parent folder only if not the root folder (limited or effectively)
        $response->relativeParentFolderPath = $response->parentFolderPath ? $this->getRelativePath($path) : false;

        return json_encode($response);
    }

    private function createFullPath($path){
        if(!$path || $path === '/' || $path === 'undefined'){
            $path = JPATH_ROOT;
        }else{
            if(strpos($path, JPATH_ROOT) === false){
                $path = JPATH_ROOT . $path;
            }else{
                // nothing to do
            }
        }

        return $path;
    }

    private function getRelativePath($path): string
    {
        if(dirname($path) === JPATH_ROOT){
            return "/";
        }
        if(!str_contains(JPATH_ROOT, $path)){
            $newPath = str_replace(JPATH_ROOT, '', $path);
            return dirname($newPath);
        }

        return "";
    }

    private function getFolderList(string $path): array
    {
        $children = [];
        $realPath = str_replace('\\', '/', realpath($path));

        if (is_dir($realPath)) {
            $dir = new \DirectoryIterator($path);

            foreach ($dir as $fileInfo) {
                if ($fileInfo->isDir() && !$fileInfo->isDot()) {
                    // Create the cleaned pathName by removing everything before the JPATH_ROOT constant from the path
                    $serverPath = str_replace(JPATH_ROOT, '', $fileInfo->getPathname());

                    $children[] = [
                        'name' => $fileInfo->getFilename(),
                        'relativePath' => $serverPath,
                        'realPath' => $fileInfo->getPathname(),
                        'parent' => dirname($serverPath),
                    ];
                }
            }

            // Order the folders by name ASC
            usort($children, function($a, $b){
                return strnatcasecmp($a['name'], $b['name']);
            });
        }

        return $children;
    }
}
