<?php



class DateTimeHelper
{
  const ICAL_DATE_TIME_FORMAT = 'Ymd\THis\Z';
  const ISO_FORMAT = 'Y-m-d H:i:s';
  const ISO_DATE_FORMAT = 'Y-m-d';
  const GERMAN_FORMAT = 'd.m.Y, H:i';

  static public function secondsOneDay()
  {
    return 24*60*60;
  }

  static public function secondsOneWeek()
  {
    return 7*24*60*60;
  }

  /**
   * @return int in range 1 (monday) - 7 (sunday)
   */
  static public function dayOfTheWeek()
  {
    return date('N');
  }

  /**
   * @return int in range 1 - 53 (in rare cases, a year has 53 calendar weeks)
   */
  static public function weekOfTheYear( $timestamp=null )
  {
    return date('W',$timestamp === null ? time() : $timestamp );
  }

  static public function timestampYesterday()
  {
    return self::timestampToday() - self::secondsOneDay();
  }

  static public function timestampToday()
  {
    return mktime(0, 0, 0, date('n'), date('j'), date('Y'));
  }

  static public function timestampTomorrow()
  {
    return self::timestampToday() + self::secondsOneDay();
  }

  static public function timestampLastWeek()
  {
    return self::timestampThisWeek() - self::secondsOneWeek();
  }

  static public function timestampThisWeek()
  {
    return strtotime( 'this week' );
  }

  static public function timestampNextWeek()
  {
    return self::timestampThisWeek() + self::secondsOneWeek();
  }

  static public function timestampLastMonth()
  {
    return strtotime( '-1 month', self::timestampThisMonth() );
  }

  static public function timestampThisMonth()
  {
    return mktime(0, 0, 0, date('m'), 1, date('Y'));
  }

  static public function timestampNextMonth()
  {
    return strtotime( '+1 month', self::timestampThisMonth() );
  }

  static public function timestampLastYear()
  {
    return mktime(0, 0, 0, 1, 1, date('Y')-1);
  }

  static public function timestampThisYear()
  {
    return mktime(0, 0, 0, 1, 1, date('Y'));
  }

  static public function timestampNextYear()
  {
    return mktime(0, 0, 0, 1, 1, date('Y')+1);
  }
  
  static public function timestampLastDecade()
  {
    $currentYear = date('Y');
    $decade = (floor($currentYear / 10) - 1) * 10;
    return mktime(0, 0, 0, 1, 1, $decade);
  }

  static public function timestampThisDecade()
  {
    $currentYear = date('Y');
    $decade = floor($currentYear / 10) * 10;
    return mktime(0, 0, 0, 1, 1, $decade);
  }

  static public function timestampNextDecade()
  {
    $currentYear = date('Y');
    $decade = (floor($currentYear / 10) + 1) * 10;
    return mktime(0, 0, 0, 1, 1, $decade);
  }
  
  /**
   * Returns the timestamp of the beginning (Monday 00:00) of the given
   * calendar week.
   * 
   * If you want to get Tuesday 00:00, specify $day = 2.
   * For Wednesday 00:00, specify $day = 3.
   * ...
   * Sunday 00:00, specify $day = 7.
   * 
   * @param int $calendarWeek
   * @param int $year four digits!
   * @param int $day four digits!
   * @return int
   */
  static public function timestampCalendarWeek( $calendarWeek, $year=null, $day=1 )
  {
    if ($year === null) {
      $year = date('Y');
    }
    
    $calendarWeek = str_pad( $calendarWeek, 2, 0, STR_PAD_LEFT );
    
    return strtotime("{$year}W{$calendarWeek}{$day}");
  }

  static public function fromTimestamp( $timestamp )
  {
    $iso = date( self::ISO_FORMAT, $timestamp );
    return new DateTime( $iso );
  }

  static public function toTimestamp( DateTime $obj )
  {
    return (int)$obj->format('U');
  }

  static public function yesterday()
  {
    $iso = date( self::ISO_FORMAT, self::timestampYesterday() );
    return new DateTime( $iso );
  }

  static public function today()
  {
    $iso = date( self::ISO_FORMAT, self::timestampToday() );
    return new DateTime( $iso );
  }

  static public function tomorrow()
  {
    $iso = date( self::ISO_FORMAT, self::timestampTomorrow() );
    return new DateTime( $iso );
  }

  static public function lastWeek()
  {
    $iso = date( self::ISO_FORMAT, self::timestampLastWeek() );
    return new DateTime( $iso );
  }

  static public function thisWeek()
  {
    $iso = date( self::ISO_FORMAT, self::timestampThisWeek() );
    return new DateTime( $iso );
  }

