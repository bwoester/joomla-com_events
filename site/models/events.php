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



///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////



class PeriodStartOption
{
  const _DEFAULT_     = 0;
  const CURRENT_DAY   = 0;
  const CURRENT_WEEK  = 1;
  const CURRENT_MONTH = 2;
  const CURRENT_YEAR  = 3;
  const DYNAMIC       = 4;
}



///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////



class PeriodLength
{
  const _DEFAULT_ = 6;
}



///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////



class PeriodUnit
{
  const _DEFAULT_ = 2;
  const DAYS      = 0;
  const WEEKS     = 1;
  const MONTHS    = 2;
  const YEARS     = 3;
  const DECADES   = 4;
}



///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////



class EventsModelEvents extends JModel
{

  /////////////////////////////////////////////////////////////////////////////
  
  const MYSQL_FORMAT = 'Y-m-d G:i:s';
  const GERMAN_DATE_FORMAT = 'd.m.Y';

  /////////////////////////////////////////////////////////////////////////////
  
  /**
   * Returns the value that has been configured for the ical help article.
   * @return string
   */
  public function getParamICalHelpArticle()
  {
    return (string)JComponentHelper::getParams('com_events')
      ->get( 'iCalHelpArticle', '' );
  }
  
  /////////////////////////////////////////////////////////////////////////////
  
  /**
   * Returns a PeriodStartOption constant 
   * @return int
   */
  private function getParamPeriodStartOption()
  {
    return (int)JComponentHelper::getParams('com_events')
      ->get( 'periodStartOption', PeriodStartOption::_DEFAULT_ );
  }

  /////////////////////////////////////////////////////////////////////////////
  
  /**
   * Returns the period length. Use together with period unit.
   * @return int
   */
  private function getParamPeriodLength()
  {
    return (int)JComponentHelper::getParams('com_events')
      ->get( 'periodLength', PeriodLength::_DEFAULT_ );
  }

  /////////////////////////////////////////////////////////////////////////////
  
  /**
   * Returns a PeriodUnit constant. Use together with period unit.
   * @return int
   */
  private function getParamPeriodUnit()
  {
    return (int)JComponentHelper::getParams('com_events')
      ->get( 'periodUnit', PeriodUnit::_DEFAULT_ );
  }

  /////////////////////////////////////////////////////////////////////////////

  /**
   * Returns the category id. Category id 0 means: All categories.
   * @return int
   */
  private function getParamCatId()
  {
    return (int)JComponentHelper::getParams('com_events')
      ->get( 'catid', 0 );
  }

  /////////////////////////////////////////////////////////////////////////////

  /**
   * Return if the print action should be shown to the user
   * @return bool
   */
  private function getParamShowPrintAction()
  {
    return (bool)JComponentHelper::getParams('com_events')
      ->get( 'showPrintAction', false );
  }

  /////////////////////////////////////////////////////////////////////////////
  
  /**
   * Return the intro
   * @return string
   */
  private function getParamHeader()
  {
    return (string)JComponentHelper::getParams('com_events')
      ->get( 'header', '' );
  }

  /////////////////////////////////////////////////////////////////////////////
  
  /**
   * Return the footer
   * @return string
   */
  private function getParamFooter()
  {
    return (string)JComponentHelper::getParams('com_events')
      ->get( 'footer', '' );
  }

  /////////////////////////////////////////////////////////////////////////////
  
  /**
   * Return the start day of the period
   * @todo provide a custom filter that checks the date format
   * @return DateTime or null
   */
  private function getParamPeriodStart()
  {
    $val = JFactory::getApplication()->input->get('periodStart', '', 'string');
    return $val === '' ? null : new DateTime( $val );
  }

  /////////////////////////////////////////////////////////////////////////////
  
  /**
   * Return the end day of the period
   * @todo provide a custom filter that checks the date format
   * @return DateTime or null
   */
  private function getParamPeriodEnd()
  {
    $val = JFactory::getApplication()->input->get('periodEnd', '', 'string');
    return $val === '' ? null : new DateTime( $val );
  }

