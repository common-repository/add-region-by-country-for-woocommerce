<?php
/* main class for all settings & declaration */
if ( ! class_exists('WC_Cmetric_Arbyw')){
class WC_Cmetric_Arbyw{
  	private static $instance;
 	const TEXT_DOMAIN = 'cmetric-arbyw';	
	public static function get_instance() {
			if ( ! self::$instance ) {
				self::$instance = new WC_Cmetric_Arbyw();
			}
		return self::$instance;
	}
	public function __construct() {
		add_action( 'plugins_loaded', array($this,'WC_load_cmetric_arbyw' ));
		register_activation_hook( __FILE__, array( $this, 'activation_check' ) );
		$this->setup_constants();
		$this->includes();
	}
/* define all global variable for plugin here */
	private function setup_constants() {
		if ( ! defined( 'WC_CMETRIC_ARBYW_PLUGIN_FILE' ) ) {
			define( 'WC_CMETRIC_ARBYW_PLUGIN_FILE', __FILE__ );
		}
		if ( ! defined( 'WC_CMETRIC_ARBYW_PLUGIN_DIR_SSL' ) ) {
			define( 'WC_CMETRIC_ARBYW_PLUGIN_DIR_SSL', dirname( __FILE__ ) );
		}	
		if ( ! defined( 'WC_CMETRIC_ARBYW_PLUGIN_DIR_SSL' ) ) {
	 		define( 'WC_CMETRIC_ARBYW_FRONT_ASSET_DIR', plugin_dir_url( __FILE__ ) );
		}
	}
/* include allr required files here */ 
	private function includes() {
		require_once WC_CMETRIC_ARBYW_PLUGIN_DIR_SSL . '/includes/cmetric_arbyw_setting_functions.php';			
		$this->init();
	}
	private function init() {
		add_action( 'init', array( $this, 'load_translation' ) );
		add_action( 'admin_enqueue_scripts',array($this, 'arbyw_enqueue_scripts_func_admin')); 
		new WP_Class_Cmetric_Arbyw_Setting();				
		return true;
		// $GLOBAL['WP_Class_Cmetric_Sbcfw'] = new WP_Class_Cmetric_Sbcfw();
	}
	public function load_translation()
	{
		load_plugin_textdomain( self::TEXT_DOMAIN, false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}
/* Enqueue admin CSS here */    
	public function arbyw_enqueue_scripts_func_admin() {
		wp_enqueue_style('arbyw-admin-styles', plugin_dir_url( __FILE__ ).'includes/css/admin.css');
    }
// The primary sanity check, automatically disable the plugin on activation if it doesn't
// meet minimum requirements.
    static function activation_check() {
        if ( ! self::compatible_version() ) {
            deactivate_plugins( WC_CMETRIC_ARBYW_PLUGIN_FILE, true );
            wp_die( __( 'My Plugin requires WordPress 3.7 or higher!', 'cmetric-arbyw' ) );
        }
         update_option( 'WC_CMETRIC_ARBYW_DB_VERSION', WC_CMETRIC_ARBYW_DB_VERSION );
    }
/* check for woocomerce plugin exitst or not */
	public function WC_load_cmetric_arbyw(){
		$is_wc_active = class_exists( 'woocommerce' ) ? is_plugin_active( 'woocommerce/woocommerce.php' ) : false;
	
		if ( current_user_can( 'activate_plugins' ) && ! $is_wc_active ) {
	
			add_action( 'admin_notices', array($this,'woocommerce_cmetric_arbyw_activation_notice' ));
	
			//Don't let this plugin activate
			deactivate_plugins( plugin_basename( __FILE__ ) );
			$sanitize_activate 	= sanitize_text_field($_GET['activate']);
			if ( isset( $sanitize_activate ) ) {
				unset( $sanitize_activate );			
			}
			return false;
		}
		else{
		$this->cmetric_arbyw_structure_install_function();
		}
	}
	static function compatible_version() {
        if ( version_compare( $GLOBALS['wp_version'], '3.7', '<' ) ) {
            return false;
        }
        // Add sanity checks for other version requirements here
        return true;
    }
/* custom notice added for plugin */
	public function woocommerce_cmetric_arbyw_activation_notice() {
		echo '<div class="error"><p>' . __( '<strong>Activation Error:</strong> You must have the <a href="https://wordpress.org/plugins/woocommerce/" target="_blank">WooCommerce</a> plugin installed and activated for the <b>add region by country for Woocommerce</b> to activate.',  self::TEXT_DOMAIN ) . '</p></div>';
    }
    public function cmetric_arbyw_structure_install_function(){
		global $wpdb;
	/* create required table code on plugin */
		$charset_collate = $wpdb->get_charset_collate();
		$table_name=$wpdb->prefix ."woocommerce_custom_region_by_country";
		if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name)
		{
				$sql = "CREATE TABLE $table_name (
				id int(50) NOT NULL AUTO_INCREMENT,
				countrycode varchar(200) NULL,
			 /*   regioncode varchar(200) NULL, */
				regionlist longtext null,                   
				created_date datetime DEFAULT CURRENT_TIMESTAMP  NULL,                    
				PRIMARY KEY  (id),
				CONSTRAINT UNIQUE (countrycode)
			  ) $charset_collate;";
			  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			  dbDelta( $sql );
			update_option( 'WC_CMETRIC_ARBYW_DB_VERSION', WC_CMETRIC_ARBYW_DB_VERSION );
		}
    }
}
}
?>