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

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

/* Some help for the IDE */
/* @var $this EventsViewEvents */
/* @var $eventsModel EventsModelEvents */

$eventsModel = $this->eventsModel;

?>

<div class="item-page">

<ul class="actions">
<?php if (!$this->print) : ?>

  <?php if ($eventsModel->showPrintAction()): ?>
    <li class="print-icon">
    <?php echo JHtml::_('icon.print_popup', $eventsModel); ?>
    </li>
  <?php endif; ?>

  <li class="calendar-icon">
    <a href="<?php echo $eventsModel->getLinkCalendar(); ?>">
      zum Kalender
    </a>
  </li>

<?php else : ?>
  <li>
  <?php echo JHtml::_('icon.print_screen'); ?>
  </li>
<?php endif; ?>
</ul>


<dl class="article-info">
  <dt class="article-info-term">Integration</dt>
  <?php if ($eventsModel->showLinkICalendarHelp()): ?>
  <dd class="icalendar-help">
    Was ist iCalendar? <a href="<?php echo $eventsModel->getLinkICalendarHelp(); ?>">iCalendar Hilfe</a>
  </dd>
  <?php endif; ?>
  <dd class="icalendar-link">
    iCalendar Link: <a href="<?php echo $eventsModel->getLinkICalendar(); ?>">iCalendar</a>
  </dd>
 </dl>

  
<p>
<?php echo $eventsModel->getHeader(); ?>
</p>

<table>
  <caption>
    Die Termine für den Zeitraum
    <span class="period"><?php echo $eventsModel->getPeriodStart()->format(EventsModelEvents::GERMAN_DATE_FORMAT); ?></span>
    bis <span class="period"><?php echo DateTimeHelper::substractDays($eventsModel->getPeriodEnd(),1)->format(EventsModelEvents::GERMAN_DATE_FORMAT); ?></span>.
  </caption>
  <thead>
    <tr>
      <th>Datum</th>
      <th>Was</th>
      <th>Wo</th>
      <th>Begin</th>
    </tr>
  </thead>
<?php foreach ($eventsModel->getEventsForCurrentPeriod() as $eventItem): ?>
  <tr>
    <?php
      $date = new DateTime($eventItem->time_start);
    ?>
    <td><?php echo $date->format('d.m.Y'); ?></td>
    <td><?php echo $eventItem->title; ?></td>
    <td><?php echo $eventItem->location; ?></td>
    <td><?php echo $date->format('H:i') . ' Uhr'; ?></td>
  </tr>
<?php endforeach; ?>
</table>

<p>
<?php echo $eventsModel->getFooter(); ?>
</p>

<?php if (!$this->print) : ?>
<div class="pagination">
<ul>
  <li>
    <?php if ($eventsModel->showLinkEarlier()): ?>
    <a href="<?php echo $eventsModel->getLinkEarlier(); ?>">
      <?php echo $eventsModel->getPeriodLength(); ?> Monate zurück
    </a>
    <?php else: ?>
      <?php echo $eventsModel->getPeriodLength(); ?> Monate zurück
    <?php endif; ?>
  </li>
  <li>
    <?php if ($eventsModel->showLinkLater()): ?>
    <a href="<?php echo $eventsModel->getLinkLater(); ?>">
      <?php echo $eventsModel->getPeriodLength(); ?> Monate vor
    </a>
    <?php else: ?>
      <?php echo $eventsModel->getPeriodLength(); ?> Monate vor
    <?php endif; ?>
  </li>
</ul>
</div>
<?php endif; ?>

</div> <!-- class="item-page" -->