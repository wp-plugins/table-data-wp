<?php

/*License: The MIT License (MIT)

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
SOFTWARE.*/
require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
require_once(ABSPATH . 'wp-admin/includes/template.php');
    //Add menus on admin panel
    function bp_tdata_menu(){
        $headername=get_option('bp_tdata_headername');
        //Hook for register the settings
        add_action('admin_init', 'registerTableDataSettings');
        //Add Menu pages
        add_menu_page('Table Data', 'Table Data', 'manage_options', 'bptdata-options', 'addBPTableDataOptions');
        //Display page
        $head=($headername!="")?$headername:"Data Display";
        add_submenu_page( 'bptdata-options', '', $head, 'manage_options', 'bptdata-display', 'addBPTDataResults');
    }

    //Register Settings
    //Register the options on Wordpress
    function registerTableDataSettings(){
         register_setting('bp-tdata-settings-group', //settings page
            'bp_tdata_tablename' //option name
        );
          register_setting('bp-tdata-settings-group', //settings page
            'bp_tdata_headername' //option name
        );
          register_setting('bp-tdata-settings-group', //settings page
            'bp_tdata_columnexclude' //option name
        );
    }
 
    
    
    function addBPTableDataOptions(){
            $headername=get_option('bp_tdata_headername');
            $tablename=get_option('bp_tdata_tablename');
            $columnexclude=get_option('bp_tdata_columnexclude');
            ?>
            <div class="wrap">
                <h2></h2>
                <?php 
                if(isset($_GET['settings-updated']))
                {?>
                    <div id="setting-error-settings_updated" class="updated settings-error"> 
                    <p><strong>Settings saved.</strong></p></div>
                <?php 
                }?>
                <div id="detailview"><i>This plugin helps to display Database table data into View on Pages you want</i></div>
                <form method="post" action="options.php">
                    <?php settings_fields('bp-tdata-settings-group'); ?>
                    <?php do_settings_sections('bp-tdata-settings-group'); ?>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row">Table Name <small>(with prefix)</small></th>
                            <td><input type="text" id="bp_tdata_tablename" name="bp_tdata_tablename" value="<?php echo $tablename;?>"/></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Heading Name <small>(Displayed on top of result page)</small></th>
                            <td><input type="text" id="bp_tdata_headername" name="bp_tdata_headername" value="<?php echo $headername;?>"/></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">Exclude Columns <small>(eg:column1,column2..etc)</small></th>
                            <td><input type="text" id="bp_tdata_columnexclude" name="bp_tdata_columnexclude" value="<?php echo $columnexclude;?>"/></td>
                        </tr>
                    </table>
                    <?php submit_button(); ?>
                </form>
                <style>
                    #detailview{
                        width: 100%;
                        background: none repeat scroll 0% 0% #FFF;
                        height: 25px;
                        border-radius: 7px;
                        box-shadow: 0px 1px 2px #D3D33D;
                        padding: 1% 5%;
                    }
                </style>
            </div>
        <?php
        }
    
function addBPTDataResults(){ 
    $wp_list_table = new Data_List_Table();
    $wp_list_table->prepare_items();
    $headername=get_option('bp_tdata_headername');
?>
	<div class="wrap">

		<?php //Plugin Title ?>
		<div id="icon-plugins" class="icon32"><br /></div>
		<h2><?php echo ($headername!="")?$headername:"Table Data Results"?></h2>

		<?php //Table of elements
		$wp_list_table->display();
		?>

	</div>
<?php
}

class Data_List_Table extends WP_List_Table {


	/**
     * Constructor, we override the parent to pass our own arguments
     * We usually focus on three parameters: singular and plural labels, as well as whether the class supports AJAX.
     */	
	function __construct() {
		parent::__construct( array(
			'singular'=> 'wp_list_text_contact', //Singular label
			'plural' => 'wp_list_test_contacts', //plural label, also this well be one of the table class
			'ajax'	=> false //We won't support Ajax for this table
		) );
	}
	

