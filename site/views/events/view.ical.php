<?php
/**
 * @version     1.0.0
 * @package     com_events
 * @copyright   Copyright (C) 2011. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Benjamin Wöster
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

JLoader::import( 'components.com_events.helpers.dateTimeHelper', JPATH_BASE );



///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////



/**
 * @see "http://tools.ietf.org/html/rfc5545#section-3.6.1"
 */
class VEvent
{
  const STATUS_TENTATIVE = 'TENTATIVE'; // Indicates event is tentative.
  const STATUS_CONFIRMED = 'CONFIRMED'; // Indicates event is definite.
  const STATUS_CANCELLED = 'CANCELLED'; // Indicates event was cancelled.
  
  const CLASS_PUBLIC        = 'PUBLIC';
  const CLASS_PRIVATE       = 'PRIVATE';
  const CLASS_CONFIDENTIAL  = 'CONFIDENTIAL';

  private $_uid     = '';
  private $_dtStamp = null;
  private $_dtStart = null;

  public $dtEnd = null;
  public $class = VEvent::CLASS_PUBLIC;
  public $dtCreated = null;               // CREATED:20120317T221013Z
  public $description = '';               // DESCRIPTION:Termin body
  public $dtLastModified = null;          // LAST-MODIFIED:20120317T221014Z
  public $location = '';                  // LOCATION:location
  public $summary = '';                   // SUMMARY:Termin Subject
  public $status = '';                    // STATUS:CONFIRMED

  public function __construct( $uid, DateTime $stamp, DateTime $start )
  {
    $this->_uid     = $uid;
    $this->_dtStamp = $stamp;
    $this->_dtStart = $start;
  }

  public function getUid()
  {
    return $this->_uid;
  }

  public function getStamp()
  {
    return $this->_dtStamp;
  }

  public function setStamp( DateTime $value )
  {
    $this->_dtStamp = $value;
  }

  public function getStart()
  {
    return $this->_dtStart;
  }

  public function setStart( DateTime $value )
  {
    $this->_dtStart = $value;
  }

  public function renderLines()
  {
    $aLines = array(
      // begin event
      'BEGIN:VEVENT',
      // required eventprops:
      'DTSTAMP:'  . $this->format( $this->_dtStamp ), // @see "http://tools.ietf.org/html/rfc5545#section-3.8.7.2"
      'UID:'      . $this->formatUid( $this->_uid ),
      'DTSTART:'  . $this->format( $this->_dtStart ), // required since we don't specify the "METHOD" property
    );

    if ($this->dtEnd instanceof DateTime) {
      $aLines[] = 'DTEND:' . $this->format( $this->dtEnd );
    }

    if ($this->dtCreated instanceof DateTime) {
      $aLines[] = 'CREATED:' . $this->format( $this->dtCreated );
    }

    if ($this->dtLastModified instanceof DateTime) {
      $aLines[] = 'LAST-MODIFIED:' . $this->format( $this->dtLastModified );
    }

    if (is_string($this->class) && $this->class !== '') {
      $aLines[] = 'CLASS:' . $this->class;
    }

    if ($this->status === self::STATUS_CANCELLED || $this->status === self::STATUS_CONFIRMED || $this->status === self::STATUS_TENTATIVE) {
      $aLines[] = 'STATUS:' . $this->status;
    }
    
    if (is_string($this->summary) && $this->summary !== '') {
      $aLines[] = 'SUMMARY:' . $this->summary;
    }

    if (is_string($this->location) && $this->location !== '') {
      $aLines[] = 'LOCATION:' . $this->location;
    }

    if (is_string($this->description) && $this->description !== '') {
      $aLines[] = 'DESCRIPTION:' . $this->description;
    }

    // end event
    $aLines[] = 'END:VEVENT';

    return $aLines;
  }

  /**
   * Formats a date time property
   * @see "http://tools.ietf.org/html/rfc5545#section-3.3.5"
   * @param DateTime $value
   */
  protected function format( DateTime $value )
  {
    /* @var $tmp DateTime */
    $tmp = clone $value;
    $tmp->setTimezone( new DateTimeZone('UTC') );
    return $tmp->format( DateTimeHelper::ICAL_DATE_TIME_FORMAT );
  }

