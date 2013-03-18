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

/**
 * Behavior helper.
 */
class ComEventsBehavior
{

  /**
   * Add unobtrusive javascript support for a detetime control.
   *
   * @return  void
   */
  public static function datetime()
  {
    static $loaded = false;

    // Only load once
    if ($loaded) {
      return;
    }

    $document = JFactory::getDocument();
    $tag      = JFactory::getLanguage()->getTag();

    //Add uncompressed versions when debug is enabled
//    $uncompressed  = JFactory::getConfig()->get('debug') ? '-uncompressed' : '';
    JHtml::_( 'stylesheet', 'com_events/anytime/anytime.css', array(), true );
    JHtml::_( 'script', 'com_events/anytime/anytime.js', false, true );

//    $translation = self::_dateTimeTranslation();
//    if ($translation) {
//      $document->addScriptDeclaration($translation);
//    }
    
    $loaded = true;
  }

  /**
   * TODO: copied from joomla as an example. Not yet modified to translate the
   *       anytime calendar.
   * 
   * Internal method to translate the JavaScript Calendar
   *
   * @return  string  JavaScript that translates the object
   */
  protected static function _dateTimeTranslation()
  {
    static $jsscript = 0;

    if ($jsscript == 0)
    {
      $return =
        'Calendar._DN = new Array ("'
        .JText::_('SUNDAY', true).'", "'
        .JText::_('MONDAY', true).'", "'
        .JText::_('TUESDAY', true).'", "'
        .JText::_('WEDNESDAY', true).'", "'
        .JText::_('THURSDAY', true).'", "'
        .JText::_('FRIDAY', true).'", "'
        .JText::_('SATURDAY', true).'", "'
        .JText::_('SUNDAY', true).'");'
        .' Calendar._SDN = new Array ("'
        .JText::_('SUN', true).'", "'
        .JText::_('MON', true).'", "'
        .JText::_('TUE', true).'", "'
        .JText::_('WED', true).'", "'
        .JText::_('THU', true).'", "'
        .JText::_('FRI', true).'", "'
        .JText::_('SAT', true).'", "'
        .JText::_('SUN', true).'");'
        .' Calendar._FD = 0;'
        .' Calendar._MN = new Array ("'
        .JText::_('JANUARY', true).'", "'
        .JText::_('FEBRUARY', true).'", "'
        .JText::_('MARCH', true).'", "'
        .JText::_('APRIL', true).'", "'
        .JText::_('MAY', true).'", "'
        .JText::_('JUNE', true).'", "'
        .JText::_('JULY', true).'", "'
        .JText::_('AUGUST', true).'", "'
        .JText::_('SEPTEMBER', true).'", "'
        .JText::_('OCTOBER', true).'", "'
        .JText::_('NOVEMBER', true).'", "'
        .JText::_('DECEMBER', true).'");'
        .' Calendar._SMN = new Array ("'
        .JText::_('JANUARY_SHORT', true).'", "'
        .JText::_('FEBRUARY_SHORT', true).'", "'
        .JText::_('MARCH_SHORT', true).'", "'
        .JText::_('APRIL_SHORT', true).'", "'
        .JText::_('MAY_SHORT', true).'", "'
        .JText::_('JUNE_SHORT', true).'", "'
        .JText::_('JULY_SHORT', true).'", "'
        .JText::_('AUGUST_SHORT', true).'", "'
        .JText::_('SEPTEMBER_SHORT', true).'", "'
        .JText::_('OCTOBER_SHORT', true).'", "'
        .JText::_('NOVEMBER_SHORT', true).'", "'
        .JText::_('DECEMBER_SHORT', true).'");'
        .' Calendar._TT = {};Calendar._TT["INFO"] = "'.JText::_('JLIB_HTML_BEHAVIOR_ABOUT_THE_CALENDAR', true).'";'
        .' Calendar._TT["ABOUT"] =
        "DHTML Date/Time Selector\n" +
        "(c) dynarch.com 2002-2005 / Author: Mihai Bazon\n" +
        "For latest version visit: http://www.dynarch.com/projects/calendar/\n" +
        "Distributed under GNU LGPL.  See http://gnu.org/licenses/lgpl.html for details." +
        "\n\n" +
        "'.JText::_('JLIB_HTML_BEHAVIOR_DATE_SELECTION', true).'" +
        "'.JText::_('JLIB_HTML_BEHAVIOR_YEAR_SELECT', true).'" +
        "'.JText::_('JLIB_HTML_BEHAVIOR_MONTH_SELECT', true).'" +
        "'.JText::_('JLIB_HTML_BEHAVIOR_HOLD_MOUSE', true).'";
        Calendar._TT["ABOUT_TIME"] = "\n\n" +
        "Time selection:\n" +
        "- Click on any of the time parts to increase it\n" +
        "- or Shift-click to decrease it\n" +
        "- or click and drag for faster selection.";

        Calendar._TT["PREV_YEAR"] = "'.JText::_('JLIB_HTML_BEHAVIOR_PREV_YEAR_HOLD_FOR_MENU', true).'";'
        .' Calendar._TT["PREV_MONTH"] = "'.JText::_('JLIB_HTML_BEHAVIOR_PREV_MONTH_HOLD_FOR_MENU', true).'";'
        .' Calendar._TT["GO_TODAY"] = "'.JText::_('JLIB_HTML_BEHAVIOR_GO_TODAY', true).'";'
        .' Calendar._TT["NEXT_MONTH"] = "'.JText::_('JLIB_HTML_BEHAVIOR_NEXT_MONTH_HOLD_FOR_MENU', true).'";'
        .' Calendar._TT["NEXT_YEAR"] = "'.JText::_('JLIB_HTML_BEHAVIOR_NEXT_YEAR_HOLD_FOR_MENU', true).'";'
        .' Calendar._TT["SEL_DATE"] = "'.JText::_('JLIB_HTML_BEHAVIOR_SELECT_DATE', true).'";'
        .' Calendar._TT["DRAG_TO_MOVE"] = "'.JText::_('JLIB_HTML_BEHAVIOR_DRAG_TO_MOVE', true).'";'
        .' Calendar._TT["PART_TODAY"] = "'.JText::_('JLIB_HTML_BEHAVIOR_TODAY', true).'";'
        .' Calendar._TT["DAY_FIRST"] = "'.JText::_('JLIB_HTML_BEHAVIOR_DISPLAY_S_FIRST', true).'";'
        .' Calendar._TT["WEEKEND"] = "0,6";'
        .' Calendar._TT["CLOSE"] = "'.JText::_('JLIB_HTML_BEHAVIOR_CLOSE', true).'";'
        .' Calendar._TT["TODAY"] = "'.JText::_('JLIB_HTML_BEHAVIOR_TODAY', true).'";'
        .' Calendar._TT["TIME_PART"] = "'.JText::_('JLIB_HTML_BEHAVIOR_SHIFT_CLICK_OR_DRAG_TO_CHANGE_VALUE', true).'";'
        .' Calendar._TT["DEF_DATE_FORMAT"] = "'.JText::_('%Y-%m-%d', true).'";'
        .' Calendar._TT["TT_DATE_FORMAT"] = "'.JText::_('%a, %b %e', true).'";'
        .' Calendar._TT["WK"] = "'.JText::_('JLIB_HTML_BEHAVIOR_WK', true).'";'
        .' Calendar._TT["TIME"] = "'.JText::_('JLIB_HTML_BEHAVIOR_TIME', true).'";';

      $jsscript = 1;

      return $return;
    }
    else
    {
      return false;
    }
  }


}