    /**
     * Add extra markup in the toolbars before or after the list       
     * @param string $which, helps you decide if you add the markup after (bottom) or before (top) the list
     */		
	function extra_tablenav( $which ) {
            $headername=get_option('bp_tdata_headername');
		if ( $which == "top" ){
			//The code that goes before the table is here
			echo ($headername!="")?$headername:"Table Data <small>Database</small>";
		}
	}		


    /**
     * Define the columns that are going to be used in the table  
     * @return array $columns, the array of columns to use with the table
     */		
	function get_columns() {
                global $wpdb;
                $exclude=explode(',',get_option('bp_tdata_columnexclude'));
                $table=get_option('bp_tdata_tablename');
                if($table!=""){
                    $columnsDB=$wpdb->get_results("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '$table'");
                    foreach($columnsDB as $value)
                    {
                        if(!in_array($value->COLUMN_NAME, $exclude))
                        $columns[$value->COLUMN_NAME]=$value->COLUMN_NAME;
                    }
                }
                return $columns;
	}

    /**
     * Prepare the table with different parameters, pagination, columns and table elements
     */	
	function prepare_items() {
		global $wpdb, $_wp_column_headers;		
		$screen = get_current_screen();
		
		/* -- Preparing your query -- */
                $table=get_option('bp_tdata_tablename');
		$query = "SELECT * FROM ".$wpdb->prefix.$table;
		/* -- Ordering parameters -- */
			//Parameters that are going to be used to order the result
			$orderby = !empty($_GET["orderby"]) ? mysql_real_escape_string($_GET["orderby"]) : 'ASC';
			$order = !empty($_GET["order"]) ? mysql_real_escape_string($_GET["order"]) : '';
			if(!empty($orderby) & !empty($order)){ $query.=' ORDER BY '.$orderby.' '.$order; }

		/* -- Pagination parameters -- */
			//Number of elements in your table?
			$totalitems = $wpdb->query($query); //return the total number of affected rows
			//How many to display per page?
			$perpage = 5;
			//Which page is this?
			$paged = !empty($_GET["paged"]) ? mysql_real_escape_string($_GET["paged"]) : ''; if(empty($paged) || !is_numeric($paged) || $paged<=0 ){ $paged=1; }	//Page Number
			//How many pages do we have in total?
			$totalpages = ceil($totalitems/$perpage); //Total number of pages
			//adjust the query to take pagination into account
			if(!empty($paged) && !empty($perpage)){ 
				$offset=($paged-1)*$perpage;
				$query.=' LIMIT '.(int)$offset.','.(int)$perpage;
			}
                        


		/* -- Register the pagination -- */
			$this->set_pagination_args( array(
				"total_items" => $totalitems,
				"total_pages" => $totalpages,
				"per_page" => $perpage,
			) );
			//The pagination links are automatically built according to those parameters	
		
		/* -- Register the Columns -- */
			$columns = $this->get_columns();
			$_wp_column_headers[$screen->id]=$columns;
			
		/* -- Fetch the items -- */
			$this->items = $wpdb->get_results($query);	

	}	

    /**
     * Display the rows of records in the table
     * @return string, echo the markup of the rows 
     */	
	function display_rows() {
		
		//Get the records registered in the prepare_items method
		$records = $this->items;
		//Get the columns registered in the get_columns and get_sortable_columns methods
		$columns = $this->get_columns();
		//Loop for each record
                echo "<thead><tr>";
                foreach ( $columns as $column_name => $column_display_name ) {
				echo '<td>'.$column_display_name.'</td>';
			}
                echo "</tr></thead>";
		if(count($records)>0){
                        
                    foreach($records as $key=>$rec){
			//Open the line
			echo '<tr>';
			foreach ( $columns as $column_name => $column_display_name ) {
                            $cell=$rec->$column_name;
                            if(is_serialized($cell)){
                                $unser=  maybe_unserialize($cell);
                                $data=implode(",", $unser);
                            }
                            else
                            {
                                $data=$cell;
                            }
                            
                          
                            echo '<td '.$attributes.'>'.stripslashes($data).'</td>';
			}
			//Close the line
			echo'</tr>';	
		}
                
            }
	}
}