  /**
   * Formats the uid property. Will result in:
   * [unique hash for event] @ [domain]
   * @see "http://tools.ietf.org/html/rfc5545#section-3.8.4.7"
   * @param string $value
   */
  protected function formatUid( $value )
  {
    $className = get_class($this);
    $left = md5( $className . $value );
    $right = JURI::getInstance()->getHost();

    if (strpos($right,'www.') === 0) {
      $right = substr($right, 4);
    }

    return  $left . '@' . $right;
  }

}



///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////



/**
 * @see "http://tools.ietf.org/html/rfc5545#section-3.4"
 */
class VCalendar
{
  const EOL = "\r\n";

  public $version = '2.0';
  public $prodId  = '-//bwoester//NONSGML com_events//EN';

  private $_aEvents = array();

  public function addEvent( VEvent $event )
  {
    $this->_aEvents[ $event->getUid() ] = $event;
  }

  /**
   * Renders the calendar.
   * @return string
   */
  public function render()
  {
    $aLines = array(
      // begin calendar
      'BEGIN:VCALENDAR',
      // those two are required.
      // @see "http://tools.ietf.org/html/rfc5545#section-3.6"
      'VERSION:' . $this->version,
      'PRODID:'  . $this->prodId,
    );

    // include all events
    // @TODO: ensure there is at least one component in the calendar body
    // @see "http://tools.ietf.org/html/rfc5545#section-3.6"
    // """
    //   In addition, it [the calendar] MUST include at least one calendar
    //   component.
    // """
    foreach ($this->_aEvents as $event)
    {
      $aLines = array_merge( $aLines, $event->renderLines() );
    }

    // end calendar
    $aLines[] = 'END:VCALENDAR';

    // @see "http://tools.ietf.org/html/rfc5545#section-3.1"
    // """
    //   Content lines are delimited by a line break, which is a CRLF sequence
    //   (CR character followed by LF character).
    // """
    return implode( VCalendar::EOL, $aLines ) . VCalendar::EOL;
  }

}



///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////



/**
 * HTML View class for the Events component
 * @see "http://icalvalid.cloudapp.net/" for validating iCal output
 */
class EventsViewEvents extends JView
{
	protected $state;
	protected $item;

  /* @var $eventsModel EventsModelEvents */
  public $eventsModel;

	function display($tpl = null)
	{
    $doc = JFactory::getDocument();

    /* @var $eventsModel EventsModelEvents */
    $eventsModel = $this->eventsModel = $this->getModel( 'events' );

    // Set the MIME type for ical output.
    $doc->setMimeEncoding("text/calendar");

    // Change the suggested filename.
    // ... or better don't do it. At least google's calendar won't work with
    // this....
    // JResponse::setHeader( 'Content-Disposition', 'attachment; filename="'.$this->getName().'.ics"' );

    // begin of current half year
    $periodStart = $eventsModel->getPeriodStart();
    // end of current half year
    $periodEnd = $eventsModel->getPeriodEnd();

    // extend the range one year into the past and one year into the future
    $from = DateTimeHelper::substractYears( $periodStart );
    $to = DateTimeHelper::addYears( $periodEnd );

    $events = $eventsModel->getEvents( $from, $to, $eventsModel->getCatId() );

    $iCal = new VCalendar();
    
    foreach ($events as $event)
    {
      $iEvent = new VEvent(
        $event->id,
        new DateTime(),   // bad: we don't yet store when an event has last been modified...
        new DateTime( $event->time_start )
      );

      $iEvent->description  = $event->description;
      $iEvent->location     = $event->location;
      $iEvent->summary      = $event->title;
      $iEvent->dtEnd        = $event->time_end === '0000-00-00 00:00:00'
        ? DateTimeHelper::addHours( new DateTime($event->time_start), 2 )
        : new DateTime( $event->time_end );
      $iEvent->status       = ((bool)$event->cancelled)
        ? VEvent::STATUS_CANCELLED
        : '';
      
      // currently not supported by com_events
      // $iEvent->dtCreated = ???
      // $iEvent->dtLastModified = ???

      $iCal->addEvent( $iEvent );
    }

    $debugBuffer = $iCal->render();
    echo $debugBuffer;
	}

}