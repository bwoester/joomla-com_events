<?php
/**
 * @version     1.2.0
 * @package     com_events
 * @copyright   Copyright (C) 2011. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Benjamin Wöster
 */

// no direct access
defined('_JEXEC') or die;

$published = $this->state->get('filter.published');
?>
<fieldset class="batch">
	<legend><?php echo JText::_('COM_EVENTS_BATCH_OPTIONS');?></legend>
	<p><?php echo JText::_('COM_EVENTS_BATCH_TIP'); ?></p>

	<?php if ($published >= 0) : ?>
		<?php echo JHtml::_('batch.item', 'com_events');?>
	<?php endif; ?>

	<button type="submit" onclick="Joomla.submitbutton('event.batch');">
		<?php echo JText::_('JGLOBAL_BATCH_PROCESS'); ?>
	</button>
	<button type="button" onclick="document.id('batch-category-id').value='';">
		<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>
	</button>
</fieldset>
