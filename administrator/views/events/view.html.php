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
 * View class for a list of Events.
 */
class EventsViewEvents extends JView {

	protected $categories;
  protected $items;
  protected $pagination;
  protected $state;

  /**
   * Display the view
   */
  public function display($tpl = null)
  {
		$this->categories	= $this->get('CategoryOrders');
    $this->items      = $this->get('Items');
    $this->pagination = $this->get('Pagination');
    $this->state      = $this->get('State');

    // Check for errors.
    if (count($errors = $this->get('Errors'))) {
      JError::raiseError(500, implode("\n", $errors));
      return false;
    }

    $this->addToolbar();

    parent::display($tpl);
  }

  /**
   * Add the page title and toolbar.
   *
   * @since  1.6
   */
  protected function addToolbar() {
    require_once JPATH_COMPONENT . '/helpers/events.php';

    $state = $this->get('State');
    $canDo = EventsHelper::getActions($state->get('filter.category_id'));

    JToolBarHelper::title(JText::_('COM_EVENT_MANAGER_EVENTS'), 'events.png');

    //Check if the form exists before showing the add/edit buttons
    $formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/event';
    if (file_exists($formPath)) {

      if ($canDo->get('core.create')) {
        JToolBarHelper::addNew('event.add', 'JTOOLBAR_NEW');
      }

      if ($canDo->get('core.edit') && isset($this->items[0])) {
        JToolBarHelper::editList('event.edit', 'JTOOLBAR_EDIT');
      }
    }

    if ($canDo->get('core.edit.state')) {

      if (isset($this->items[0]->state)) {
        JToolBarHelper::divider();
        JToolBarHelper::custom('events.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
        JToolBarHelper::custom('events.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
      } else if (isset($this->items[0])) {
        //If this component does not use state then show a direct delete button as we can not trash
        JToolBarHelper::deleteList('', 'events.delete', 'JTOOLBAR_DELETE');
      }

      if (isset($this->items[0]->state)) {
        JToolBarHelper::divider();
        JToolBarHelper::archiveList('events.archive', 'JTOOLBAR_ARCHIVE');
      }
      if (isset($this->items[0]->checked_out)) {
        JToolBarHelper::custom('events.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
      }
    }

    //Show trash and delete for components that uses the state field
    if (isset($this->items[0]->state)) {
      if ($state->get('filter.state') == -2 && $canDo->get('core.delete')) {
        JToolBarHelper::deleteList('', 'events.delete', 'JTOOLBAR_EMPTY_TRASH');
        JToolBarHelper::divider();
      } else if ($canDo->get('core.edit.state')) {
        JToolBarHelper::trash('events.trash', 'JTOOLBAR_TRASH');
        JToolBarHelper::divider();
      }
    }

    if ($canDo->get('core.admin')) {
      JToolBarHelper::preferences('com_events');
    }
  }

}