  static public function nextWeek()
  {
    $iso = date( self::ISO_FORMAT, self::timestampNextWeek() );
    return new DateTime( $iso );
  }

  static public function lastMonth()
  {
    $iso = date( self::ISO_FORMAT, self::timestampLastMonth() );
    return new DateTime( $iso );
  }

  static public function thisMonth()
  {
    $iso = date( self::ISO_FORMAT, self::timestampThisMonth() );
    return new DateTime( $iso );
  }

  static public function nextMonth()
  {
    $iso = date( self::ISO_FORMAT, self::timestampNextMonth() );
    return new DateTime( $iso );
  }

  static public function lastYear()
  {
    $iso = date( self::ISO_FORMAT, self::timestampLastYear() );
    return new DateTime( $iso );
  }

  static public function thisYear()
  {
    $iso = date( self::ISO_FORMAT, self::timestampThisYear() );
    return new DateTime( $iso );
  }

  static public function nextYear()
  {
    $iso = date( self::ISO_FORMAT, self::timestampNextYear() );
    return new DateTime( $iso );
  }

  static public function lastDecade()
  {
    $iso = date( self::ISO_FORMAT, self::timestampLastDecade() );
    return new DateTime( $iso );
  }

  static public function thisDecade()
  {
    $iso = date( self::ISO_FORMAT, self::timestampThisDecade() );
    return new DateTime( $iso );
  }

  static public function nextDecade()
  {
    $iso = date( self::ISO_FORMAT, self::timestampNextDecade() );
    return new DateTime( $iso );
  }

  static public function addSeconds( DateTime $dateTime, $numberOfSeconds=1 )
  {
    $dateTime->modify("+{$numberOfSeconds} seconds");
    return $dateTime;
  }

  static public function addMinutes( DateTime $dateTime, $numberOfMinutes=1 )
  {
    $dateTime->modify("+{$numberOfMinutes} minutes");
    return $dateTime;
  }

  static public function addHours( DateTime $dateTime, $numberOfHours=1 )
  {
    $dateTime->modify("+{$numberOfHours} hours");
    return $dateTime;
  }

  static public function addDays( DateTime $dateTime, $numberOfDays=1 )
  {
    $dateTime->modify("+{$numberOfDays} days");
    return $dateTime;
  }

  static public function addWeeks( DateTime $dateTime, $numberOfWeeks=1 )
  {
    $dateTime->modify("+{$numberOfWeeks} weeks");
    return $dateTime;
  }

  static public function addMonths( DateTime $dateTime, $numberOfMonths=1 )
  {
    $dateTime->modify("+{$numberOfMonths} months");
    return $dateTime;
  }

  static public function addYears( DateTime $dateTime, $numberOfYears=1 )
  {
    $dateTime->modify("+{$numberOfYears} years");
    return $dateTime;
  }

  static public function addDecades( DateTime $dateTime, $numberOfDecades=1 )
  {
    $numberOfYears = 10 * $numberOfDecades;
    $dateTime->modify("+{$numberOfYears} years");
    return $dateTime;
  }

  static public function substractSeconds( DateTime $dateTime, $numberOfSeconds=1 )
  {
    $dateTime->modify("-{$numberOfSeconds} seconds");
    return $dateTime;
  }

  static public function substractMinutes( DateTime $dateTime, $numberOfMinutes=1 )
  {
    $dateTime->modify("-{$numberOfMinutes} minutes");
    return $dateTime;
  }

  static public function substractHours( DateTime $dateTime, $numberOfHours=1 )
  {
    $dateTime->modify("-{$numberOfHours} hours");
    return $dateTime;
  }

  static public function substractDays( DateTime $dateTime, $numberOfDays=1 )
  {
    $dateTime->modify("-{$numberOfDays} days");
    return $dateTime;
  }

  static public function substractWeeks( DateTime $dateTime, $numberOfWeeks=1 )
  {
    $dateTime->modify("-{$numberOfWeeks} weeks");
    return $dateTime;
  }

  static public function substractMonths( DateTime $dateTime, $numberOfMonths=1 )
  {
    $dateTime->modify("-{$numberOfMonths} months");
    return $dateTime;
  }

  static public function substractYears( DateTime $dateTime, $numberOfYears=1 )
  {
    $dateTime->modify("-{$numberOfYears} years");
    return $dateTime;
  }

  static public function substractDecades( DateTime $dateTime, $numberOfDecades=1 )
  {
    $numberOfYears = 10 * $numberOfDecades;
    $dateTime->modify("-{$numberOfYears} years");
    return $dateTime;
  }

}
