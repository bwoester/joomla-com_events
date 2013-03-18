<?php
/**
 * @version     1.0.0
 * @package     com_events
 * @copyright   Copyright (C) 2011. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Created by com_combuilder - http://www.notwebdesign.com
 */

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

class JFormFieldToggle extends JFormField
{
  public $defaultTheme = 'candy';
  public $themes = array('candy', 'candy-blue', 'candy-yellow', 'android', 'ios');
  public $mapThemesCssClass = array(
    'candy'       => 'candy',
    'candy-blue'  => 'candy blue',
    'candy-yellow'=> 'candy yellow',
    'android'     => 'android',
    'ios'         => 'ios'
  );
  
  /**
   * The form field type.
   *
   * @var    string
   * @since  1.6
   */
  protected $type = 'Toggle';

  /**
   * Method to get the field input markup.
   *
   * @return  string  The field input markup.
   * @since  1.6
   */
  protected function getInput()
  {
    // register necessary style sheets
    JHtml::_( 'stylesheet', 'com_events/css-toggle-switch/toggle-switch.css', array(), true );
    JHtml::_( 'stylesheet', 'com_events/com_events/com_events.css', array(), true );
    
    $markup = <<<TOGGLE_MARKUP
<div class="{$this->_getContainerCssClass()}">
  {$this->_getInputOptions()}
  <span class="slide-button"></span>
</div>            
TOGGLE_MARKUP;
    
    return $markup;
  }
  
  /////////////////////////////////////////////////////////////////////////////

  private function _getInputOptions()
  {
    $retVal = array();
    $options = $this->_getOptions();
    
    // Build the radio field output.
    foreach ($options as $i => $option)
    {
      // Initialize some option attributes.
      $checked  = ((string) $option->value == (string) $this->value) ? 'checked="checked"' : '';
      $class    = !empty($option->class) ? 'class="' . $option->class . '"' : '';
      $disabled = !empty($option->disable) ? 'disabled="disabled"' : '';

      // Initialize some JavaScript option attributes.
      $onclick = !empty($option->onclick) ? 'onclick="' . $option->onclick . '"' : '';

      $id     = $this->id . $i;
      $value  = htmlspecialchars( $option->value, ENT_COMPAT, 'UTF-8' );
      $lblText= JText::alt( $option->text, preg_replace('/[^a-zA-Z0-9_\-]/','_',$this->fieldname) );
      
      // label's onclick="" is there to solve problems with some mobile browsers.
      $retVal[] = <<<TOGGLE_MARKUP
  <input type="radio" id="{$id}" name="{$this->name}" value="{$value}" {$checked} {$class} {$onclick} {$disabled} />
  <label onclick="" for="{$id}" {$class}>{$lblText}</label>
TOGGLE_MARKUP;
      
    }
    
    return implode( "\n", $retVal );
  }
  
  /////////////////////////////////////////////////////////////////////////////
  
  /**
   * Method to get the field options for radio buttons.
   *
   * @return  array  The field option objects.
   *
   * @since   11.1
   */
  private function _getOptions()
  {
    // Initialize variables.
    $options = array();

    foreach ($this->element->children() as $option)
    {

      // Only add <option /> elements.
      if ($option->getName() != 'option')
      {
        continue;
      }

      // Create a new option object based on the <option /> element.
      $tmp = JHtml::_(
        'select.option', (string) $option['value'], trim((string) $option), 'value', 'text',
        ((string) $option['disabled'] == 'true')
      );

      // Set some option attributes.
      $tmp->class = (string) $option['class'];

      // Set some JavaScript option attributes.
      $tmp->onclick = (string) $option['onclick'];

      // Add the option object to the result set.
      $options[] = $tmp;
    }

    reset($options);

    return $options;
  }
  
  /////////////////////////////////////////////////////////////////////////////
  
  private function _getContainerCssClass()
  {
    $aClasses = array(
      'switch',
      $this->_getCssClassForTheme(),
    );

    if ($this->element['class']) {
      $aClasses[] = (string) $this->element['class'];
    }

    return implode( ' ', $aClasses );
  }
  
  /////////////////////////////////////////////////////////////////////////////
  
  private function _getTheme()
  {
    $configuredTheme = isset( $this->element['theme'] )
      ? (string)$this->element['theme']
      : $this->defaultTheme;
    return in_array( $configuredTheme, $this->themes )
      ? $configuredTheme
      : $this->defaultTheme;
  }
  
  /////////////////////////////////////////////////////////////////////////////
  
  private function _getCssClassForTheme()
  {
    return $this->mapThemesCssClass[ $this->_getTheme() ];
  }
  
  /////////////////////////////////////////////////////////////////////////////
  
}