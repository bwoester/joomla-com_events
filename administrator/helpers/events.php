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
   * @return  JObject
   * @since  1.6
   */
  public static function getActions() {
    $user = JFactory::getUser();
    $result = new JObject;

    $assetName = 'com_events';

    $actions = array(
        'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
    );

    foreach ($actions as $action) {
      $result->set($action, $user->authorise($action, $assetName));
    }

    return $result;
  }

}
