<?php

    /**
    * @author    JoomShaper http://www.joomshaper.com
    * @copyright Copyright (C) 2010 - 2013 JoomShaper
    * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2
    */

    defined('JPATH_BASE') or die;

    // Requred Componenet and module helper
    //require_once JPATH_ROOT . '/modules/mod_sp_soccer_recent_results/helper.php';

    jimport('joomla.form.formfield');
    jimport('joomla.filesystem.folder');
    jimport('joomla.filesystem.file');
    
    class JFormFieldCategorylist extends JFormField {

        protected $type = 'categorylist';

        protected function getInput(){

            // Get Tournaments
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);

            // Select all records from the user profile table where key begins with "custom.".
            $query->select($db->quoteName(array('id', 'title' )));
            $query->from($db->quoteName('#__categories'));
            $query->where($db->quoteName('published')." = 1");
            $query->where($db->quoteName('extension')." = 'com_spsimpleportfolio'");
            $query->order('title ASC');

            $db->setQuery($query);  
            $results = $db->loadObjectList();
            $categories_list = $results;

            

            $options = array(''=>'All categories');
            
            foreach($categories_list as $category){
                $options[] = JHTML::_( 'select.option', $category->id, $category->title );

                //print_r($tournament);
            }
            
            return JHTML::_('select.genericlist', $options, 'jform[params]['.$this->fieldname.']', '', 'value', 'text', $this->value);
        }
    }
