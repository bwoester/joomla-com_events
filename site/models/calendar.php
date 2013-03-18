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

JLoader::import( 'components.com_events.models.events', JPATH_BASE );



class EventsModelCalendar extends EventsModelEvents
{

  public function getLinkEvents()
  {
    // From "http://docs.joomla.org/Supporting_SEF_URLs_in_your_component":
    // "Notice that it is possible to leave out the parameters option and
    // Itemid.
    // option defaults to the name of the component currently being executed,
    // and Itemid defaults to the current menu item's ID."
    return JRoute::_( "index.php?view=events&format=json" );
  }

}
