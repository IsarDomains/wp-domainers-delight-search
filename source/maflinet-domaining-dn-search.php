<?php
/*
Plugin Name: mafli.net - DN Search Widget
Plugin URI:  http://mafli.net/wordpress-plugin-dn-search/
Description: Search Sedo, Uniregistry or GoDaddy database for domain names (including Affiliate Ids)
Author: IsarDomains.com
Author URI: http://isardomains.com
Version: 0.1
Text Domain: maflinet-dn-search
*/

// Register MaFliNet_DNSearch_Widget widget
function register_maflinet_dnsearchfield_widget() {
    register_widget( 'MaFliNet_DNSearch_Widget' );
}

// Include the JS file that contains neccessary code
function maflinet_dnsearchfield_enqueue_script(){
	wp_enqueue_style('maflinet_css', plugin_dir_url( __FILE__ ) . 'css/maflinet.css');
    wp_enqueue_script( 'maflinet_js_script', plugin_dir_url( __FILE__ ) . 'js/maflinet.js', '1.0' );
}

// Provide the translation
function maflinet_dnsearchfield_translation() {
	load_plugin_textdomain( 'maflinet-dn-search', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');
}

// The code that prepares the shortcode 
function maflinet_dnsearch_shortcode($params = [], $content="") {
	global $serviceProviderList;
	global $languageList;
	//merge values with defaults
     $atts = shortcode_atts(array(
        'service' => 'sedo',
		'open'    => '_blank',
		'affiliateid' => '',
		'showplaceholdertext'  => 'false',
		'placeholdertext'      => '',
		'disableenterkey'       => 'false',
		'hidesearchbutton'     => 'false',
		'searchbuttonlabel'    => __( 'Search', 'maflinet-dn-search' ),
		'sedowebsitelanguage'  => '',
		'sedowebsitesearchtlds'  => '',
		'sedowebsiteshowadult'  => 'false',
		'sedowebsitesynonyms'  => 'true'
    ), $params);
		
	//parse the inputs to check valid values
	$okSedoShowAdult = $atts['sedowebsiteshowadult'];
	if($okSedoShowAdult == "true" || $okSedoShowAdult =="1"){ $okSedoShowAdult = "true"; }
	
	$okService = $atts['service'];
	if(!array_key_exists($okService, $serviceProviderList)){ $okService = $serviceProviderList[0]; } else { $okService = "sedo"; }
	
	$okLanguage = $atts['sedowebsitelanguage'];
	if(!array_key_exists($okLanguage, $languageList)){ $okLanguage = $languageList[0]; }
	
	$displaySettings = array();
	$displaySettings['targetSite'] = $okService;
	$displaySettings['openResultInTarget'] = $atts['open'];
	$displaySettings['affiliateId'] = $atts['affiliateid'];
	$displaySettings['description'] = $content;
	$displaySettings['showPlaceholderText'] = $atts['showplaceholdertext'];
	$displaySettings['placeholderText'] = $atts['placeholdertext'];
	$displaySettings['disableEnterKey'] = $atts['disableenterkey'];
	$displaySettings['hideSearchButton'] = $atts['hidesearchbutton'];
	$displaySettings['searchButtonLabel'] = $atts['searchbuttonlabel'];
	$displaySettings['sedoWebsiteLanguage'] = $okLanguage;
	$displaySettings['sedoWebsiteSafeSearch'] = $okSedoShowAdult;
	$displaySettings['sedoWebsiteSearchVariations'] = $atts['sedowebsitesynonyms'];
	$displaySettings['sedoWebsiteSearchTLDs'] = $atts['sedowebsitesearchtlds'];
	
	displayDNSearchField($displaySettings);
		
}
//Global serviceProviderList
$serviceProviderList = array( 'sedo' =>'https://sedo.com/search/?keyword=', 
							  'uniregistry' => 'http://ap.uniregistry.com/click?s=',
							  'godaddy' => 'https://in.godaddy.com/dpp/find?domainToCheck=');

//Global sedo search result page languages
$languageList = array('' => 'Browser default',
						'd' => 'German',
						'e' => 'English',
						'es' => 'Spanish',
						'fr' => 'French',
						'cn' => 'Chinese',
						'br' => 'Brasil',
						'it' => 'Italian',
						'nl' => 'Dutch');

//Global open search result window
$openSearchResultList = array('_blank'  => 'New Window / Tab (_blank)',
							  '_parent' => 'Parent Window (_parent)',
							  '_self'   => 'Same Window (_self)',
							  '_top'    => 'Top Window (_top)');

// Hook up all related stuff
add_action('widgets_init', 'register_maflinet_dnsearchfield_widget' );
add_action('wp_enqueue_scripts', 'maflinet_dnsearchfield_enqueue_script');
add_action('plugins_loaded', 'maflinet_dnsearchfield_translation');
add_shortcode('dnsearch', 'maflinet_dnsearch_shortcode');

class MaFliNet_DNSearch_Widget extends WP_Widget {

	protected $defaults;

/**
 * Register widget with WordPress.
 */
	function __construct() {

		$this->defaults = array(
			'title'				   => '',
			'openResultInTarget'    => '_blank',
			'affiliateId'        => '',
			'description'          => '',
			'showPlaceholderText'  => 'false',
			'placeholderText'      => '',
			'disableEnterKey'       => 'false',
			'hideSearchButton'     => 'false',
			'searchButtonLabel'    => __( 'Search', 'maflinet-dn-search' ),
			'targetSite'			=> 'sedo',
			'sedoWebsiteLanguage'  => '',
			'sedoWebsiteSearchTLDs'  => '',
			'sedoWebsiteSafeSearch'  => 'false',
			'sedoWebsiteSearchVariations'  => 'true'
		);
		
		$widget_ops = array(
			'classname'   => 'user-profile',
			'description' => __( 'DN Search: Search Sedo, Uniregistry or GoDaddy to find domains.', 'maflinet-dn-search' ),
		);

		$control_ops = array(
			'id_base' => 'user-profile',
			'width'   => 200,
			'height'  => 250,
		);

		parent::__construct( 'user-profile', __( 'mafli.net - DN Search Widget', 'maflinet-dn-search' ), $widget_ops, $control_ops );

	}

/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	function widget( $args, $instance ) {

		extract( $args );

		/** Merge with defaults */
		$instance = wp_parse_args( (array) $instance, $this->defaults );
		//$serviceProviderList = wp_parse_args( (array) $instance, $this->serviceProviderList );

		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}
		displayDNSearchField($instance);
		echo $args['after_widget'];
	}
		
	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, $this->defaults );
		global $languageList;
		global $openSearchResultList;
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'maflinet-dn-search'); ?>:</label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($instance['title']); ?>" />
		</p>
		<fieldset>
			<p>
				<label for="<?php echo $this->get_field_id( 'description' ); ?>"><?php _e( 'Description', 'maflinet-dn-search' ); ?>:</label>
				<textarea class="widefat" rows="8" id="<?php echo $this->get_field_id('description'); ?>" name="<?php echo $this->get_field_name('description'); ?>"><?php echo $instance["description"]; ?></textarea>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'targetSite' ); ?>"><?php _e( 'Search Service Provider', 'maflinet-dn-search' ); ?>:</label>
				<select name="<?php echo $this->get_field_name('targetSite'); ?>" id="<?php echo $this->get_field_id('targetSite'); ?>" class="widefat">
					<option value="sedo" <?php selected( $instance['targetSite'], 'sedo' ); ?>><?php _e('Sedo.com', 'maflinet-dn-search'); ?></option>
					<option value="uniregistry" <?php selected( $instance['targetSite'], 'uniregistry' ); ?>><?php _e('Uniregistry.com', 'maflinet-dn-search'); ?></option>
					<option value="godaddy" <?php selected( $instance['targetSite'], 'godaddy' ); ?>><?php _e('GoDaddy.com', 'maflinet-dn-search'); ?></option>
				</select>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'affiliateId' ); ?>"><?php _e( 'Service Provider Affiliate Id', 'maflinet-dn-search' ); ?>:</label>
				<input type="text" id="<?php echo $this->get_field_id( 'affiliateId' ); ?>" name="<?php echo $this->get_field_name( 'affiliateId' ); ?>" value="<?php echo esc_attr( $instance['affiliateId'] ); ?>" class="widefat" maxlength="15"/>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'openResultInTarget' ); ?>"><?php _e( 'Open Search Result in', 'maflinet-dn-search' ); ?>:</label>
				<select name="<?php echo $this->get_field_name('openResultInTarget'); ?>" id="<?php echo $this->get_field_id('openResultInTarget'); ?>" class="widefat">
					<?php foreach ($openSearchResultList as $lang => $description) { ?>
					<option value="<?=$lang?>" <?php selected( $instance['openResultInTarget'], $lang ); ?>><?php _e($description, 'maflinet-dn-search'); ?></option>
					<?php } ?>
				</select>
			</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'showPlaceholderText' ); ?>"><?php _e( 'Show Placeholder', 'maflinet-dn-search' ); ?>:</label>
			<select name="<?php echo $this->get_field_name('showPlaceholderText'); ?>" id="<?php echo $this->get_field_id('showPlaceholderText'); ?>" class="widefat">
				<option value="false" <?php selected( $instance['showPlaceholderText'], 'false' ); ?>><?php _e('No', 'maflinet-dn-search'); ?></option>
				<option value="true" <?php selected( $instance['showPlaceholderText'], 'true' ); ?>><?php _e('Yes', 'maflinet-dn-search'); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'placeholderText' ); ?>"><?php _e( 'Placeholder Text', 'maflinet-dn-search' ); ?>:</label>
			<input type="text" id="<?php echo $this->get_field_id( 'placeholderText' ); ?>" name="<?php echo $this->get_field_name( 'placeholderText' ); ?>" value="<?php echo esc_attr( $instance['placeholderText'] ); ?>" class="widefat" maxlength="20" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'disableEnterKey' ); ?>"><?php _e( 'Disable Enter Key To Start Search', 'maflinet-dn-search' ); ?>:</label>
			<select name="<?php echo $this->get_field_name('disableEnterKey'); ?>" id="<?php echo $this->get_field_id('disableEnterKey'); ?>" class="widefat">
				<option value="false" <?php selected( $instance['disableEnterKey'], 'false' ); ?>><?php _e('No', 'maflinet-dn-search'); ?></option>
				<option value="true" <?php selected( $instance['disableEnterKey'], 'true' ); ?>><?php _e('Yes', 'maflinet-dn-search'); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'hideSearchButton' ); ?>"><?php _e( 'Hide Searchbutton', 'maflinet-dn-search' ); ?>:</label>
			<select name="<?php echo $this->get_field_name('hideSearchButton'); ?>" id="<?php echo $this->get_field_id('hideSearchButton'); ?>" class="widefat">
				<option value="false" <?php selected( $instance['hideSearchButton'], 'false' ); ?>><?php _e('No', 'maflinet-dn-search'); ?></option>
				<option value="true" <?php selected( $instance['hideSearchButton'], 'true' ); ?>><?php _e('Yes', 'maflinet-dn-search'); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'searchButtonLabel' ); ?>"><?php _e( 'Label Searchbutton', 'maflinet-dn-search' ); ?>:</label>
			<input type="text" id="<?php echo $this->get_field_id( 'searchButtonLabel' ); ?>" name="<?php echo $this->get_field_name( 'searchButtonLabel' ); ?>" value="<?php echo esc_attr( $instance['searchButtonLabel'] ); ?>" class="widefat" />
		</p>
		</fieldset>
		<hr/>
		<p><?php _e( 'The following options are only relevant if <strong>Sedo.com</strong> is selected as service provider.', 'maflinet-dn-search'); ?></p>
		<fieldset>
		<p>
			<label for="<?php echo $this->get_field_id( 'sedoWebsiteSearchTLDs' ); ?>"><?php _e( 'Search Extensions (TLDs)', 'maflinet-dn-search' ); ?>:</label>
			<input type="text" id="<?php echo $this->get_field_id( 'sedoWebsiteSearchTLDs' ); ?>" name="<?php echo $this->get_field_name( 'sedoWebsiteSearchTLDs' ); ?>" value="<?php echo esc_attr( $instance['sedoWebsiteSearchTLDs'] ); ?>" class="widefat" placeholder="Example: com,de,net,club" />
		</p>		
		<p>
			<label for="<?php echo $this->get_field_id( 'sedoWebsiteLanguage' ); ?>"><?php _e('Open Sedo Result Page In Language', 'maflinet-dn-search'); ?>:</label>
			<select name="<?php echo $this->get_field_name('sedoWebsiteLanguage'); ?>" id="<?php echo $this->get_field_id('sedoWebsiteLanguage'); ?>" class="widefat">
				<?php foreach ($languageList as $prefix => $translation) { ?>
					<option value="<?=$prefix?>" <?php selected( $instance['sedoWebsiteLanguage'], $prefix ); ?>><?php _e($translation,'maflinet-dn-search');?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'sedoWebsiteSafeSearch' ); ?>"><?php _e( 'Include Adult Domains', 'maflinet-dn-search' ); ?>:</label>
			<select name="<?php echo $this->get_field_name('sedoWebsiteSafeSearch'); ?>" id="<?php echo $this->get_field_id('sedoWebsiteSafeSearch'); ?>" class="widefat">
				<option value="1" <?php selected( $instance['sedoWebsiteSafeSearch'], '1' ); ?>><?php _e('No', 'maflinet-dn-search'); ?></option>
				<option value="2" <?php selected( $instance['sedoWebsiteSafeSearch'], '2' ); ?>><?php _e('Yes', 'maflinet-dn-search'); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'sedoWebsiteSearchVariations' ); ?>"><?php _e( 'Use Synonym Search', 'maflinet-dn-search' ); ?>:</label>
			<select name="<?php echo $this->get_field_name('sedoWebsiteSearchVariations'); ?>" id="<?php echo $this->get_field_id('sedoWebsiteSearchVariations'); ?>" class="widefat">
				<option value="false" <?php selected( $instance['sedoWebsiteSearchVariations'], 'false' ); ?>><?php _e('No', 'maflinet-dn-search'); ?></option>
				<option value="true" <?php selected( $instance['sedoWebsiteSearchVariations'], 'true' ); ?>><?php _e('Yes', 'maflinet-dn-search'); ?></option>
			</select>
		</p>
		</fieldset>
		<?php
	}

 /**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] = (!empty( $new_instance['title'])) ? strip_tags($new_instance['title']) : '';		
		$instance['targetSite'] = (!empty( $new_instance['targetSite'])) ? strip_tags( $new_instance['targetSite'] ): $old_instance['targetSite'];
		$instance['openResultInTarget'] = (!empty( $new_instance['openResultInTarget'])) ? strip_tags( $new_instance['openResultInTarget']) : $old_instance['openResultInTarget'];
		$instance['affiliateId']   = (!empty( $new_instance['affiliateId'])) ? strip_tags( $new_instance['affiliateId'] ): '';
		$instance['description'] = (!empty( $new_instance['description'])) ? ( $new_instance['description'] ): '';
		$instance['showPlaceholderText'] = (!empty( $new_instance['showPlaceholderText'])) ? strip_tags( $new_instance['showPlaceholderText'] ): $old_instance['showPlaceholderText'];
		$instance['placeholderText'] = (!empty( $new_instance['placeholderText'])) ? strip_tags( $new_instance['placeholderText'] ): $old_instance['placeholderText'];
		$instance['disableEnterKey'] = (!empty( $new_instance['disableEnterKey'])) ? strip_tags( $new_instance['disableEnterKey'] ): $old_instance['disableEnterKey'];
		$instance['hideSearchButton'] = (!empty( $new_instance['hideSearchButton'])) ? strip_tags( $new_instance['hideSearchButton'] ): $old_instance['hideSearchButton'];
		$instance['searchButtonLabel'] = (!empty( $new_instance['searchButtonLabel'])) ? strip_tags( $new_instance['searchButtonLabel'] ): $old_instance['searchButtonLabel'];
		$instance['sedoWebsiteLanguage'] = (!empty( $new_instance['sedoWebsiteLanguage'])) ? strip_tags( $new_instance['sedoWebsiteLanguage'] ): '';
		$instance['sedoWebsiteSafeSearch'] = (!empty( $new_instance['sedoWebsiteSafeSearch'])) ? strip_tags( $new_instance['sedoWebsiteSafeSearch'] ): $old_instance['sedoWebsiteSafeSearch'];
		$instance['sedoWebsiteSearchVariations'] = (!empty( $new_instance['sedoWebsiteSearchVariations'])) ? strip_tags( $new_instance['sedoWebsiteSearchVariations'] ): $old_instance['sedoWebsiteSearchVariations'];
		$instance['sedoWebsiteSearchTLDs'] = (!empty( $new_instance['sedoWebsiteSearchTLDs'])) ? ( $new_instance['sedoWebsiteSearchTLDs'] ): '';

		return $instance;

	}

}

 /**
  * Function: displayDNSearchField
  *	Display the search field in the frontend based on specified parameter
  *
  * @param array $instance Values that drives the behavior.
  * @param array $serviceProviderList The current available list of supported sites.
  *
  * @return HTML code to display the field.
  */
