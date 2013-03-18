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

/**
 * Events helper.
 */
class EventsHelper {

  /**
   * Configure the Linkbar.
   */
  public static function addSubmenu( $vName = '' )
  {
    JSubMenuHelper::addEntry(
      JText::_('COM_EVENTS_SUBMENU_EVENTS'),
      'index.php?option=com_events&view=events',
      $vName == 'events'
    );
    
		JSubMenuHelper::addEntry(
			JText::_('COM_EVENTS_SUBMENU_CATEGORIES'),
			'index.php?option=com_categories&extension=com_events',
			$vName == 'categories'
    );
    
    if ($vName=='categories')
    {
      JToolBarHelper::title(
        JText::sprintf('COM_CATEGORIES_CATEGORIES_TITLE', JText::_('COM_EVENTS')),
        'events-categories'
      );
    }
  }

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param	int		The category ID.
	 *
	 * @return	JObject
	 * @since	1.6
	 */
	public static function getActions($categoryId = 0)
	{
		$user   = JFactory::getUser();
		$result	= new JObject;

		if (empty($categoryId))
    {
			$assetName  = 'com_events';
			$level      = 'component';
		}
    else
    {
			$assetName  = 'com_events.category.' . (int)$categoryId;
			$level      = 'category';
		}

		$actions = JAccess::getActions( 'com_events', $level );

		foreach ($actions as $action) {
			$result->set( $action->name, $user->authorise($action->name,$assetName) );
		}

		return $result;
	}

}