  /////////////////////////////////////////////////////////////////////////////
  
  /**
   * @return DateTime
   */
  public function getPeriodStart()
  {
    $paramPeriodStart = $this->getParamPeriodStart();
    $paramPeriodEnd   = $this->getParamPeriodEnd();
    
    if ($paramPeriodStart instanceof DateTime && $paramPeriodEnd instanceof DateTime) {
      return $paramPeriodStart;
    }
    
    $paramPeriodStartOption = $this->getParamPeriodStartOption();
    
    switch ($paramPeriodStartOption)
    {
    case PeriodStartOption::CURRENT_DAY:
        $periodStart = DateTimeHelper::today();
        break;
    case PeriodStartOption::CURRENT_MONTH:
        $periodStart = DateTimeHelper::thisMonth();
        break;
    case PeriodStartOption::CURRENT_WEEK:
        $periodStart = DateTimeHelper::thisWeek();
        break;
    case PeriodStartOption::CURRENT_YEAR:
        $periodStart = DateTimeHelper::thisYear();
        break;
    case PeriodStartOption::DYNAMIC:
        $periodStart = $this->_getPeriodStart_dynamic(
          $this->getParamPeriodLength(),
          $this->getParamPeriodUnit()
        );
        break;
    default:
        throw new Exception( "Period start option not implemented: '{$paramPeriodStartOption}'" );
    }

    return $periodStart;
  }

  /////////////////////////////////////////////////////////////////////////////

