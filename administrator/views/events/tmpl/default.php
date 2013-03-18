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

JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');

$user       = JFactory::getUser();
$userId     = $user->get('id');
$listOrder  = $this->escape($this->state->get('list.ordering'));
$listDirn   = $this->escape($this->state->get('list.direction'));
$canOrder   = $user->authorise('core.edit.state', 'com_events.category');
$saveOrder	= $listOrder=='ordering';
$params     = (isset($this->state->params)) ? $this->state->params : new JObject();

?>

<form action="<?php echo JRoute::_('index.php?option=com_events&view=events'); ?>" method="post" name="adminForm" id="adminForm">

  <fieldset id="filter-bar">

    <div class="filter-search fltlft">
      <label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
      <input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('Search'); ?>" />
      <button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
      <button type="button" onclick="document.id('filter_search').value = '';
          this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
    </div>

		<div class="filter-select fltrt">
			<select name="filter_state" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.state'), true);?>
			</select>
      
			<select name="filter_category_id" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_CATEGORY');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('category.options', 'com_events'), 'value', 'text', $this->state->get('filter.category_id'));?>
			</select>
		</div>

  </fieldset>

  <div class="clr"> </div>

  <table class="adminlist">
    <colgroup>
      <col width="1%" />
      <col />
      <col />
      <col />
      <col />
      <col />
      <col />
      <col width="1%" />
    </colgroup>    
    <thead>
      <tr>
        <th>
          <input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)" />
        </th>
        <th>
          <?php echo JHtml::_('grid.sort', 'COM_EVENTS_HEADING_TITLE', 'a.title', $listDirn, $listOrder); ?>
        </th>
        <th>
          <?php echo JHtml::_('grid.sort', 'COM_EVENTS_HEADING_TIME_START', 'a.time_start', $listDirn, $listOrder); ?>
        </th>
        <th>
          <?php echo JHtml::_('grid.sort', 'COM_EVENTS_HEADING_LOCATION', 'a.location', $listDirn, $listOrder); ?>
        </th>
        <th>
          <?php echo JHtml::_('grid.sort', 'COM_EVENTS_HEADING_CANCELLED', 'a.cancelled', $listDirn, $listOrder); ?>
        </th>
				<th>
					<?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
				</th>
        <th>
					<?php echo JHtml::_('grid.sort', 'JCATEGORY', 'category_title', $listDirn, $listOrder); ?>
        </th>
				<th>
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
				</th>
      </tr>
    </thead>
    <tfoot>
			<tr>
				<td colspan="8">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
    </tfoot>
    <tbody>

    <?php foreach ($this->items as $i => $item): ?>
      <?php
      $canCreate  = $user->authorise( 'core.create'     , "com_events.category.{$item->catid}" );
			$canEdit    = $user->authorise( 'core.edit'       , "com_events.category.{$item->catid}" );
			$canCheckin = $user->authorise( 'core.manage'     , 'com_checkin')
                    || $item->checked_out == $userId
                    || $item->checked_out == 0;
			$canChange  = $user->authorise( 'core.edit.state' , "com_events.category.{$item->catid}")
                    && $canCheckin;
      $id         = (int)$item->id;
      $editUrl    = JRoute::_( "index.php?option=com_events&task=event.edit&id={$id}" );
      ?>
      <tr class="row<?php echo $i % 2; ?>">
        <td class="center">
          <?php echo JHtml::_('grid.id', $i, $id); ?>
        </td>
				<td>
          <?php
          if ($item->checked_out) {
            echo JHtml::_( 'jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'events.', $canCheckin );
          }
          ?>
					<?php if ($canEdit): ?>
            <a href="<?php echo $editUrl; ?>"><?php echo $this->escape( $item->title ); ?></a>
					<?php else: ?>
            <?php echo $this->escape( $item->title ); ?>
					<?php endif; ?>
				</td>
        <td>
          <?php echo JHtml::_('date', $item->time_start, JText::_('DATE_FORMAT_LC2')); ?>
        </td>
        <td>
          <?php echo $this->escape( $item->location ); ?>
        </td>
        <td>
          <?php // TODO: implement toggle switch ?>
          <?php echo ((int)$item->cancelled === 0) ? JText::_('JNO') : JText::_('JYES'); ?>
        </td>
				<td>
					<?php echo JHtml::_( 'jgrid.published', $item->state, $i, 'events.', $canChange ); ?>
				</td>
				<td>
					<?php echo $this->escape( $item->category_title ); ?>
				</td>
				<td>
					<?php echo $id; ?>
				</td>        
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div>
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
    <?php echo JHtml::_('form.token'); ?>
  </div>
</form>