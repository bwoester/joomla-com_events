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
 * DateTime helper.
 */
class ComEventsDatetime
{

	/**
	 * Displays a date time picker control field
   *
   * Use "JB Library" from "http://www.joomlabamboo.com/joomla-extensions/jb-library-plugin-a-free-joomla-jquery-plugin"
   * to get jquery support needed for the date time picker.
	 *
	 * @param   string  $value    The date time value
	 * @param   string  $name     The name of the text field
	 * @param   string  $id       The id of the text field
	 * @param   string  $format   The date time format (see "http://www.ama3.com/anytime/index.php#AnyTime.Converter.format")
	 * @param   array   $attribs  Additional HTML attributes
	 *
	 * @return string
	 */
  public static function render($value, $name, $id, $format='%Y-%m-%d %H:%i:%S', $attribs=null)
	{
		static $done;

		if ($done === null) {
			$done = array();
		}

		$readonly = isset($attribs['readonly']) && $attribs['readonly'] == 'readonly';
		$disabled = isset($attribs['disabled']) && $attribs['disabled'] == 'disabled';

		if ((!$readonly) && (!$disabled))
    {
			// Load the date time behavior
			JHtml::_('ComEvents.behavior.datetime');
			JHtml::_('behavior.tooltip');

			// Only display the triggers once for each control.
			if (!in_array($id, $done))
			{
				$document = JFactory::getDocument();
				$document->addScriptDeclaration(
          'window.addEvent( "domready", function() {
            $("#'.$id.'").AnyTime_picker({
              format: "'.$format.'",
              firstDOW: '.JFactory::getLanguage()->getFirstDay().'
            });
            $("#'.$id.'_img").click( function() {
              $("#'.$id.'").focus();
            });
          });'
        );

				$done[] = $id;
			}
		}

    $attribs['type']  = 'text';
    $attribs['title'] = (0!==(int)$value ? JHtml::_('date',$value):'');
    $attribs['name']  = $name;
    $attribs['id']    = $id;
    $attribs['value'] = htmlspecialchars( $value, ENT_COMPAT, 'UTF-8' );

    $image = $readonly ? '' : JHtml::_(
      'image','system/calendar.png',
      JText::_('JLIB_HTML_CALENDAR'),
      array(
        'class' => 'calendar',
        'id'    => $id . '_img'
      ),
      true
    );

		return '<input '.JArrayHelper::toString($attribs).'/>' . $image;
	}

}
