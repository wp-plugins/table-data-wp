<?php
/**
 * Plugin Name: Table Data WP
 * Plugin URI: https://github.com/BabuYii/table-data-wp
 * Description: The Simple Table Data WP plugin Displays the Table Records in Database
 * Version: 1.0
 * Author: Babu M
 * Author URI: www.infobabu.com
 * License: The MIT License (MIT)

Copyright (c) 2014 Babu M

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
 */


require_once('PHP/config.php');
class WPTableData{
    
     static $inst;
    
     function __construct()
    {
        self::$inst = $this;

        add_action('init', array($this, 'init'));
    }
    //Add hooks on app init
    function init()
    {
        add_action('admin_menu','bp_tdata_menu');
        //create option for table name to be used
        add_option( 'bp_tdata_tablename');
        add_option( 'bp_tdata_headername');
        add_option( 'bp_tdata_columnexclude');
    }

}
new WPTableData;