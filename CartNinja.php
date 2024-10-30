<?php
/*
Plugin Name: CartNinja - A Wordpress Shopping Cart Plugin
Plugin URI: http://cartninja.com/wordpress/
Description: CartNinja is a Wordpress Shopping Cart Plugin that is extremely flexible, lightweight, and conversion driven.  Perfect for selling any type of solid goods, digital downloads, anything you want to sell, you can sell it with Cart Ninja!  Add product details to Posts, Pages, or Custom Post Types for ultimate flexibility and improved workflow.
Version: 1.0
Author: CartNinja Programming Team
Author URI: http://cartninja.com/
License: GPL2
 */

/*  Copyright 2012  CartNinja.com  (email : support@cartninja.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
$CNCSS = get_option('CNCSS');
if($CNCSS){ add_action('wp_enqueue_scripts', 'addCartNinjaCSS'); }
function addCartNinjaCSS() {
	wp_register_style( 'prefix-style', plugins_url('AddToCartStyles.css', __FILE__) );
	wp_enqueue_style( 'prefix-style' );
}



class CN_Product_Post_Type {
  public function __construct() {
	  //$this->register_post_type();
	  $this->metaboxes();
  }

  public function register_post_type() {
	$args = array(
		'labels' => array(
			'name' => 'Products',
			'singular_name' => 'Product',
			'add_new' => 'Add New Product',
			'add_new_item' => 'Add New Product',
			'edit_item' => 'Edit Product',
			'new_item' => 'Add New Item',
			'view_item' => 'View Product',
			'search_items' => 'Search Products',
			'not_found' => 'No Products found',
			'not_found_in_trash' => 'No Products Found In Trash'
		),
		'query_var' => 'Site Products',
		'public' => true,
		'menu_position' => 5,
		'menu_icon' => plugin_dir_url( __FILE__ ) . 'CNIcon.png',
		'rewrite' => array(
			'slug' => 'product',
			'with_front' => FALSE
		)
	);
  	register_post_type('CN_Product', $args);
  }

  public function metaboxes() {
	add_action('add_meta_boxes', 'addProductMeta');
	function addProductMeta() {
		add_meta_box('CNProductDetails', 'Product Details - <small>Adding a Price Will Turn This Into a Product</small>', 'CNProductDetails');
	}

	function CNProductDetails($post) { 
		$productPrice = get_post_meta($post->ID, 'CNPrice', true);
		$productThumbnail = get_post_meta($post->ID, 'CNThumbnail', true);
		if(!$productThumbnail) { $productThumbnail = 'https://'; }
		$productOptions = get_post_meta($post->ID, 'CNOptions', true);
		$productDownload = get_post_meta($post->ID, 'CNDownload', true);
		$CNProductNameOR = get_post_meta($post->ID, 'CNProductNameOR', true);
?>

	<p>
<!-- Cart Ninja Product Details Box -->
<p align="center"><a href="http://cartninja.com/"><img src="<?php echo plugin_dir_url( __FILE__ ) . 'smallLogo.png'; ?>" alt="CartNinja.com Configuration" /></a></p>
	<p>
    		<label for="CNPrice"><b>Price:</b></label>
		<input type="text" name="CNPrice" id="CNPrice" value="<?php echo esc_attr($productPrice); ?>" /> <small>ex. 19.99</small>
	</p>	
	<p>
		<label for="CNThumbnail"><b>Product Thumbnail:</b> <small>(optional)</small> <small><b>Warning:</b> Ensure that the image you link to is secure (https://)</small></label>
		<input type="text" name="CNThumbnail" id="CNThumbnail" class="widefat" value="<?php echo esc_attr($productThumbnail); ?>" />
		<small><a class="dashboardLink" onclick="popup = window.open('https://cartninja.com/cart/Thumbnails.php','popup', 'height=600,width=800,scrollbars=yes,resizable=no,location=no'); return false" target="_blank" href="https://cartninja.com/cart/Thumbnails.php"> Click Here To Upload A Secure Thumbnail</a></small>
	</p>
	<p>
		<label for="CNOptions"><b>Product Options: <small>(optional)</small></b></label>
		<textarea name="CNOptions" id="CNOptions" class="widefat" style="height: 150px; display: block; clear: both;"><?php echo $productOptions; ?></textarea>
		<small><a onclick="popup = window.open('https://cartninja.com/dashboard/dojo/wordpressProductOptions.php','popup', 'height=600,width=800,scrollbars=yes,resizable=yes,location=yes'); return false" target="_blank" href="https://cartninja.com/dashboard/dojo/wordpressProductOptions.php">Click Here To Learn How To Add Options</a></small>
	</p>
	<p>
		<label for="CNDownload"><b>Downloadable Product Key:</b> <small>NOTE: Use only if this is a Downloadable Product</small></label><br/>
		<input type="text" name="CNDownload" id="CNDownload" class="widefat" value="<?php echo $productDownload; ?>" />
		<small><a onclick="popup = window.open('https://cartninja.com/cart/Digital.php','popup', 'height=600,width=800,scrollbars=yes,resizable=no,location=no'); return false" target="_blank" href="https://cartninja.com/cart/Digital.php">Click Here To Create A Digital Product Key</a></small>
	</p>
	<p>
		<label for="CNProductName"><b>Override Product Name:</b></label> (optional) <small>Product Name defaults to post title, but you can override it here</small>
		<input type="text" name="CNProductNameOR" id="CNProductNameOR" class="CNProductNameOR widefat" value="<?php echo $CNProductNameOR ?>" />
	</p>
	<p style="text-align: right; display: block;"><small><a onclick="popup = window.open('https://cartninja.com/cart/ProductCreator.php','popup', 'height=768,width=1024,scrollbars=yes,resizable=no,location=no'); return false" target="_blank" href="https://cartninja.com/cart/ProductCreator.php">Specify Minimum Price in Cart Ninja (optional)</a></small></p>
	
	<input type="submit" class="metabox_submit" value="Save" />
	<script>jQuery('.metabox_submit').click(function(e) { e.preventDefault(); jQuery('#publish').click(); });</script>	
	
		
		
	<?php
	}
	
	add_action('save_post', 'savePost');
	function savePost() {
		if(isset($_POST['CNPrice']) ) {
			$id = get_the_ID();
			update_post_meta( $id, 'CNPrice', strip_tags($_POST['CNPrice']) );
			update_post_meta( $id, 'CNThumbnail', strip_tags($_POST['CNThumbnail']) );
			update_post_meta( $id, 'CNOptions', $_POST['CNOptions'] );
			update_post_meta( $id, 'CNDownload', strip_tags($_POST['CNDownload']) );
			update_post_meta( $id, 'CNProductNameOR', strip_tags($_POST['CNProductNameOR']) );
		}
	}
  }

}

add_action('init', 'createMetaFields');
function createMetaFields() {
  new CN_Product_Post_Type();
}

/*if(get_option('CNOutputForm') != 'Shortcode') {
	add_filter('the_content', 'AddProduct'); // Calls the Add Product Function which adds product details to the end of the post
}*/

