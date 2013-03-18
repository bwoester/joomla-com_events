<?php

/**
 * @version     1.0.0
 * @package     com_events
 * @copyright   Copyright (C) 2011. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Created by com_combuilder - http://www.notwebdesign.com
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of Events records.
 */
class EventsModelevents extends JModelList {

  /**
   * Constructor.
   *
   * @param    array    An optional associative array of configuration settings.
   * @see        JController
   * @since    1.6
   */
  public function __construct($config = array())
  {
    if (empty($config['filter_fields'])) {
      $config['filter_fields'] = array(
        'id', 'a.id',
        'ordering', 'a.ordering',
        'state', 'a.state',
        'checked_out', 'a.checked_out',
        'checked_out_time', 'a.checked_out_time',
        'catid', 'a.catid', 'category_title',
        'title', 'a.title',
        'time_start', 'a.time_start',
        'description', 'a.description',
        'location', 'a.location',
        'time_end', 'a.time_end',
        'meeting_place', 'a.meeting_place',
        'meeting_time', 'a.meeting_time',
        'cancelled', 'a.cancelled',
    );
    }

    parent::__construct($config);
  }

  /**
   * Method to auto-populate the model state.
   *
   * Note. Calling getState in this method will result in recursion.
   */
  protected function populateState($ordering = null, $direction = null)
  {
    // Initialise variables.
    $app = JFactory::getApplication('administrator');

    // Load the filter state.
    $search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
    $this->setState('filter.search', $search);

		$state = $this->getUserStateFromRequest($this->context.'.filter.state', 'filter_state', '', 'string');
		$this->setState('filter.state', $state);

		$categoryId = $this->getUserStateFromRequest($this->context.'.filter.category_id', 'filter_category_id', '');
		$this->setState('filter.category_id', $categoryId);
    

		// Load the parameters.
		$params = JComponentHelper::getParams('com_events');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('a.time_start', 'desc');
  }

  /**
   * Method to get a store id based on model configuration state.
   *
   * This is necessary because the model is used by the component and
   * different modules that might need different sets of data or different
   * ordering requirements.
   *
   * @param  string    $id  A prefix for the store id.
   * @return  string    A store id.
   * @since  1.6
   */
  protected function getStoreId($id = '') {
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.access');
		$id	.= ':'.$this->getState('filter.state');
		$id	.= ':'.$this->getState('filter.category_id');

    return parent::getStoreId($id);
  }

  /**
   * Build an SQL query to load the list data.
   *
   * @return  JDatabaseQuery
   * @since  1.6
   */
  protected function getListQuery() {
    // Create a new query object.
    $db = $this->getDbo();
    $query = $db->getQuery(true);

    // Select the required fields from the table.
    $query->select(
            $this->getState(
                    'list.select', 'a.*'
            )
    );
    $query->from($db->quoteName('#__events_event').' AS a');

		// Join over the users for the checked out user.
		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

		// Join over the categories.
		$query->select('c.title AS category_title');
		$query->join('LEFT', '#__categories AS c ON c.id = a.catid');

		// Filter by published state
		$published = $this->getState('filter.state');
		if (is_numeric($published)) {
			$query->where('a.state = '.(int) $published);
		} elseif ($published === '') {
			$query->where('(a.state IN (0, 1))');
		}

		// Filter by category.
		$categoryId = $this->getState('filter.category_id');
		if (is_numeric($categoryId)) {
			$query->where('a.catid = '.(int) $categoryId);
		}
    
		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search))
    {
			if (stripos($search, 'id:') === 0)
      {
				$query->where('a.id = '.(int) substr($search, 3));
			}
      else
      {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
        $query->where(
<<<SEARCH_QUERY_WHERE
(
  a.title LIKE {$search}
  OR a.description LIKE {$search}
  OR a.location LIKE {$search}
  OR a.meeting_place LIKE {$search}
)
SEARCH_QUERY_WHERE
        );
			}
		}

    // Add the list ordering clause.
    $orderCol   = $this->state->get( 'list.ordering', 'a.time_start' );
    $orderDirn  = $this->state->get( 'list.direction', 'ASC' );
		if ($orderCol === 'category_title') {
      $orderCol = "c.title {$orderDirn}, a.time_start";
		}
    $query->order( $db->getEscaped("{$orderCol} {$orderDirn}") );
    
    return $query;
  }

}
