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

JLoader::import( 'components.com_events.helpers.dateTimeHelper', JPATH_BASE );

/**
 * HTML View class for the Events component
 */
class EventsViewEvents extends JView
{
	protected $state;
	protected $item;

  /* @var $eventsModel EventsModelEvents */
  public $eventsModel;

  /**
   * @todo only return published events (depending on user rights?)
   * @param type $tpl
   */
	function display($tpl = null)
	{
    // Set the MIME type for JSON output.
    JFactory::getDocument()->setMimeEncoding("application/json");
    // Change the suggested filename.
    JResponse::setHeader( 'Content-Disposition', 'attachment; filename="'.$this->getName().'.json"' );

    $timestampFrom  = JRequest::getInt('start');
    $timestampTo    = JRequest::getInt('end');

    $from = DateTimeHelper::fromTimestamp( $timestampFrom );
    $to   = DateTimeHelper::fromTimestamp( $timestampTo );

    /* @var $eventsModel EventsModelEvents */
    $eventsModel = $this->getModel( 'events' );
    $events = $eventsModel->getEvents( $from, $to );
    $data = array();

    foreach ($events as $event)
    {
      $tooltip = $this->generateTooltip( $event );
      $data[] = array(
        'title'   => $event->title,
        'start'   => $event->time_start,
        'end'     => $event->time_end,
        'tooltip' => $tooltip,
      );
    }   

    echo json_encode($data);
	}

  protected function generateTooltip( $event )
  {
    $timeStart = new DateTime( $event->time_start );
    $strTimeStart = $timeStart->format( DateTimeHelper::GERMAN_FORMAT ) . ' Uhr';
    return '<dl>
      
      <dt>Was</dt>
      <dd>'.$event->title.'</dd>

      <dt>Wann</dt>
      <dd>'.$strTimeStart.'</dd>

      <dt>Wo</dt>
      <dd>'.$event->location.'</dd>

    </dl>';
  }

}