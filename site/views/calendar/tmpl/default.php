<?php
/**
 * @version     1.0.0
 * @package     com_events
 * @copyright   Copyright (C) 2011. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Created by com_combuilder - http://www.notwebdesign.com
 */

// no direct access
defined('_JEXEC') or die;

/* Some help for the IDE */
/* @var $this EventsViewCalendar */
/* @var $calendarModel EventsModelCalendar */

$calendarModel = $this->calendarModel;

JHtml::_( 'stylesheet', 'com_events/fullCalendar/fullcalendar.css', array(), true );
// TODO
JHtml::_( 'stylesheet', 'com_events/fullCalendar/fullcalendar.print.css', array(), true );

JHtml::_( 'script', 'com_events/qTip/jquery.qtip-1.0.0-rc3.min.js', false, true );
JHtml::_( 'script', 'com_events/fullCalendar/fullcalendar.min.js', false, true );

?>

<div class="calendar"></div>

<?php
$cancelledLabel = JText::_('COM_EVENTS_EVENT_CANCELLED_LABEL');;
$cancelledDesc  = JText::_('COM_EVENTS_EVENT_CANCELLED_DESC');
$spanCancelled = "<span class=\"label label-cancelled\" title=\"{$cancelledDesc}\">{$cancelledLabel}</span>";

$doc = JFactory::getDocument();
$doc->addScriptDeclaration('
jQuery(document).ready(function() {

  var localOptions = {
    buttonText: {
        today: "Heute",
        month: "Monat",
        day: "Tag",
        week: "Woche"
    },
    monthNames: [ "Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember" ],
    monthNamesShort: [ "Jan", "Feb", "Mär", "Apr", "Mai", "Jun", "Jul", "Aug", "Sept", "Okt", "Nov", "Dez" ],
    dayNames: [ "Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag" ],
    dayNamesShort: [ "So", "Mo", "Di", "Mi", "Do", "Fr", "Sa" ]
  };

  jQuery("div.calendar").fullCalendar($.extend({
    firstDay: 1,
    events: "'.$calendarModel->getLinkEvents().'",
    eventRender: function( event, element )
    {
      var spanCancelled = \''.$spanCancelled.'\';
      
      if (event.cancelled) {
        element.find(".fc-event-inner").append( spanCancelled );
      }
      
      element.find(".fc-event-title").qtip({
        content: event.tooltip,
        style: {
          tip: "topLeft"
        }
      });      
    }
  }, localOptions));

});
');
?>