function AddProduct($content) {

ob_start();  /* Create a Buffer to Store The HTML In */

	if(is_singular()) {

	$productPrice = get_post_meta(get_the_ID(), 'CNPrice', true);
	if($productPrice) { // If user set the price display the product Form
			/* Get Product Details For Display */	
			$productOptions = get_post_meta(get_the_ID(), 'CNOptions', true);
			$productThumbnail = get_post_meta(get_the_ID(), 'CNThumbnail', true);
			if($productThumbnail == 'https://') { $productThumbnail = ''; }
			$CNUsername = get_option('CNUsername');
			$CNCurrency = get_option('CNCurrency');
			if(!$CNCurrency) { $CNCurrency = '$'; }
			$CNJS = get_option('CNJS');
			$productName = get_post_meta(get_the_ID(), 'CNProductNameOR', true);
			if(!$productName || $productName == '') { $productName = get_the_title(); }
			$CNDownload = trim(get_post_meta(get_the_ID(), 'CNDownload', true));
			$CNAddToCartText = get_option('CNAddToCartText');
			
?>
<!-- Cart Ninja Product Form -->
<?php if($CNJS){ add_action('wp_footer', 'addJavascript'); /* Adds Cart Ninja Javascript */ } ?>
<div class="CNProductFormDiv<?php if($CNOutputForm == 'Right'){ echo ' CNFormRight'; } if($CNOutputForm == 'Left') { echo ' CNFormLeft'; } ?>">
<form action="https://secure.cartninja.com" class="cartNinjaForm" method="POST" id="cartNinjaProduct">
      <input type="hidden" name="username" value="<?php echo $CNUsername; ?>">
      <input type="hidden" name="productName" value="<?php echo $productName; ?>">
      <input type="hidden" name="price" value="<?php echo $productPrice; ?>">
      <input type="hidden" name="img" value="<?php echo $productThumbnail; ?>">
      <?php if($CNDownload) { ?> <input type="hidden" name="download" value="<?php echo $CNDownload ?>" /> <?php } ?>      

    <fieldset class="CNFieldset">
      <div class="CNProductDetails">

	<b class="CNProductName"><?php echo $productName; ?></b> <span class="CNTooltip">Customize &amp; Order</span><br/>
	<b class="CNProductPrice"><span>Price:</span> <?php echo $CNCurrency; ?><em class="finalPrice"><?php echo $productPrice ?></em></b>
	<div class="CNQuantity"><label for="quantity">Quantity:</label> <input type="text" name="quantity" value="1" /></div>

      </div> <!-- End of Product Details -->

      <?php if($productOptions) { $hasOptions = true; // User Specified some Options so Output them ?>
		
	<div class="CNOptionsArea">

	        <p class="CNOptionsHeader"><strong>Options:</strong></p>
		<?php /*<pre><?php echo $productOptions ?></pre>*/ ?>

		<?php 
		$optionArray = explode('[]', $productOptions); 
		
		foreach($optionArray as $i=>$key) { // Loop through every Option Name
		?>
		<?php list($OptionName, $OptionTypeAndValues) = explode('|', $key); ?>
		<?php $OptionName = trim($OptionName); ?>
		<?php list($OptionType, $OptionValues) = explode("\n", $OptionTypeAndValues, 2); ?>
		<?php $OptionType = trim($OptionType); ?>

		<?php /* HANDLE OUTPUT FOR DROPDOWN OPTION TYPE */
		if($OptionType == 'Dropdown') { ?>
			<?php list($OptionValue, $OptionTip) = explode('>>', $OptionValues); ?>
			<div class="CNDropdown <?php echo CSSClass($OptionName); ?>"><label for="<?php echo cleanValues($OptionName) ?>"><?php echo $OptionName ?> <?php if($OptionTip) {?><span class="CNTooltip"><?php echo $OptionTip; ?></span><?php } ?></label>
				<select name="<?php echo cleanValues($OptionName) ?>" />
				<?php $theValue = explode("\n", $OptionValue) ?>
				<?php foreach($theValue as $i=>$key) { // Loop through each option ?>
					<?php if($key) { ?>
						<?php list($ValueName, $ValueCost) = explode('[', $key); ?>
						<?php $ValueName = trim($ValueName); $ValueCost = trim(str_replace(']', '', $ValueCost)); if(!$ValueCost){$ValueCost=0;} ?>
						<option value="<?php echo cleanValues($ValueName) . ' +'.$ValueCost; ?>" class="<?php echo CSSClass($ValueName); ?>"><?php echo $ValueName ?></option>
					<?php } ?>
				<?php } ?>
				</select>
			</div> <!-- end .CNDropDown -->
		<?php } /* END OUTPUT FOR A DROPDOWN TYPE */ ?>
		<?php /* HANDLE OUTPUT FOR RADIO OPTION TYPE */
		if($OptionType == 'Radio') { ?>
			<?php list($OptionValue, $OptionTip) = explode('>>', $OptionValues); ?>
			<?php $OptionValue=trim($OptionValue); $OptionTip = trim($OptionTip); ?>
					<div class="CNRadio <?php CSSClass($OptionName); ?>"><label for="<?php echo cleanValues($OptionName) ?>"><?php echo $OptionName ?> <?php if($OptionTip) {?><span class="CNTooltip"><?php echo $OptionTip; ?></span><?php } ?></label>
				<?php $theValue = explode("\n", $OptionValue) ?>
				<?php foreach($theValue as $i=>$key) { // Loop through each option ?>
					<?php if($key) { ?>
						<?php list($ValueName, $ValueCost) = explode('[', $key); ?>
						<?php $ValueName = trim($ValueName); $ValueCost = trim(str_replace(']', '', $ValueCost)); if(!$ValueCost){$ValueCost=0;} ?>
				<span class="CNRadioOption <?php echo CSSClass($ValueName); ?>">
					<input type="radio" name="<?php echo cleanValues($OptionName) ?>" <?php if($i==0) { echo 'checked'; } ?> value="<?php echo cleanValues($ValueName) . ' +'.$ValueCost; ?>" /> <?php echo $ValueName ?>
				</span>
					<?php } ?>
				<?php } ?>
			</div> <!-- end .CNRadio -->
		<?php } /* END OUTPUT FOR A RADIO TYPE */ ?>

		<?php /* HANDLE OUTPUT FOR A CHECKBOX */
		if($OptionType=='Checkbox') {
			list($OptionValue, $OptionTip) = explode('>>', $OptionValues);
			$OptionValue = trim($OptionValue); $OptionTip = trim($OptionTip);
			list($ValueCost) = explode("\n", $OptionValue);
		?>
			<div class="CNCheckbox <?php echo CSSClass($OptionName); ?>">
				<input type="checkbox" value="<?php echo 'Added +'.$ValueCost ?>" name="<?php echo cleanValues($OptionName); ?>" /> 
				<label for="<?php echo cleanValues($OptionName) ?>"><?php echo $OptionName ?></label> <?php if($OptionTip) {?>
				<span class="CNTooltip"><?php echo $OptionTip; ?></span><?php } ?> 
			</div>
		<?php
		} /* END OUTPUT FOR A CHECKBOX */
		?>
		<?php /* HANDLE OUTPUT FOR A TEXTBOX */
		if($OptionType=='Textbox') {
			list($OptionValue, $OptionTip) = explode('>>', $OptionValues);
			$OptionValue = trim($OptionValue); $OptionTip = trim($OptionTip);
			list($ValueCost) = explode("\n", $OptionValue);
		?>		

			<div class="CNText <?php echo CSSClass($OptionName); ?>"><label for="<?php echo cleanValues($OptionName); ?>"><?php echo $OptionName ?><?php if($OptionTip) {?><span class="CNTooltip"><?php echo $OptionTip; ?></span><?php } ?>
			</label> <input type="text" value="" name="<?php echo cleanValues($OptionName) ?>" /></div>
		
		
			<?php } /* END HANDLING OUTPUT FOR A TEXTBOX */ ?>

		<?php /* HANDLE OUTPUT FOR A TEXTAREA */
		if($OptionType=='Textarea') {
			list($OptionValue, $OptionTip) = explode('>>', $OptionValues);
			$OptionValue = trim($OptionValue); $OptionTip = trim($OptionTip);
			list($ValueCost) = explode("\n", $OptionValue);
		?>		

			<div class="CNTextarea <?php echo CSSClass($OptionName); ?>"><label for="<?php echo cleanValues($OptionName) ?>"><?php echo $OptionName ?><?php if($OptionTip) {?> <span class="CNTooltip"><?php echo $OptionTip; ?></span><?php } ?>
			</label> <textarea name="<?php echo cleanValues($OptionName); ?>"></textarea></div>
		
		
		<?php } /* END HANDLING OUTPUT FOR A TEXTAREA */ ?>

		<?php /* HANDLE OUTPUT FOR CUSTOM HTML */
		if($OptionType=='HTML') {
			list($OptionValue, $OptionTip) = explode('>>', $OptionValues);
			$OptionValue = trim($OptionValue); $OptionTip = trim($OptionTip);
			list($TheHTML) = explode("\n", $OptionValue);
		?>		

			<div class="CNHTML <?php echo CSSClass($OptionTip) ?>"><?php echo $TheHTML; ?></div>
		
		
		<?php } /* END HANDLING OUTPUT FOR A TEXTAREA */ ?>
				

	<?php } // Done Looping Through Product Options ?>
		
		
      
	</div> <!-- End of Options Area -->
	      

	<?php 
      }
?>
      <?php if($hasOptions) { ?><b class="CNProductPrice CNBottomFinalPrice"><span>Price:</span> <?php echo $CNCurrency ?><em class="finalPrice"><?php echo $productPrice ?></em></b><?php } ?>
	<?php
		if(!$CNAddToCartText) { $CNAddToCartText = 'Add To Cart'; }
	?>
      <input type="submit" value="<?php echo $CNAddToCartText ?>" name="CNAddToCart" class="CNAddToCart">

    </fieldset>
</form>
</div>

<!-- End of Cart Ninja Product Form -->

<?php
	$CNProductForm = ob_get_contents();
			ob_end_clean();
			return $CNProductForm;

	} // End Detecting if User Set Price




	} 
}

