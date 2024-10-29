<div class="wrap"><div id="icon-tools" class="icon32"></div>
<h2><?php echo esc_html( __( 'Add Regions by Country', 'cmetric-arbyw' ) );?></h2>
<?php
	global $wpdb;
	$tablename		= $wpdb->prefix ."woocommerce_custom_region_by_country";
	$alert			= "";
	$noticetype		= "";
	if(isset($_POST['action']) && isset($_POST['action'])){
		$post_action 	= sanitize_text_field($_POST['action']);
	}
	
		if (isset($post_action) && $post_action != "") {
			if ($post_action == 'add_new_region') {	 	
				$cdate 			= date('Y-m-d H:i:s');   
				$country_code	= sanitize_text_field($_POST['country_code']);
				$region_list	= sanitize_textarea_field($_POST['region_list']);
				if ($wpdb->insert(
					$tablename, array(
							'countrycode' 	=> $country_code,
							'regionlist'	=> $region_list,
							'created_date'	=> $cdate
					)) == true) {
						$alert .= "New Region List Added.";
					} else {
						$alert .= "ERROR: multiple Region entry not allowed for same country. Please Update existing Region list.";
							$noticetype .= "notice-error";
					}   
			}    	 
			if ($post_action == 'edit_region') {
				$uid 		= intval($_POST['id']);
				$region_list= sanitize_textarea_field($_POST['region_list']);
				if($uid){
					if ($wpdb->update(
					$tablename, array(                         
							'regionlist'=>$region_list,),
							array('id' => $uid)) == true) {
						$alert.="Region List Updated.";
						}	
				} else {
					$alert.="ERROR: Price List NOT Updated";
				} 
            }
        }
	
	if(isset($_GET['action']) && isset($_GET['action']) || isset($_GET['uid']) && isset($_GET['uid'])){
		$get_action	= sanitize_text_field($_GET['action']);
		$get_uid	= sanitize_text_field($_GET['uid']);
	}
	
	if (isset($get_action) && isset($get_uid) && ($get_action=='update')) {    
  		$uid = $get_uid;
  		region_build_form('edit-map', $uid,$alert);
	} else {
		$uid='';
  		region_build_form('add-map', $uid,$alert);	
	}
function region_build_form($type, $id,$alert){
		if ($type == 'add-map') {
		 	$message = "Add the details of Region.";
            $formname = "add_new_region";              
            $title = " Add Region";               
            $location = "";
            $room_category = "";              
            $price="";
            $price_dollar = "";
            $custom_action = '';
            $submit_action = "add_new_region";
            $submit_bt_value = "Add Region";
            $countrycode='';
            $allowedit = '';
            $dbid = '';
            $noticetype= '';
		}
		if($type == 'edit-map') {
			global $wpdb;
			$tablename=$wpdb->prefix ."woocommerce_custom_region_by_country";
			$region_dbdata = $wpdb->get_row("SELECT * FROM $tablename WHERE id = '$id'", ARRAY_A);
            $title = "Update Region";
            $message = "You can edit your Region from here.";
            $formname = "edit_region";
            $dbid = $region_dbdata['id'];
            $countrycode = $region_dbdata['countrycode'];
			$regionlist = $region_dbdata['regionlist'];															
            $submit_action = "edit_region";
            $submit_bt_value = "Update Region";
            $allowedit = 'disabled  readonly';
            $noticetype ='';
		}
		?>
	<?php if ($alert != "") { ?> 
		<div id="message_notice" class="updated notice <?php echo esc_html( __( $noticetype, 'cmetric-arbyw' ) ); ?> is-dismissible"><p>
			<?php echo esc_html( __( $alert, 'cmetric-arbyw' ) );?>
		</p>
		</div>
	<?php } ?>
	<form method="post" action="#" id="<?php echo esc_html( __( $formname, 'cmetric-arbyw' ) );?>" name="<?php echo esc_html( __( $formname, 'cmetric-arbyw' ) );?>" />
		<?php if ($type == 'add-map') { ?>
		    <input type="hidden" name="action" value="<?php echo esc_html( __( $formname, 'cmetric-arbyw' ) ); ?>"/>
		<?php } else { ?>        
			<a href="?page=arbyw-region-by-country-page"><input type="button" class="button-primary" value="<?php echo esc_html( __( 'Back To Add Region', 'cmetric-arbyw' ) ); ?>" /></a>
			<input type="hidden" name="action" value="<?php echo esc_html( __( $formname, 'cmetric-arbyw' ) ); ?>" />
		<?php } ?>
			<input type="hidden" name="id" value="<?php echo esc_html( __( $dbid, 'cmetric-arbyw' ) ); ?>" /> 		
		<table class="bookingprice_tlb">
		       <tr>
		         <td width="25%" valign="top"><h3><?php echo esc_html( __( $title, 'cmetric-arbyw' ) ); ?><!-- Booking Price Details --></h3>
		           <table class="form-table"  id="add-table">
		            <tr valign="top" class="form-field form-required">
		              <th scope="row">
						<label for="country_code"><?php echo esc_html( __( 'Country', 'cmetric-arbyw' ) );?> <span class="description"></span></label>
					  </th>
					  <td>
				   		<select name="<?php echo esc_html( __( 'country_code', 'cmetric-arbyw' ) );?>" <?php echo esc_html( __( $allowedit, 'cmetric-arbyw' ) );?> required>
				          <?php 
								global $woocommerce;
								$countries_obj   = new WC_Countries();
								$countries   = $countries_obj->__get('countries'); 
				          	 foreach ($countries as $key => $countries_val) { 
								if ( $key === $countrycode ){ $select_attribute = 'selected';   
								}else{ $select_attribute=''; } ?>
				              <option value="<?php echo $key; ?>" <?php echo $select_attribute; ?> ><?php echo esc_html( __($countries_val, 'cmetric-arbyw') ); ?></option>
							<?php  } //Close Foreach?>	
				        </select>
				      </td>
				     </tr>
				     <tr>
						<th><?php echo esc_html( __( 'Region List', 'cmetric-arbyw' ) );?></th>
	         			<td>
						<textarea name="<?php echo esc_html( __( 'region_list', 'cmetric-arbyw' ) );?>"  value=""  aria-required="true" required autocapitalize="none" autocorrect="off"  class="setting_region_text" cols="43" required placeholder="<?php echo esc_html( __( 'Add each Region per line', 'cmetric-arbyw' ) );?>"><?php echo !empty($regionlist) ? esc_html( __($regionlist, 'cmetric-arbyw') ) : ''; ?></textarea>
						</br>
						<span class="description"><?php echo esc_html( __( 'Add each Region per line', 'cmetric-arbyw' ) );?></span>
						</td>         				
	         		</tr>
					<tr>
						<th></th>
						<td>
				         <button class="button button-primary" data-tooltip="" id="save-regions" name="save-regions" value="<?php echo esc_html( __( $submit_bt_value, 'cmetric-arbyw' ) );?>" style="float:left;margin-bottom: 20px !important;"><?php echo esc_html( __( 'Save Settings', 'cmetric-arbyw' ) );?></button>
						</td>
			       </tr>
				  </table>
				</td>
			</tr>
		</table>
    </form>
    <?php }
    ?>
</div>
<?php 

	if(isset($_GET['message']) && isset($_GET['message'])){
		$get_message	= sanitize_text_field($_GET['message']);
	}
  if(isset($get_message) && ($get_message=="delete")){
	echo '<div class="notice notice-success is-dismissible"><p>' . __( 'Success! This Regions deleted Successfully.', 'cmetric-arbyw' ). '</p></div>';
	}
?>