<?php
/**
* @package     SP Simple Portfolio
* @subpackage  mod_spsimpleportfolio
*
* @copyright   Copyright (C) 2010 - 2020 JoomShaper. All rights reserved.
* @license     GNU General Public License version 2 or later.
*/

defined('_JEXEC') or die('Restricted Access!');

class com_spsimpleportfolioInstallerScript
{
    
    public function uninstall($parent)
    {
        $db = JFactory::getDBO();
        $status = new stdClass;
        $status->modules = array();
        $manifest = $parent->getParent()->manifest;
        
        // Uninstall Modules
        $modules = $manifest->xpath('modules/module');
        foreach ($modules as $module)
        {
            $name = (string)$module->attributes()->module;
            $client = (string)$module->attributes()->client;
            $db = JFactory::getDBO();
            $query = "SELECT `extension_id` FROM `#__extensions` WHERE `type`='module' AND element = ".$db->Quote($name)."";
            $db->setQuery($query);
            $extensions = $db->loadColumn();
            if (count((array) $extensions))
            {
                foreach ($extensions as $id)
                {
                    $installer = new JInstaller;
                    $result = $installer->uninstall('module', $id);
                }
                $status->modules[] = array('name' => $name, 'client' => $client, 'result' => $result);
            }
        }
    }
    
    public function postflight($type, $parent) {
        $db = JFactory::getDbo();
        $src = $parent->getParent()->getPath('source');
        $manifest = $parent->getParent()->manifest;

        // update database
        $columns = $db->getTableColumns('#__spsimpleportfolio_items');
        
        if (!isset($columns['client']))
        {
            try
            {
                $db = JFactory::getDbo();
                $queryStr = "ALTER TABLE `#__spsimpleportfolio_items` ADD `client` varchar(100) NOT NULL AFTER `description`";
                $db->setQuery($queryStr);
                $db->execute();
            }
            catch (Exception $e)
            {
                $parent->getParent()->abort($e->getMessage());
                return false;
            }
        }
        
        // Install Modules
        $modules = $manifest->xpath('modules/module');
        foreach ($modules as $module)
        {
            $name = (string)$module->attributes()->module;
            $client = (string)$module->attributes()->client;
            $path = $src . '/modules/' . $name;
            $position = (isset($module->attributes()->position) && $module->attributes()->position) ? (string)$module->attributes()->position : '';
            $ordering = (isset($module->attributes()->ordering) && $module->attributes()->ordering) ? (string)$module->attributes()->ordering : 0;
            
            $installer = new JInstaller;
            $result = $installer->install($path);
        }
        
        if ($type == 'uninstall')
        {
            return true;
        }
    }
}