function addJavascript() { ?>
	<script type="text/javascript" src="<?php echo plugin_dir_url( __FILE__ ) . 'AddToCartScripts.js'; ?>"></script> 
<?php
}

function wpFeatures_register_scripts() { // Adds JQuery and proper CSS to the Theme
	wp_enqueue_script("jquery");
}

add_action('wp_print_scripts', 'wpFeatures_register_scripts');
add_shortcode('NinjaProduct', 'NinjaProduct');


/*Shortcode Tag*/
function NinjaProduct() {
	return AddProduct('');
}
function NinjaProductTag() {
	echo AddProduct('');
}



?>
<?php // add the admin options page to the wordpress menu
add_action('admin_menu', 'plugin_admin_add_page');
function plugin_admin_add_page() {
	//add_options_page('WP Features Options', 'WPFeatures', 'manage_options', 'plugin', 'plugin_options_page');
	add_menu_page( 'Cart Ninja Settings', 'CartNinja', 'manage_options', 'wp-cartninja-admin', 'wp_cartninja_administration', plugin_dir_url( __FILE__ ) . 'CNIcon.png' );
}
?>
<?php

function wp_cartninja_administration() {
?>
<style type="text/css">
  label {
    font-size: 18px;
    line-height: 20px;
 }
.odd {
  border-top: 1px solid #ddd;
  border-bottom: 1px solid #ddd;
padding: 10px;
display: block;
clear: both;
}
</style>

<div class="wrap" style="text-align: center;">

<p><a href="http://cartninja.com/" target="_blank"><img src="<?php echo plugin_dir_url( __FILE__ ) . 'CNLogo.png'; ?>" alt="CartNinja.com Configuration" /></a></p>

<?php

	if($_REQUEST['submit']){
		update_wp_cartninja();
	}
	if($_REQUEST['reset']){

	}
	print wp_cartninja_form();

?>

</div>

<?php
}

