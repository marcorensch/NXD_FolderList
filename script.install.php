<?php
/**
 * @package    nxd_folderlist
 *
 * @author     proximate <your@email.com>
 * @copyright  A copyright
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * @link       http://your.url.com
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\InstallerScript;
use Joomla\CMS\Installer\Adapter\PluginAdapter;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseInterface;

defined('_JEXEC') or die;

/**
 * Nxd FolderList script file.
 *
 * @package   plugin.nxdfolderlist
 * @since     1.0.0
 */
class PlgFieldsNXDFolderListInstallerScript extends InstallerScript
{
    public $alias = 'nxdfolderlist';
    public $extension_type = 'plugin';
    public $plugin_folder = 'fields';
    public $show_message = false;

    // Enable Plugin after installation

    /**
     * @throws Exception
     */
    public function install(PluginAdapter $adapter): void
    {
        // Enqueue Message
        $version = $adapter->getManifest()->version;
        $app = Factory::getApplication();
        $app->enqueueMessage(Text::sprintf('PLG_FIELDS_NXD_FOLDERLIST_INSTALLATION_TEXT', $version), 'message');

        // Enable Plugin
        $this->enablePlugin();

    }

    /**
     * @throws Exception
     */
    public function update(PluginAdapter $adapter): void
    {
        // Enqueue Message
        $version = $adapter->getManifest()->version;
        $app = Factory::getApplication();
        $app->enqueueMessage(Text::sprintf('PLG_FIELDS_NXD_FOLDERLIST_UPDATE_TEXT', $version), 'message');
    }


    public function uninstall(PluginAdapter $adapter): void
    {
        $app = Factory::getApplication();
        $app->enqueueMessage(Text::_('PLG_FIELDS_NXD_FOLDERLIST_UNINSTALLATION_TEXT'), 'message');
    }


    private function enablePlugin(): void
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        $query->update($db->quoteName('#__extensions'))
            ->set($db->quoteName('enabled') . ' = 1')
            ->where($db->quoteName('type') . ' = ' . $db->quote($this->extension_type))
            ->where($db->quoteName('element') . ' = ' . $db->quote($this->alias))
            ->where($db->quoteName('folder') . ' = ' . $db->quote($this->plugin_folder));
        $db->setQuery($query);
        $db->execute();
    }


}