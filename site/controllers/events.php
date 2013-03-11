<?php

/**
 * @version     1.0.0
 * @package     com_events
 * @copyright   Copyright (C) 2011. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Created by com_combuilder - http://www.notwebdesign.com
 */
// No direct access.
defined('_JEXEC') or die;

require_once JPATH_COMPONENT . '/controller.php';

/**
 * Events list controller class.
 */
class EventsControllerEvents extends EventsController {

  /**
   * Proxy for getModel.
   * @since	1.6
   */
  public function &getModel($name = 'Events', $prefix = 'EventsModel') {
    $model = parent::getModel($name, $prefix, array('ignore_request' => true));
    return $model;
  }

}