?>
<?php /* THE UPDATE INSERTION FORM */
function update_wp_cartninja(){

	//Clean up these variables to prevent SQL Injection
	//
	
	$CNUsername = mysql_real_escape_string($_REQUEST['CNUsername']);
	$CNOutputForm = mysql_real_escape_string($_REQUEST['CNOutputForm']);
	$CNCSS = mysql_real_escape_string($_REQUEST['CNCSS']);
	$CNJS = mysql_real_escape_string($_REQUEST['CNJS']);
	$CNCurrency = mysql_real_escape_string($_REQUEST['CNCurrency']);
	$CNAddToCartText = mysql_real_escape_string($_REQUEST['CNAddToCartText']);

	update_option('CNUsername', $CNUsername);
	update_option('CNOutputForm', $CNOutputForm);
	update_option('CNCSS', $CNCSS);
	update_option('CNJS', $CNJS);
	update_option('CNCurrency', $CNCurrency);
	update_option('CNAddToCartText', $CNAddToCartText);	

	$ok=true;

	if ($ok){ ?>

	<div id="message" class="updated fade">
		<p>Options saved successfully.</p>
	</div>
		
	<?php } else { ?>

		<div id="message" class="error fade">
			<p>Error: Failed to Save Options</p>
		</div>
	<?php }
}
?>
<?php 

