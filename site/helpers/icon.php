<?php
/**
 * @version		$Id: icon.php 21706 2011-06-28 21:28:56Z chdemko $
 * @package		Joomla.Site
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Content Component HTML Helper
 *
 * @static
 * @package		Joomla.Site
 * @subpackage	com_content
 * @since 1.5
 */
class JHtmlIcon
{

	static function print_popup( EventsModelEvents $eventsModel )
	{
    $strStart   = $eventsModel->getPeriodStart()->format( DateTimeHelper::ISO_DATE_FORMAT );
    $strLength  = $eventsModel->getPeriodLength();
    $url        = JRoute::_( "index.php?view=default&periodStart={$strStart}&periodLength={$strLength}" );
    $url        .= '&tmpl=component&print=1&layout=default';

    $status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no';
    $text = JHtml::_('image','system/printButton.png', JText::_('JGLOBAL_PRINT'), NULL, true);

    $attribs = array(
      'title'   => JText::_('JGLOBAL_PRINT'),
      'onclick' => "window.open(this.href,'win2','".$status."'); return false;",
      'rel'     => 'nofollow',
    );

    return JHtml::_('link',JRoute::_($url), $text, $attribs);
	}

	static function print_screen()
	{
    $text = JHtml::_('image','system/printButton.png', JText::_('JGLOBAL_PRINT'), NULL, true);
//    $text = JText::_('JGLOBAL_ICON_SEP') .'&#160;'. JText::_('JGLOBAL_PRINT') .'&#160;'. JText::_('JGLOBAL_ICON_SEP');

    return '<a href="#" onclick="window.print();return false;">'.$text.'</a>';
	}

}
