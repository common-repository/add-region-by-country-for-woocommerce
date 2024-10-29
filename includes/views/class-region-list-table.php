<?php
// Turn off error reportingerror_reporting(0);
if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Region_List_Table extends WP_List_Table {
    function __construct(){
        global $status, $page;
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'id',     //singular name of the listed records
            'plural'    => 'properties',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );
    }
    function column_default($item, $column_name){
        //create array to get name by check box value for email 
        switch($column_name){
             case 'id':
             return  $item[$column_name];
             case 'countryname':                       
                $countries = new WC_Countries();
                $countries = $countries->__get('countries');            
                return $countries[$item['countrycode']];
            case 'regionlist' :
            $htm = '';                   
                      $arrr =  explode( PHP_EOL, $item[$column_name] );                
                    foreach ($arrr as $key => $value) {
                        $htm .=$value.'<br>';
                    }
                    return $htm;
            case 'edit_action':
            $datalink='';
                if ( is_admin() ) {
                     $datalink .= '<a href="?page=arbyw-region-by-country-page&uid='.$item['id'].'&action=update">Edit</a> | '; 
                
                     $datalink .= '<a href="?page=arbyw-region-by-country-page&uid='.$item['id'].'&action=delete">Delete</a>'; 
                }
                return $datalink;             
            default:
                return  print_r($item[$column_name],true); //Show the whole array for troubleshooting purposes
        }
    }
    function get_property_value($pid = '',$pname = ''){
        global $wpdb;       
        $tablename=$wpdb->prefix ."woocommerce_custom_region_by_country";   
        $statusval = $wpdb->get_results(" SELECT * FROM $tablename");
        return $statusval[0]->property_value;
    }
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
            /*$2%s*/ $item['id']                //The value of the checkbox should be the record's id
        );
    }
    function get_columns(){
        $columns = array(
          //  'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
           //  'id'=> 'No',
            'countrycode'     => 'Country code',   
            'countryname'  => 'Country Name',         
            'regionlist'    => 'Regions',                          
            'edit_action'=> 'ACTION'
        );
        return $columns;
    }
    function get_sortable_columns() {
        $sortable_columns = array(          
        );
        return $sortable_columns;
    }
    /*function get_bulk_actions() {
        $actions = array(
            'delete'    => 'Delete'
        );
        return $actions;
    }*/
    function process_bulk_action() {  
    global $wpdb;      
        //Detect when a bulk action is being triggered...        
        if( 'delete'===$this->current_action() ) {
                $tablename  =$wpdb->prefix ."woocommerce_custom_region_by_country";
                $uid        = intval($_GET['uid']);
                $wpdb->delete( $tablename, array( 'id' => $uid) );
                $this->listing_redirection($message='delete');
        }        
    }
    function check_allow_delete($cid){
     
    }
    function listing_redirection($message){
        $url = home_url().'/wp-admin/admin.php?page=arbyw-region-by-country-page&message='.$message;
        wp_redirect($url);
        die();
    }
    function prepare_items() {
        global $wpdb; //This is used only if making any database queries
        $per_page   = 10;
        $columns    = $this->get_columns();
        $hidden     = array();
        $sortable   = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->process_bulk_action();     
        
        global $wpdb;       
        $tablename  = $wpdb->prefix ."woocommerce_custom_region_by_country";
        
        if(isset($_GET['s']) && isset($_GET['s'])){
            $searchval  = sanitize_text_field($_GET['s']);
        }
        if(isset($searchval) && !empty($searchval)){
            $searchdata = trim($searchval);
            $plist= $wpdb->get_results("SELECT * FROM $tablename where  regionlist LIKE '%$searchdata%' or countrycode LIKE '%$searchdata%' GROUP By id  ORDER BY id DESC ");
        }else{
            $plist= $wpdb->get_results("SELECT * FROM $tablename ORDER BY id DESC ");           
        }
        $id         = array();
        $countrycode= array(); 
        $regionlist = array();
        $i          =0;
        foreach ($plist as $wk_posts) {
            $id[]           =$wk_posts->id;
            $countrycode[]  =$wk_posts->countrycode; 
            $regionlist[]   =$wk_posts->regionlist;
            $data[] = array(
                    'id' => $id[$i],
                    'countrycode'  => $countrycode[$i], 
                    'regionlist' =>   $regionlist[$i]    
                    );
            $i++;
        }
        
        if(isset($_POST['sync']) && isset($_POST['sync'])){
            $sanitizesync = sanitize_text_field($_POST['sync']);
        }
        if(isset($sanitizesync)){
            add_edit_property();
        }       
        function usort_reorder($a,$b){
            $sanitize_orderby   = sanitize_text_field($_REQUEST['orderby']);
            $sanitize_order     = sanitize_text_field($_REQUEST['order']);
            $orderby            = (!empty($sanitize_orderby)) ? $sanitize_orderby : 'postcode'; //If no sort, default to title
            $order              = (!empty($sanitize_order)) ? $sanitize_order : 'asc'; //If no order, default to asc
            $result             = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
        }
        //usort($data, 'usort_reorder');    
                        
        $current_page   = $this->get_pagenum();        
       
   
        if(!empty($data)){
             $total_items    = count(array($data)); 
            $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
            $this->items    = $data;
            $this->set_pagination_args( array(
                'total_items' => $total_items,                  //WE have to calculate the total number of items
                'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
                'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
            ) );   
        }        
            
       
    }
}
?>