  /**
   * Calculate the start of the current period dynamically.
   * 
   * For this, use the next bigger unit as a container. The unit greater than
   * year is decade.
   * Segment the container into x pieces, depending on the lenght of the period.
   * If the period is too long, use only one segment.
   * Figure out, in which segment we are now.
   * Return the beginning of the segment.
   * 
   * The last segment may be smaller than the others, so if we define a period
   * of 5 months, a visitor will see:
   * - events for five months (Jan-May)
   * - events for five months (Jun-Okt)
   * - events for two months (Nov & Dec)
   * depending on when he visits the page.
   * 
   * --------------------------------------------------------------------------
   * 
   * Example 1:
   * Length = 6, Unit = Months
   * Segment Year. 12 months, length 6 makes 2 pieces (Jan-Jun & Jul-Dez).
   * If we are in Apr, return Jan. If we are in Nov, return Jul.
   * 
   * --------------------------------------------------------------------------
   * 
   * Example 2:
   * Length = 1, Unit = Weeks
   * Segment Month. This is a bit harder, because monthes vary in length. Also,
   * should the "first" week be the one that contains [month], 1st? Or should
   * the "first" week be the first one that contains Mo-Su of [month]?
   * ISO 8601 defines the first week of the YEAR as the one which includes the
   * first Thursday of that year. Each new week of the year starts with a
   * monday.
   * Let's inspect March 2013.
   * 1st is a Friday, 31st is a Sunday. All calendar weeks relevant for March
   * are:
   * 
   * CW 09 - Mo, 25th Feb - Su, 03rd Mar
   * CW 10 - Mo, 04th Mar - Su, 10th Mar
   * CW 11 - Mo, 11th Mar - Su, 17th Mar
   * CW 12 - Mo, 18th Mar - Su, 24th Mar
   * CW 13 - Mo, 25th Mar - Su, 31st Mar
   * 
   * So, we'll go with 5 segment pieces.
   * Check in which week are currently, and return the week's first day.
   * 
   * --------------------------------------------------------------------------
   * 
   * Example 3:
   * Length = 2, Unit = Weeks
   * Segment Month. Again.
   * Let's inspect April 2013.
   * 1st is a Monday, 30st is a Tuesday. All calendar weeks relevant for April
   * are:
   * 
   * CW 14 - Mo, 01st Apr - Su, 07th Apr
   * CW 15 - Mo, 08th Apr - Su, 14th Apr
   * CW 16 - Mo, 15th Apr - Su, 21th Apr
   * CW 17 - Mo, 22th Apr - Su, 28th Apr
   * CW 18 - Mo, 29th Apr - Su, 05th May
   * 
   * Since our length is 2, our pieces are:
   * 1) Mo, 01st Apr - Su, 14th Apr
   * 2) Mo, 15th Apr - Su, 28th Apr
   * 3) Mo, 29th Apr - Tu, 30th Apr
   * 
   * So, we'll go with 3 segment pieces.
   * Check in which we are currently, and return the period's first day.
   * 
   * --------------------------------------------------------------------------
   * 
   * Example 4:
   * Length = 2, Unit = Days
   * Segment Week. 7 days, length 2 makes 3,5 pieces. Round up, use 4.
   * Segment 1: Mo & Tu
   * Segment 2: We & Th
   * Segment 3: Fr & Sa
   * Segment 4: Su
   * Check which day is today, and return the segment's first day.
   * 
   * --------------------------------------------------------------------------
   * 
   * Example 5:
   * Length = 4, Unit = Years
   * Segment Decade. 10 Years, length 4 makes 2.5 pieces. Round up, use 3.
   * We're in 2013, the current decade is from 2010-2020. Segments are from
   * 2010-2014, 2014-2018, 2018-2020.
   * Return 2010/01/01. If it was already 2014, retun 2014/01/01
   * 
   * --------------------------------------------------------------------------
   * 
   * Example 6:
   * Length = 20, Unit = Years
   * Segment Decade. 10 Years, length 20 makes 0.5 pieces. Round up, use 1.
   * We're in 2013, the current decade is from 2010-2020. Segments are from
   * 2010-2030
   * Return 2010/01/01.
   * 
   * --------------------------------------------------------------------------
   * 
   * @param int $length
   * @param int $unit valid values are PeriodUnit::DAYS, PeriodUnit::WEEKS,
   * PeriodUnit::MONTHS and PeriodUnit::YEARS
   */
  private function _getPeriodStart_dynamic( $length, $unit )
  {
    $containerMap = array(
      PeriodUnit::DAYS    => PeriodUnit::WEEKS,
      PeriodUnit::WEEKS   => PeriodUnit::MONTHS,
      PeriodUnit::MONTHS  => PeriodUnit::YEARS,
      PeriodUnit::YEARS   => PeriodUnit::DECADES,
    );
    
    $now = time();
    $container = $containerMap[$unit];
    $segmentStart = $this->getContainerStartDate( $container );
    
    do
    {
      $periodStart = clone $segmentStart;
      
      switch ($unit)
      {
      case PeriodUnit::DAYS:
          DateTimeHelper::addDays( $segmentStart, $length );
          break;
      case PeriodUnit::WEEKS:
          DateTimeHelper::addWeeks( $segmentStart, $length );
          break;
      case PeriodUnit::MONTHS:
          DateTimeHelper::addMonths( $segmentStart, $length );
          break;
      case PeriodUnit::YEARS:
          DateTimeHelper::addYears( $segmentStart, $length );
          break;
      }
      
    } while (DateTimeHelper::toTimestamp($segmentStart) < $now);
    
    return $periodStart;
  }
  
  /////////////////////////////////////////////////////////////////////////////

  /**
   * @param int $container PeriodUnit constant
   * @return DateTime
   */
  private function getContainerStartDate( $container )
  {
    switch ($container)
    {
    case PeriodUnit::DAYS:
        $startDate = DateTimeHelper::today();
        break;
    case PeriodUnit::WEEKS:
        $startDate = DateTimeHelper::thisWeek();
        break;
    case PeriodUnit::MONTHS:
        $tsCurrentMonth = DateTimeHelper::timestampThisMonth();
        $calendarWeek = DateTimeHelper::weekOfTheYear( $tsCurrentMonth );
        $tsCalendarWeek = DateTimeHelper::timestampCalendarWeek( $calendarWeek );
        $startDate = DateTimeHelper::fromTimestamp( $tsCalendarWeek );
        break;
    case PeriodUnit::YEARS:
        $startDate = DateTimeHelper::thisYear();
        break;
    case PeriodUnit::DECADES:
        $startDate = DateTimeHelper::thisDecade();
        break;
    }
    
    return $startDate;
  }
  
