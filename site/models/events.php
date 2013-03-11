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

jimport('joomla.application.component.model');
jimport('joomla.application.component.helper');

JLoader::import( 'components.com_events.helpers.dateTimeHelper', JPATH_BASE );


JTable::addIncludePath(JPATH_ROOT . '/administrator/components/com_events/tables');



class EventsModelEvents extends JModel
{
  const MYSQL_FORMAT = 'Y-m-d G:i:s';
  const GERMAN_DATE_FORMAT = 'd.m.Y';

  /**
   * @return DateTime
   */
  public function getPeriodStart()
  {
    $periodStart = JRequest::getString( 'periodStart' );
    $dateStart = null;

    if ($periodStart === '')
    {
      $currentMonth = (int)date('n');
      $dateStart = ($currentMonth <= 6) ?
        DateTimeHelper::thisYear() :
        DateTimeHelper::addMonths( DateTimeHelper::thisYear(), 6 );
    }
    else
    {
      $dateStart = new DateTime( $periodStart );
    }

    return $dateStart;
  }

  /**
   * @return int
   */
  public function getPeriodLength()
  {
    return JRequest::getInt('periodLenght') === 0 ? 6 : JRequest::getInt('periodLenght');
  }

  /**
   * @return DateTime
   */
  public function getPeriodEnd()
  {
    return DateTimeHelper::addMonths( $this->getPeriodStart(), $this->getPeriodLength() );
  }

  public function showLinkEarlier()
  {
    // we only show links 5 years into the past
    $sentinel     = DateTimeHelper::substractYears( new DateTime(), 5 );
    $periodStart  = $this->getPeriodStart();

    return (int)$sentinel->format('U') < (int)$periodStart->format('U');
  }

  public function getLinkEarlier()
  {
    $earlier = DateTimeHelper::substractMonths( $this->getPeriodStart(), $this->getPeriodLength() );
    $strNewStart = $earlier->format( DateTimeHelper::ISO_DATE_FORMAT );
    $strPeriodLength = $this->getPeriodLength();
    return JRoute::_( "index.php?view=default&periodStart={$strNewStart}&periodLength={$strPeriodLength}" );
  }

  public function showLinkLater()
  {
    // we only show links 5 years into the future
    $sentinel     = DateTimeHelper::addYears( new DateTime(), 5 );
    $periodStart  = $this->getPeriodStart();

    return (int)$sentinel->format('U') > (int)$periodStart->format('U');
  }

  public function getLinkLater()
  {
    $later = $this->getPeriodEnd();
    $strNewStart = $later->format( DateTimeHelper::ISO_DATE_FORMAT );
    $strPeriodLength = $this->getPeriodLength();
    return JRoute::_( "index.php?view=default&periodStart={$strNewStart}&periodLength={$strPeriodLength}" );
  }

  public function getLinkPrint()
  {
    return ''; //http://localhost/hvtv/jupgrade/presse-stimmen/103-lorem-ipsum-test-artikel?tmpl=component&print=1&layout=default&page=
  }

  public function getLinkCalendar()
  {
    return JRoute::_( "index.php?view=calendar" );
  }

  public function getHeader()
  {
    /* @var $state JRegistry */
    $state = $this->getState('parameters.menu');
    return $state->get('header');
  }

  public function getFooter()
  {
    /* @var $state JRegistry */
    $state = $this->getState('parameters.menu');
    return $state->get('footer');
  }

  public function showPrintAction()
  {
    /* @var $state JRegistry */
    $state = $this->getState('parameters.menu');
    $showPrint = $state->get('showPrintAction');
    return $showPrint;
  }

  public function showEMailAction()
  {
    /* @var $state JRegistry */
    $state = $this->getState('parameters.menu');
    $showEMail = $state->get('showEMailAction');
    return $showEMail;
  }

  public function getEventsForCurrentDay()
  {
    $from = DateTimeHelper::today();
    $to   = DateTimeHelper::tomorrow();
    return $this->getEvents( $from, $to );
  }

  public function getEventsForCurrentWeek()
  {
    $from = DateTimeHelper::thisWeek();
    $to   = DateTimeHelper::nextWeek();
    return $this->getEvents( $from, $to );
  }

  public function getEventsForCurrentMonth()
  {
    $from = DateTimeHelper::thisMonth();
    $to   = DateTimeHelper::nextMonth();
    return $this->getEvents( $from, $to );
  }

  public function getEventsForCurrentQuarter()
  {
    $from = null;
    $currentMonth = (int)date('n');

    // First quarter
    if ($currentMonth <= 3) {
      $from = DateTimeHelper::thisYear();
    }
    // Second quarter
    else if ($currentMonth <= 6) {
      $from = DateTimeHelper::addMonths( DateTimeHelper::thisYear(), 3 );
    }
    // Third quarter
    else if ($currentMonth <= 9) {
      $from = DateTimeHelper::addMonths( DateTimeHelper::thisYear(), 6 );
    }
    // Fourth quarter
    else {
      $from = DateTimeHelper::addMonths( DateTimeHelper::thisYear(), 9 );
    }

    return $this->getEvents( $from, DateTimeHelper::addMonths(clone $from,3) );
  }

  public function getEventsForCurrentHalfYear()
  {
    $from = null;
    $currentMonth = (int)date('n');

    // First half
    if ($currentMonth <= 6) {
      $from = DateTimeHelper::thisYear();
    }
    // Second half
    else
    {
      $from = DateTimeHelper::addMonths( DateTimeHelper::thisYear(), 6 );
    }

    return $this->getEvents( $from, DateTimeHelper::addMonths(clone $from,6) );
  }

  public function getEventsForCurrentYear()
  {
    $from = DateTimeHelper::thisYear();
    $to   = DateTimeHelper::nextYear();
    return $this->getEvents( $from, $to );
  }

  public function getEventsForCurrentPeriod()
  {
    $from = $this->getPeriodStart();
    $to   = $this->getPeriodEnd();
    return $this->getEvents( $from, $to );
  }

  /**
   * @todo allow to filter by category
   * @param DateTime $from
   * @param DateTime $to
   * @return array
   */
  public function getEvents( DateTime $from, DateTime $to )
  {
    $strFrom  = $from->format( self::MYSQL_FORMAT );
    $strTo    = $to->format( self::MYSQL_FORMAT );

    /* @var $query JDatabaseQuery */

    $db     = $this->getDbo();
    $query  = $db->getQuery(true);
    $query->select( 'e.*' );
    $query->from('#__events_event as e');
    $query->where("time_start >= '{$strFrom}' AND time_start < '{$strTo}'");
    $query->order('time_start');

    $db->setQuery((string)$query);
    if (!$db->query())
    {
      JError::raiseError(500, $db->getErrorMsg());
    }

    return $db->loadObjectList();
  }

}
