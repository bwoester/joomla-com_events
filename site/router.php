<?php
/**
 * @version     1.0.0
 * @package     com_events
 * @copyright   Copyright (C) 2011. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Created by com_combuilder - http://www.notwebdesign.com
 */

JLoader::import( 'components.com_events.helpers.dateTimeHelper', JPATH_BASE );

class ComEventsRouter
{
  const SEO_DATE_FORMAT = 'd-m-Y';
  const ISO_DATE_FORMAT = 'Y-m-d';
  const ISO_DATE_FORMAT_STRFTIME = '%Y-%m-%d';

  const RE_SEO_DATE = '/^(?P<day>\d{2})-(?P<month>\d{2})-(?P<year>\d{4})$/';
  
  const VIEW_CALENDAR = 'calendar';
  const VIEW_EVENTS   = 'events';

  const FORMAT_HTML = 'html';
  const FORMAT_ICAL = 'ical';
  const FORMAT_JSON = 'json';
  
  const DEFAULT_VIEW    = 'events';
  const DEFAULT_FORMAT  = 'html';
  
  /////////////////////////////////////////////////////////////////////////////
  
  /**
   * Formats URL to: [/view][/YYYY-MM-DD-bis-YYYY-MM-DD][/format=json]
   * - view is left out if it is the default view (events)
   * - period is left out if neigther periodStart nor periodEnd are given
   * - period is left out if view is 'calendar'
   * - format is left out if it is the default format (html)
   * - format is left out if it is the view is 'calendar'
   * 
   * Some examples:
   * 
   * ?option=com_events&view=events&periodStart=&periodEnd=&Itemid=509
   * => /
   * 
   * ?option=com_events&view=events&periodStart=2013-03-22&periodEnd=2013-04-01&Itemid=509&format=json
   * => /22-03-2013-bis-01-04-2013/format=json
   * 
   * ?option=com_events&view=calendar&periodStart=&periodEnd=&Itemid=509
   * => /kalender
   * 
   * @param	array	A named array
   * @return	array (
   *   'option'       => com_events,  // component name
   *   'view'         => default,     // view name
   *   'periodStart'  => yyyy-mm-dd,
   *   'periodEnd'    => yyyy-mm-dd,
   *   'ItemId'       => int,         // menu item id
   * )
   */
  public static function buildRoute( &$query )
  {
    $segments = array(
      'view'    => '',
      'period'  => '',
      'format'  => '',
    );

    self::ensureView( $query );
    self::ensureFormat( $query );
    self::ensurePeriodStart( $query );
    self::ensurePeriodEnd( $query );
    
    $view         = $query['view'];
    $format       = $query['format'];
    $periodStart  = $query['periodStart'];
    $periodEnd    = $query['periodEnd'];
    
    unset( $query['view'], $query['format'], $query['periodStart'], $query['periodEnd'] );

    $segments['period'] = self::constructPeriod( $periodStart, $periodEnd );
    
    switch ($format)
    {
    case self::DEFAULT_FORMAT:
        // this is our default format, it's optional in the url
        break;
    default:
        // all other formats go in as the last path segment
        $segments['format'] = "format={$format}";
        break;
    }
    
    switch ($view)
    {
    case self::VIEW_CALENDAR:
        // @todo use translations? how to parse the route?
        // translate to german
        $segments['view'] = 'kalender';
        break;
    case self::DEFAULT_VIEW:
        // this is our default view, it's optional in the url
        break;
    default:
      // all other views go in as the first path segment
        $segments['view'] = $view;
        break;
    }
    
    // view calendar does not support formats or periods
    if ($view === self::VIEW_CALENDAR)
    {
      $segments['period'] = '';
      $segments['format'] = '';
    }
    
    // build up the segments array in the correct order, filter out all empty 
    // elements (false, null, '' <-- we use those empty strings), then return it.
    return array_filter(array(
      $segments['view'],
      $segments['period'],
      $segments['format'],
    ));
  }
  
  /////////////////////////////////////////////////////////////////////////////
  
