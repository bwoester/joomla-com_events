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

  static public function dayOfTheWeek()
  {
    return date('d');
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
    // dayOfTheWeek := Monday (1)   => today - 0
    // dayOfTheWeek := Tuesday (2)  => today - 1*secondsOneDay
    // ...
    // return self::timestampToday() - ((self::dayOfTheWeek()-1) * self::secondsOneDay());
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

  static public function fromTimestamp( $timestamp )
  {
    $iso = date( self::ISO_FORMAT, $timestamp );
    return new DateTime( $iso );
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

}
