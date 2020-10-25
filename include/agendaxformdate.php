<?php

// ------------------------------------------------------------------------- //
//                           Agendax-X for Xoops                             //
//                              Version:  2.0                                //
// ------------------------------------------------------------------------- //
// Author: Wang Jue (alias wjue)                                             //
// Purpose: Very Flexible Calendar and Event Module                          //
// email: wjue@wjue.org                                                      //
// URLs: http://www.wjue.org,  http://www.guanxiCRM.com                      //
//---------------------------------------------------------------------------//
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//---------------------------------------------------------------------------//
// Parts of this software is based on EXTcalendar                            //
// of Kristof De Jaeger<br>sweaty@urgent.ugent.be                          //

/**
 * Date selection field
 */
class agendaxFormDate extends XoopsFormElement
{
    /**
     * Size
     * @var    int
     */

    public $_size;

    /**
     * Maximum length of the text
     * @var    int
     */

    public $_maxlength;

    /**
     * Initial text
     * @var    string
     */

    public $_value;

    /**
     * Constructor
     *
     * @param string $caption   Caption
     * @param string $name      "name" attribute
     * @param int    $size      Size
     * @param int    $maxlength Maximum length of text
     * @param string $value     Initial text
     */
    public function __construct($caption, $name, $value = '', $size = 12, $maxlength = 12)
    {
        $this->setCaption($caption);

        $this->setName($name);

        $this->_size = (int)$size;

        $this->_maxlength = (int)$maxlength;

        $this->_value = $value;
    }

    /**
     * Get size
     *
     * @return    int
     */
    public function getSize()
    {
        return $this->_size;
    }

    /**
     * Get maximum text length
     *
     * @return    int
     */
    public function getMaxlength()
    {
        return $this->_maxlength;
    }

    /**
     * Get initial text
     *
     * @return  string
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * Prepare HTML for output
     *
     * @return    string  HTML
     */
    public function render()
    {
        $rending = "<input type='text' name='" . $this->getName() . "' id='" . $this->getName() . "' size='" . $this->getSize() . "' maxlength='" . $this->getMaxlength() . "' value='" . $this->getValue() . "'" . $this->getExtra() //		    ."><button id=\"trigger\">...</button>";
                   . '><button id="trigger_' . $this->getName() . '">...</button>';

        $rending .= '<script type="text/javascript">
            Calendar.setup(
            {
                inputField  : "' . $this->getName() . '",      // ID of the input field
                ifFormat    : "y-M-d",    // the date format
//                button      : "trigger"    // ID of the button
                button      : "trigger_' . $this->getName() . '"    // ID of the button
               }
            );
            </script>
            ';

        return $rending;
    }
}