  /////////////////////////////////////////////////////////////////////////////
  
  /**
   * @return DateTime
   */
  public function getPeriodEnd()
  {
    $paramPeriodStart = $this->getParamPeriodStart();
    $paramPeriodEnd   = $this->getParamPeriodEnd();
    
    if ($paramPeriodStart instanceof DateTime && $paramPeriodEnd instanceof DateTime) {
      return $paramPeriodStart;
    }
    
    $periodStart  = $this->getPeriodStart();
    $periodEnd    = clone $periodStart;
    
    $paramPeriodLength  = $this->getParamPeriodLength();
    $paramPeriodUnit    = $this->getParamPeriodUnit();
    
    switch ($paramPeriodUnit)
    {
    case PeriodUnit::DAYS:
        $periodEnd = DateTimeHelper::addDays( $periodEnd, $paramPeriodLength );
        break;
    case PeriodUnit::MONTHS:
        $periodEnd = DateTimeHelper::addMonths( $periodEnd, $paramPeriodLength );
        break;
    case PeriodUnit::WEEKS:
        $periodEnd = DateTimeHelper::addWeeks( $periodEnd, $paramPeriodLength );
        break;
    case PeriodUnit::YEARS:
        $periodEnd = DateTimeHelper::addYears( $periodEnd, $paramPeriodLength );
        break;
    }
    
    return $periodEnd;
  }

  /////////////////////////////////////////////////////////////////////////////
  
  /**
   * @return bool
   */
  public function showLinkICalendarHelp()
  {
    return $this->getParamICalHelpArticle() !== '';
  }

  /////////////////////////////////////////////////////////////////////////////
  
  // @TODO return findEvent( event.date < periodStart ) instanceof Event;
  public function showLinkEarlier()
  {
    // we only show links 5 years into the past
    $sentinel     = DateTimeHelper::substractYears( new DateTime(), 5 );
    $periodStart  = $this->getPeriodStart();

    return (int)$sentinel->format('U') < (int)$periodStart->format('U');
  }

  /////////////////////////////////////////////////////////////////////////////
  
  public function getLinkEarlier()
  {
    $earlier = DateTimeHelper::substractMonths( $this->getPeriodStart(), $this->getPeriodLength() );
    $strNewStart = $earlier->format( DateTimeHelper::ISO_DATE_FORMAT );
    $strPeriodLength = $this->getPeriodLength();
    return JRoute::_( "index.php?view=default&periodStart={$strNewStart}&periodLength={$strPeriodLength}" );
  }

  /////////////////////////////////////////////////////////////////////////////
  
  // @TODO return findEvent( event.date > periodEnd ) instanceof Event;
  public function showLinkLater()
  {
    // we only show links 5 years into the future
    $sentinel     = DateTimeHelper::addYears( new DateTime(), 5 );
    $periodStart  = $this->getPeriodStart();

    return (int)$sentinel->format('U') > (int)$periodStart->format('U');
  }

  /////////////////////////////////////////////////////////////////////////////
  
  public function getLinkLater()
  {
    $later = $this->getPeriodEnd();
    $strNewStart = $later->format( DateTimeHelper::ISO_DATE_FORMAT );
    $strPeriodLength = $this->getPeriodLength();
    return JRoute::_( "index.php?view=default&periodStart={$strNewStart}&periodLength={$strPeriodLength}" );
  }

  /////////////////////////////////////////////////////////////////////////////
  
  public function getLinkPrint()
  {
    return ''; //http://localhost/hvtv/jupgrade/presse-stimmen/103-lorem-ipsum-test-artikel?tmpl=component&print=1&layout=default&page=
  }

  /////////////////////////////////////////////////////////////////////////////
  
  public function getLinkCalendar()
  {
    return JRoute::_( "index.php?view=calendar" );
  }

  /////////////////////////////////////////////////////////////////////////////
  
