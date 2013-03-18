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
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'event.cancel' || document.formvalidator.isValid(document.id('event-form'))) {
			Joomla.submitform(task, document.getElementById('event-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_events&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="event-form" class="form-validate">
	
  <div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_EVENTS_LEGEND_EVENT'); ?></legend>
			<ul class="adminformlist">
            
        <li>
          <?php echo $this->form->getLabel('catid'); ?>
          <?php echo $this->form->getInput('catid'); ?>
        </li>

        <li>
          <?php echo $this->form->getLabel('state'); ?>
          <?php echo $this->form->getInput('state'); ?>
        </li>
        
        <li>
          <?php echo $this->form->getLabel('title'); ?>
          <?php echo $this->form->getInput('title'); ?>
        </li>

        <li>
          <?php echo $this->form->getLabel('time_start'); ?>
          <?php echo $this->form->getInput('time_start'); ?>
        </li>

        <li>
          <?php echo $this->form->getLabel('time_end'); ?>
          <?php echo $this->form->getInput('time_end'); ?>
        </li>
        
        <li>
          <?php echo $this->form->getLabel('location'); ?>
          <?php echo $this->form->getInput('location'); ?>
        </li>
        
        <li>
          <?php echo $this->form->getLabel('id'); ?>
          <?php echo $this->form->getInput('id'); ?>
        </li>
        
      </ul>
    </fieldset>
    
    
    
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_EVENTS_LEGEND_EVENT_DETAILS'); ?></legend>
			<ul class="adminformlist">

        <li>
          <?php echo $this->form->getLabel('meeting_place'); ?>
          <?php echo $this->form->getInput('meeting_place'); ?>
        </li>

        <li>
          <?php echo $this->form->getLabel('meeting_time'); ?>
          <?php echo $this->form->getInput('meeting_time'); ?>
        </li>
        
        <li>
          <?php echo $this->form->getLabel('description'); ?>
          <?php echo $this->form->getInput('description'); ?>
        </li>

      </ul>
    </fieldset>
      
    <?php echo $this->form->getLabel('checked_out'); ?>
    <?php echo $this->form->getInput('checked_out'); ?>
      
    <?php echo $this->form->getLabel('checked_out_time'); ?>
    <?php echo $this->form->getInput('checked_out_time'); ?>

	</div>

  
  
	<div class="width-40 fltrt">

		<?php echo JHtml::_('sliders.start', 'event-sliders-'.$this->item->id, array('useCookie'=>1)); ?>
		
    <?php echo JHtml::_('sliders.panel', JText::_('COM_EVENTS_PANEL_STATUS'), 'actions'); ?>
    <fieldset class="panelform">
      <ul class="adminformlist">
        <li>
          <?php echo $this->form->getLabel('cancelled'); ?>
          <?php echo $this->form->getInput('cancelled'); ?>
        </li>
      </ul>
    </fieldset>

		<?php echo JHtml::_('sliders.end'); ?>
	</div>
  
  

	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
	<div class="clr"></div>
</form>