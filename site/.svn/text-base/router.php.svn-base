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

  const RE_SEO_DATE = '/^(?P<day>\d{2})-(?P<month>\d{2})-(?P<year>\d{4})$/';

  /**
   * @param	array	A named array
   * @return	array (
   *   'view'         => default,
   *   'periodStart'  => yyyy-mm-dd,
   *   'periodLength' => int,         // period length in months
   *   'option'       => com_events,  // component name
   *   'ItemId'       => int,         // menu item id
   * )
   */
  public static function buildRoute( &$query )
  {
    $segments = array();

    if (isset($query['view']))
    {
      if ($query['view'] === 'calendar')
      {
        $segments[] = 'kalender';
      }

      if ($query['view'] === 'events' && isset($query['format']) && $query['format'] === 'json')
      {
        $segments[] = 'json';
        unset( $query['format'] );
      }

      unset( $query['view'] );
    }

    if (isset($query['periodStart']) && isset($query['periodLength']))
    {
      $dateStart  = new DateTime( $query['periodStart'] );
      $dateEnd    = DateTimeHelper::addMonths( clone $dateStart, $query['periodLength'] );

      // substract one day (hide the fact that we're processing from
      // 00:00 - 00:00, excluding the last day. Make it look like we're
      // including both dates, from 00:00 - 24:00).
      $dateEnd = DateTimeHelper::substractDays( $dateEnd, 1 );

      // encode as YYYYMMDD-bis-YYYYMMDD
      $segments[] =
        $dateStart->format( self::SEO_DATE_FORMAT ) .
        '-bis-' .
        $dateEnd->format( self::SEO_DATE_FORMAT );

      unset( $query['periodStart'], $query['periodLength'] );
    }

    return $segments;
  }
  
  /**
   * @param	array	A named array
   * @param	array
   *
   * Recognized formats:
   * /YYYYMMDD-bis-YYYYMMDD
   * /view/YYYYMMDD-bis-YYYYMMDD
   *
   * Damn Joomla core modifies my created routes (related to slug concept, but
   * we don't use IDs here). The first '-' will always be replaced by ':'...
   */
  public static function parseRout( $segments )
  {
    $vars = array();

    // Fix joomla behavior we don't want here
		$total = count($segments);
		for ($i=0; $i<$total; $i++)  {
			$segments[$i] = preg_replace( '/:/', '-', $segments[$i], 1 );
		}

    // remove trailing query parameters
    $lastIndex = count($segments)-1;
    $lastSegment = $segments[$lastIndex];
    $aParts = explode('&', $lastSegment);
    $segments[$lastIndex] = $aParts[0];
    for ($i=1; $i<count($aParts); $i++)
    {
      list($key, $value) = explode('=', $aParts[$i]);
      $vars[$key] = $value;
    }

    if (count($segments) === 1)
    {
      if ($segments[0] === 'kalender')
      {
        $vars['view'] = 'calendar';
      }
      else if ($segments[0] === 'json')
      {
        $vars['view'] = 'events';
        $vars['format'] = 'json';
      }
      else
      {
        $period = array_shift( $segments );
        $aParts = explode('-', $period);

        if (count($aParts) === 7)
        {
          $periodStart  = implode( '-', array($aParts[0],$aParts[1],$aParts[2]) );
          $periodEnd    = implode( '-', array($aParts[4],$aParts[5],$aParts[6]) );

          preg_match( self::RE_SEO_DATE, $periodStart, $aParts );
          $dateStart = new DateTime( "{$aParts['year']}-{$aParts['month']}-{$aParts['day']}" );

          preg_match( self::RE_SEO_DATE, $periodEnd, $aParts );
          $dateEnd = new DateTime( "{$aParts['year']}-{$aParts['month']}-{$aParts['day']}" );

          // add one day (correction from create route).
          $dateEnd = DateTimeHelper::addDays( $dateEnd, 1 );

          $vars['periodStart']  = $dateStart->format( self::ISO_DATE_FORMAT );

          $yearStart  = (int)$dateStart->format( 'Y' );
          $monthStart = (int)$dateStart->format( 'n' );
          $yearEnd    = (int)$dateEnd->format( 'Y' );
          $monthEnd   = (int)$dateEnd->format( 'n' );

          $vars['periodLength'] = 12*($yearEnd-$yearStart) + ($monthEnd-$monthStart);
        }
      }
    }

    return $vars;
  }
  
}

function EventsBuildRoute(&$query)
{
  return ComEventsRouter::buildRoute( $query );
}

function EventsParseRoute( $segments )
{
  return ComEventsRouter::parseRout( $segments );
}
