<?php
require dirname( __FILE__ ) . '/views/class-region-list-table.php';
// class for custom tab shipping by city woocommerce settings
if ( ! class_exists( 'WP_Class_Cmetric_Arbyw_Setting' ) ) :

class WP_Class_Cmetric_Arbyw_Setting  {
	/**
	 * Setup settings class
	 *
	 * @since  1.0
	 */
  	public function __construct() {
  		$this->id    = 'arbyw';
      	$this->label = __( 'Add Region by Country', 'cmetric-arbyw' );
		add_action('admin_menu', array($this,'arbyw_region_by_country_page'));
        add_filter( 'woocommerce_states', array($this,'add_arbyw_custom_states_func'), 10, 1);
	//Admin init for admin side only
		//add_action( 'admin_init', array($this, 'arbyw_adminit'));
    }
/*Function for Admin int*/
	public function arbyw_adminit(){
		
	}
/* add menu to woocommerce menu */     
    public  function arbyw_region_by_country_page() {
        add_submenu_page( 'woocommerce', 'Add Region', 'Add Region', 'manage_options', 'arbyw-region-by-country-page', array($this,'arbyw_region_by_country_page_callback') );
    }
    /* configure new region added from backend Woocommerce */
    public function add_arbyw_custom_states_func( $states )
    {    
        global $wpdb;   
        $tablename		=$wpdb->prefix ."woocommerce_custom_region_by_country";
        $db_region_list	= $wpdb->get_results("SELECT * FROM $tablename ORDER BY id DESC ", ARRAY_A); 
            foreach ($db_region_list as $value) {
				$currenyarray 		= array();
				$currenyarray 		= $states[$value['countrycode']];
				$accepted_cities 	= array_map( 'strtoupper', array_map( 'trim', explode( PHP_EOL, $value['regionlist'] ) ) );
				$accepted_cities 	= array_map( 'trim', $accepted_cities );
				$accepted_cities 	= array_map( 'strtoupper', $accepted_cities );
              
				$i=0;
				$region_array=[];
				foreach ($accepted_cities as $value_region) {                      
                    $region_array[$value['countrycode'].$i] = __($value_region,'woocommerce');  
                    $i++; 
				}          
				$states[$value['countrycode']] = array_merge((array)$currenyarray,(array)$region_array);  
			}   
        return $states; 
    } 
    public function arbyw_region_by_country_page_callback() {
      include dirname( __FILE__ ) . '/views/region_country_page.php';    
          // Create an instance of our package class.
          $region_list_table = new Region_List_Table();
          // Fetch, prepare, sort, and filter our data.
          $region_list_table->prepare_items();
           ?>
           <div class="wrap">
                <div id="icon-users" class="icon32"></div>
                <h2>Region lists</h2>
                <?php  $region_list_table->display(); ?>
            </div>
         <?php
    }
}
endif;
?>