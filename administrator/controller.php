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

class EventsController extends JController
{
  /**
   * Method to display a view.
   *
   * @param  boolean      $cachable  If true, the view output will be cached
   * @param  array      $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
   *
   * @return  JController    This object to support chaining.
   * @since  1.5
   */
  public function display($cachable = false, $urlparams = false)
  {
    require_once JPATH_COMPONENT.'/helpers/events.php';

		// Load the submenu.
		EventsHelper::addSubmenu( JFactory::getApplication()->input->getCmd('view','events') );
    
    $view   = JFactory::getApplication()->input->getCmd('view', 'events');
    $layout = JFactory::getApplication()->input->getCmd('layout', 'default');
    $id     = JFactory::getApplication()->input->getInt('id');

		// Check for edit form.
		if ($view == 'event' && $layout == 'edit' && !$this->checkEditId('com_events.edit.event', $id))
    {
      // Somehow the person just went to the form - we don't allow that.
      $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
      $this->setMessage($this->getError(), 'error');
      $this->setRedirect(JRoute::_('index.php?option=com_events&view=events', false));

      return false;
		}

    parent::display($cachable, $urlparams);

    return $this;
  }
}
