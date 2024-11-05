<?php

/**
 * @package     Spsimpleportfolio.Site
 * @subpackage  Spsimpleportfolio.Finder
 *
 * @copyright   Copyright (C) 2023 - 2024 JoomShaper <https://www.joomshaper.com>. All rights reserved.
 * @license     GNU General Public License version 3; see LICENSE
 */

namespace JoomShaper\Plugin\Finder\Spsimpleportfolio\Extension;

use Joomla\CMS\Table\Table;
use Joomla\Registry\Registry;
use Joomla\Database\DatabaseQuery;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Finder\Administrator\Indexer\Helper;
use Joomla\Component\Finder\Administrator\Indexer\Result;
use Joomla\Component\Finder\Administrator\Indexer\Adapter;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Smart Search adapter for com_spsimpleportfolio.
 *
 * @since  1.0.0
 */
class Spsimpleportfolio extends Adapter
{
    use DatabaseAwareTrait;

    /**
     * The plugin identifier.
     *
     * @var    string
     * @since  1.0.0
     */
    protected $context = 'Spsimpleportfolio';

    /**
     * The extension name.
     *
     * @var    string
     * @since  1.0.0
     */
    protected $extension = 'com_spsimpleportfolio';

    /**
     * The sublayout to use when rendering the results.
     *
     * @var    string
     * @since  1.0.0
     */
    protected $layout = 'item';

    /**
     * The type of portfolio item that the adapter indexes.
     *
     * @var    string
     * @since  1.0.0
     */
    protected $type_title = 'Protfoilo item';

    /**
     * The table name.
     *
     * @var    string
     * @since  1.0.0
     */
    protected $table = '#__spsimpleportfolio_items';

    /**
     * The field the published state is stored in.
     *
     * @var    string
     * @since  1.0.0
     */
    protected $state_field = 'published';

    /**
     * Load the language file on instantiation.
     *
     * @var    boolean
     * @since  1.0.0
     */
    protected $autoloadLanguage = true;

    /**
     * Method to index an item. The item must be a Result object.
     *
     * @param   Result  $item  The item to index as a Result object.
     *
     * @return  void
     *
     * @since   1.0.0
     * @throws  \Exception on database error.
     */
    protected function index(Result $item)
    {
        // Check if the extension is enabled.
        if (ComponentHelper::isEnabled($this->extension) === false) {
            return;
        }

        $item->setLanguage();

        // Initialize the item parameters.
        $item->params = new Registry($item->params);

        // Create a URL as identifier to recognise items again.
        $item->url = $this->getUrl($item->id, $this->extension, $this->layout);

        // Build the necessary route and path information.
        $item->route = $this->getItemLink($item->id, $item->language);


        // Add the type taxonomy data.
        $item->addTaxonomy('Type', 'Portfolio Item');

        // Add the language taxonomy data.
        $item->addTaxonomy('Language', $item->language);

        // Get content extras.
        Helper::getContentExtras($item);

        // Index the item.
        $this->indexer->index($item);
    }

    /**
     * Method to setup the indexer to be run.
     *
     * @return  boolean  True on success.
     *
     * @since   1.0.0
     */
    protected function setup()
    {
        return true;
    }

    /**
     * Method to remove the link information for items that have been deleted.
     *
     * @param   string  $context  The context of the action being performed.
     * @param   Table   $table    A Table object containing the record to be deleted
     *
     * @return  void
     *
     * @since   1.0.0
     * @throws  \Exception on database error.
     */
    public function onFinderAfterDelete($context, $table): void
    {
        if ($context === 'com_spsimpleportfolio.item') {
            $id = $table->id;
        } elseif ($context === 'com_finder.index') {
            $id = $table->link_id;
        } else {
            return;
        }

        // Remove item from the index.
        $this->remove($id);
    }

    /**
     * Method to get the SQL query used to retrieve the list of product items.
     *
     * @param   mixed  $query  A DatabaseQuery object or null.
     *
     * @return  DatabaseQuery  A database object.
     *
     * @since   1.0.0
     */
    protected function getListQuery($query = null)
    {
        $db = $this->getDatabase();

        $query = $db->getQuery(true);

        $query->select($db->quoteName(['id', 'title', 'alias', 'description', 'access']));
        $query->select($db->quoteName('published', 'state'));

        // Handle the alias CASE WHEN portion of the query.
        $case_when_item_alias = ' CASE WHEN ';
        $case_when_item_alias .= $query->charLength($db->quoteName('alias'), '!=', '0');
        $case_when_item_alias .= ' THEN ';
        $a_id = $query->castAsChar($db->quoteName('id'));
        $case_when_item_alias .= $query->concatenate([$a_id, 'alias'], ':');
        $case_when_item_alias .= ' ELSE ';
        $case_when_item_alias .= $a_id . ' END AS slug';

        $query->select($case_when_item_alias)
            ->from($db->quoteName('#__spsimpleportfolio_items'));
        
        return $query;
    }

    public function getItemLink($id, $language)
	{
		// Create the link
        $link = Uri::root() . 'index.php?option=com_spsimpleportfolio&view=item&id=' . $id;

        if ($language && $language !== '*' && Multilanguage::isEnabled()) {
            $link .= '&lang=' . $language;
        }

        return $link;
	}
}