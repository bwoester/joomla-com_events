<?php
/**
 * @version     1.0.0
 * @package     com_events
 * @copyright   Copyright (C) 2011. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Created by com_combuilder - http://www.notwebdesign.com
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * HTML View class for the Events component
 */
class EventsViewEvents extends JView
{
	protected $state;
	protected $item;
	protected $print;

  /* @var $eventsModel EventsModelEvents */
  public $eventsModel;

	function display($tpl = null)
	{
		$app    = JFactory::getApplication();
		$params = $app->getParams();
    
		$this->print	= JRequest::getBool('print');

		// Get some data from the models
		$state  = $this->get('State');
		$item   = $this->get('Item');

    $this->eventsModel = $this->getModel( 'events' );

    parent::display($tpl);
	}
}