  /**
   * @return string
   */
  public function getLinkICalendar()
  {
    return JRoute::_( "index.php?view=events&format=ical" );
  }

  /////////////////////////////////////////////////////////////////////////////
  
  public function getHeader() {
    return $this->getParamHeader();
  }

  /////////////////////////////////////////////////////////////////////////////
  
  public function getFooter() {
    return $this->getParamFooter();
  }

  /////////////////////////////////////////////////////////////////////////////
  
  public function showPrintAction() {
    return $this->getParamShowPrintAction();
  }

  /////////////////////////////////////////////////////////////////////////////
  
  public function getEventsForCurrentDay()
  {
    $from   = DateTimeHelper::today();
    $to     = DateTimeHelper::tomorrow();
    $catid  = $this->getCatId();
    return $this->getEvents( $from, $to, $catid );
  }

  /////////////////////////////////////////////////////////////////////////////
  
  public function getEventsForCurrentWeek()
  {
    $from   = DateTimeHelper::thisWeek();
    $to     = DateTimeHelper::nextWeek();
    $catid  = $this->getCatId();
    return $this->getEvents( $from, $to, $catid );
  }

  /////////////////////////////////////////////////////////////////////////////
  
  public function getEventsForCurrentMonth()
  {
    $from   = DateTimeHelper::thisMonth();
    $to     = DateTimeHelper::nextMonth();
    $catid  = $this->getCatId();
    return $this->getEvents( $from, $to, $catid );
  }

  /////////////////////////////////////////////////////////////////////////////
  
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

    $catid  = $this->getCatId();
    
    return $this->getEvents( $from, DateTimeHelper::addMonths(clone $from,3), $catid );
  }

  /////////////////////////////////////////////////////////////////////////////
  
  public function getEventsForCurrentHalfYear()
  {
    $from = null;
    $currentMonth = (int)date('n');

    // First half
    if ($currentMonth <= 6)
    {
      $from = DateTimeHelper::thisYear();
    }
    // Second half
    else
    {
      $from = DateTimeHelper::addMonths( DateTimeHelper::thisYear(), 6 );
    }

    $catid  = $this->getCatId();
    
    return $this->getEvents( $from, DateTimeHelper::addMonths(clone $from,6), $catid );
  }

  /////////////////////////////////////////////////////////////////////////////
  
  public function getEventsForCurrentYear()
  {
    $from   = DateTimeHelper::thisYear();
    $to     = DateTimeHelper::nextYear();
    $catid  = $this->getCatId();
    return $this->getEvents( $from, $to, $catid );
  }

  /////////////////////////////////////////////////////////////////////////////
  
  public function getEventsForCurrentPeriod()
  {
    $from   = $this->getPeriodStart();
    $to     = $this->getPeriodEnd();
    $catid  = $this->getCatId();
    return $this->getEvents( $from, $to, $catid );
  }

  /////////////////////////////////////////////////////////////////////////////
  
  /**
   * @param DateTime $from
   * @param DateTime $to
   * @param int $catid defaults to 0, meaning "all categories" or "don't restrict to category"
   * @return array
   */
  public function getEvents( DateTime $from, DateTime $to, $catid=0 )
  {
    $strFrom  = $from->format( self::MYSQL_FORMAT );
    $strTo    = $to->format( self::MYSQL_FORMAT );

    /* @var $query JDatabaseQuery */

    $db     = $this->getDbo();
    $query  = $db->getQuery(true);
    $query->select( 'e.*' );
    $query->from('#__events_event as e');
    $query->where("time_start >= '{$strFrom}'");
    $query->where("time_start < '{$strTo}'");
    $query->order('time_start');
    
    if (is_int($catid) && $catid > 0) {
      $query->where("catid = $catid");
    }

    $db->setQuery((string)$query);
    if (!$db->query())
    {
      JError::raiseError(500, $db->getErrorMsg());
    }

    return $db->loadObjectList();
  }

  /////////////////////////////////////////////////////////////////////////////
  

}



///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
  