function displayDNSearchField($instance){

	global $serviceProviderList;
	//Create a unique id to prevent issues if widget used multiple times on a single page...
	$uniqueIdFormFields = md5(uniqid(rand(), true));

	//Get all available settings:
	$targetSite				= $instance['targetSite'];
	$openResultInTarget 	= $instance['openResultInTarget'];
	$affiliateId 			= $instance['affiliateId'];
	$description			= $instance['description'];
	$showPlaceholderText	= $instance['showPlaceholderText'];
	$placeholderText		= $instance['placeholderText'];
	$disableEnterKey		= $instance['disableEnterKey'];
	$hideSearchButton		= $instance['hideSearchButton'];
	$searchButtonLabel		= $instance['searchButtonLabel'];
	$sedoWebsiteLanguage	= $instance['sedoWebsiteLanguage'];
	$sedoSafeSearch			= $instance['sedoWebsiteSafeSearch'];
	$sedoSearchVariations	= $instance['sedoWebsiteSearchVariations'];
	$sedoSearchTLDs			= $instance['sedoWebsiteSearchTLDs'];
	
	$baseAffiliateURL = $serviceProviderList[$targetSite];
	
	$placeholder = "";
	if ("true" == $showPlaceholderText){
		$placeholder = $placeholderText;
	}

	$css = "maflinet_searchfield_left";
	if($hideSearchButton=="true"){ 
		$css = "maflinet_searchfield_full";
	}
	?>
	<div id="maflinet_dn_search_<?=$uniqueIdFormFields?>" class="maflinet_dn_search">
		<div id="maflinet_dn_search_description_<?=$uniqueIdFormFields?>" class="maflinet_dn_search_description">
			<?=$description;?>
		</div>
		<div id="maflinet_search_wrapper_<?=$uniqueIdFormFields?>" class="maflinet_searchfield_wrapper">			
				<div class="<?=$css?>" id="maflinet_search_wrapper_left_<?=$uniqueIdFormFields?>">
					<input id="maflinet_sedosearch_domainname_<?=$uniqueIdFormFields?>" name="maflinet_sedosearch_domainname_<?=$uniqueIdFormFields?>" required value="" maxlength="128" type="text" <?php if("false" == $disableEnterKey){ ?> onKeyPress="if (event.keyCode==13){searchDomains('<?=$uniqueIdFormFields?>', '<?=$targetSite?>', '<?=$baseAffiliateURL?>','<?=$affiliateId?>', '<?=$sedoWebsiteLanguage?>', '<?=$sedoSafeSearch?>', '<?=$sedoSearchVariations?>', '<?=$sedoSearchTLDs?>', '<?=$openResultInTarget?>');}" <?php } ?>placeholder="<?=$placeholder?>">
				</div> <!-- maflinet_search_wrapper_left -->
				<?php if($hideSearchButton=="false"){ ?>				
					<div class="maflinet_searchfield_right" id="maflinet_search_wrapper_right_<?=$uniqueIdFormFields?>">				
						<input type="button" value="<?=$searchButtonLabel?>" onClick="searchDomains('<?=$uniqueIdFormFields?>', '<?=$targetSite?>', '<?=$baseAffiliateURL?>', '<?=$affiliateId?>', '<?=$sedoWebsiteLanguage?>', '<?=$sedoSafeSearch?>', '<?=$sedoSearchVariations?>', '<?=$sedoSearchTLDs?>', '<?=$openResultInTarget?>');" id="maflinet_sedosearch_submit_<?=$uniqueIdFormFields?>" name="maflinet_sedosearch_submit_<?=$uniqueIdFormFields?>">
					</div> <!-- maflinet_searchfield_right -->
				<?php } ?>
			<div class="maflinet_searchfield_error_hidden" id="maflinet_sedosearch_error_<?=$uniqueIdFormFields?>" name="maflinet_sedosearch_error_<?=$uniqueIdFormFields?>">
				<div class="maflinet_searchfield_error"><?php _e('At least 1 character is required!', 'maflinet-dn-search'); ?></div>
			</div> <!-- maflinet_searchfield_error_hidden -->
		</div> <!-- maflinet_search_wrapper -->
	</div><!-- maflinet_dn_search -->
	<?php
}