  /**
   * @param	array	A named array
   * @param	array
   * 
   * Parses URLs of format: [/view][/YYYY-MM-DD-bis-YYYY-MM-DD][/format=json]
   * 
   * Some examples:
   * 
   * /
   * => ?view=events&periodStart=&periodEnd=
   * 
   * /kalender
   * => ?view=calendar
   * 
   * /format=json
   * => ?view=events&periodStart=&periodEnd=&format=json
   * 
   * /events/format=ical
   * => ?view=events&periodStart=&periodEnd=&format=ical
   * 
   * /22-03-2013-bis-01-04-2013
   * => ?view=events&periodStart=2013-03-22&periodEnd=2013-04-01
   * 
   * /22-03-2013-bis-01-04-2013/format=json
   * => ?view=events&periodStart=2013-03-22&periodEnd=2013-04-01&format=json
   * 
   * - default view is 'events'
   * - period is optional. If not given, the component will use the menu's params
   * - default format is html
   */
  public static function parseRout( $segments )
  {
    $query = array(
      'view'        => self::DEFAULT_VIEW,
      'periodStart' => '',
      'periodEnd'   => '',
      'format'      => self::DEFAULT_FORMAT,
    );   

    // Fix joomla behavior we don't want here
    self::fixSegments( $segments );

    foreach ($segments as $segment)
    {
      // we found a format segment!
      if (strpos($segment,'format=') !== false)
      {
        $aFormatPieces = explode( '=', $segment );
        array_shift( $aFormatPieces );
        $query['format'] = implode( '=', $aFormatPieces );
        continue;
      }
      
      // we found a period segment!
      $aPeriodParts = explode( '-', $segment );
      if (count($aPeriodParts) === 7)
      {
        // example: 22-03-2013-bis-01-04-2013
        // meaning: dd mm yyyy  -  dd mm yyyy
        // index..:  0  1    2   3  4  5    6
        $dateStart  = new DateTime( "{$aPeriodParts[2]}-{$aPeriodParts[1]}-{$aPeriodParts[0]}" );
        $dateEnd    = new DateTime( "{$aPeriodParts[6]}-{$aPeriodParts[5]}-{$aPeriodParts[4]}" );
        
        // add one day (correction from create route).
        $dateEnd = DateTimeHelper::addDays( $dateEnd, 1 );
        
        $query['periodStart'] = $dateStart->format( self::ISO_DATE_FORMAT );
        $query['periodEnd']   = $dateEnd->format( self::ISO_DATE_FORMAT );
        
        continue;
      }
      
      // if we make it till here, we found a view segment!
      switch ($segment)
      {
      case 'kalender':
          $query['view'] = self::VIEW_CALENDAR;
          break;
      default:
          $query['view'] = $segment;
          break;
      }
    }

    // view calendar does not support formats or periods
    if ($query['view'] === self::VIEW_CALENDAR) {
      unset( $query['periodStart'], $query['periodEnd'], $query['format'] );
    }
    
    // format html is joomla default
    if ($query['format'] === self::FORMAT_HTML) {
      unset( $query['format'] );
    }
    
    return $query;
  }
  
  /////////////////////////////////////////////////////////////////////////////
  
  /**
   * Joomla core modifies my created routes (related to slug concept, but
   * we don't use IDs here). The first '-' in a segment will always be replaced
   * by a ':'...
   */
  static private function fixSegments( &$segments )
  {
		for ($i=0; $i<count($segments); $i++)  {
			$segments[$i] = preg_replace( '/:/', '-', $segments[$i], 1 );
		}    
  }
  
  /////////////////////////////////////////////////////////////////////////////
  
  static private function constructPeriod( $periodStart, $periodEnd )
  {
    if ($periodStart === '' || $periodEnd === '') {
      return '';
    }

    $dateStart  = new DateTime( $periodStart );
    $dateEnd    = new DateTime( $periodEnd );

    // substract one day (hide the fact that we're processing from
    // 00:00 - 00:00, excluding the last day. Make it look like we're
    // including both dates, from 00:00 - 24:00).
    // 
    // Example:
    // We want to show all events for January.
    // This is the range: 2000/01/01 00:00 till 2000/02/01 00:00
    // We display it as:  2000/01/01 - 2000/01/31
    $dateEnd = DateTimeHelper::substractDays( $dateEnd, 1 );

    // encode as YYYYMMDD-bis-YYYYMMDD
    return $dateStart->format( self::SEO_DATE_FORMAT )
      . '-bis-'
      . $dateEnd->format( self::SEO_DATE_FORMAT );
  }
  
  /////////////////////////////////////////////////////////////////////////////
  
  private static function ensureView( &$query )
  {
    if (!isset($query['view'])) {
      $query['view'] = self::DEFAULT_VIEW;
    }
  }
  
  /////////////////////////////////////////////////////////////////////////////
  
  private static function ensureFormat( &$query )
  {
    if (!isset($query['format'])) {
      $query['format'] = self::DEFAULT_FORMAT;
    }
  }

  /////////////////////////////////////////////////////////////////////////////
  
  private static function ensurePeriodStart( &$query )
  {
    // if it is set, validate value. Unset if value is not recognized.
    if (isset($query['periodStart']))
    {
      $result = strptime( $query['periodStart'], self::ISO_DATE_FORMAT_STRFTIME );
      
      if ($result === false) {
        unset( $query['periodStart'] );
      }
    }
    
    // if it is not set, set it to its default value
    if (!isset($query['periodStart'])) {
      $query['periodStart'] = '';
    }
  }
  
  /////////////////////////////////////////////////////////////////////////////
  
  private static function ensurePeriodEnd( &$query )
  {
    // if it is set, validate value. Unset if value is not recognized.
    if (isset($query['periodEnd']))
    {
      $result = strptime( $query['periodEnd'], self::ISO_DATE_FORMAT_STRFTIME );
      
      if ($result === false) {
        unset( $query['periodEnd'] );
      }
    }
    
    // if it is not set, set it to its default value
    if (!isset($query['periodEnd']))
    {
      $query['periodEnd'] = '';
    }
  }
  
  /////////////////////////////////////////////////////////////////////////////
  
}



///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////



function EventsBuildRoute(&$query)
{
  return ComEventsRouter::buildRoute( $query );
}

///////////////////////////////////////////////////////////////////////////////

function EventsParseRoute( $segments )
{
  return ComEventsRouter::parseRout( $segments );
}

///////////////////////////////////////////////////////////////////////////////