/* Set Default Options on Activation */
register_activation_hook( __FILE__, 'plugin_activate' );
function plugin_activate() {
  update_option('CNCSS', 'CNCSS');
  update_option('CNJS', 'CNJS');
  update_option('CNOutputForm', 'End');
}
/* End Activation Function */

function wp_cartninja_form() {  // The form where they can input data

	/* Set Default Values */
	$CNCurrency = '$';
	$CNCSS = 'CNCSS';
	$CNJS = 'CNJS';
	$CNOutputForm = 'Beginning';

	/* Get Users Values (If Set) */
	if(get_option('CNCurrency')) { $CNCurrency = get_option('CNCurrency'); }
	if(get_option('CNCSS') == '') { $CNCSS = get_option('CNCSS'); }
	if(get_option('CNJS') == '') { $CNJS = get_option('CNJS'); }
	if(get_option('CNOutputForm')) { $CNOutputForm = get_option('CNOutputForm'); }
	if(get_option('CNUsername')) { $CNUsername = get_option('CNUsername'); }
	if(get_option('CNAddToCartText')) { $CNAddToCartText = get_option('CNAddToCartText'); }
?>
<div style="width: 500px; margin: 0 auto; text-align: left;">
<form method="post">

<h3>Cart Ninja Plugin Settings</h3>

<p align="center">
	<label for="CNUsername"><b>Cart Ninja Username</b></label><br/>
	<input type="text" name="CNUsername" value="<?php echo $CNUsername ?>" maxlength="15" style="font-size: 18px; padding: 5px;" />
</p>
<p align="center">Don't have a Cart Ninja Username? <a href="http://cartninja.com/dashboard/register.php" style="font-weight: bold; font-size: 12px;" target="_blank">Click Here To Create An Account</a></p>

<div class="odd">
  <label for="CNOutputForm"><b>How to display your product</b></label>
  <div style="margin: 0 0 0 15px;">
	You can either use a Template Tag <b>&lt;?php NinjaProductTag(); ?&gt;</b> or the Shortcode <b>[NinjaProduct]</b>
 </div>
 <small>Controls where the your product form (add to cart) will be displayed</small>
</div>

<p>
<input type="checkbox" name="CNCSS" id="CNCSS" value="CNCSS" <?php if($CNCSS){ echo 'checked'; } ?> /> <label for="CNCSS">Include CartNinja CSS?</label> <br/>
<input type="checkbox" name="CNJS" id="CNJS" value="CNJS" <?php if($CNJS){ echo 'checked'; } ?> /> <label for="CNJS">Include CartNinja Javascript?</label><br/>
<small>You can opt to remove CartNinja Javascript and/or CSS if you want to do advanced customization</small>
</p>

<p><b>Currency Symbol: </b> <input type="text" name="CNCurrency" id="CNCurrency" value="<?php echo $CNCurrency ?>" style="width: 25px;"/></p>

<p><b>Add To Cart Text:</b> (optional)<br/>
<input type="text" name="CNAddToCartText" value="<?php echo $CNAddToCartText; ?>" name="CNAddToCartText" id="CNAddToCartText" /><br/>
<small>Change "Add To Cart" to something else (example: Add To Bag, Buy Now, or Add To Satchel etc..)</small></p>

	<p align="center"><input type="submit" name="submit" value="Save Settings" class="button-primary" style="font-size: 25px;" /></p>

</form>
</div>
<p style="float: left;"><a href="http://cartninja.com/dashboard/" target="_blank"><img src="<?php echo plugin_dir_url( __FILE__ ) . 'dojoDashboard.png'; ?>" style="border: 1px solid #ddd;" /></a></p>
<p style="float: right;"><a href="http://cartninja.com/dojo/" target="_blank"><img src="<?php echo plugin_dir_url( __FILE__ ) . 'gettingStarted.png'; ?>" style="border: 1px solid #ddd;" /></a></p>

<?php
  
}

function cleanValues($value) {
	$value = str_replace('+', '', $value);
	$value = htmlentities($value);
  $value = mysql_real_escape_string($value);
  return $value;
}
function CSSClass($value) {
	$pattern = '/.-?[_a-zA-Z]+[_a-zA-Z0-9-]*/s*/{';
	$value = str_replace(' ', '', $value);
	$value = preg_replace('/[^a-z0-9]+/i', '', $value);
	return $value;
}

?>
