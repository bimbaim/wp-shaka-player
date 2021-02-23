<?php
/**
 * Plugin Name:       WP Shaka Video Player
 * Plugin URI:        #
 * Description:       Use shortcode [shaka-player mpd='URL video for android' m3u8='URL video for IOS/safari' poster='Image poster']
 * Version:           0.5
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Baim Quraisy
 * Author URI:        #
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wpshaka
 * Domain Path:       /languages
 */

 if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
 
include( plugin_dir_path( __FILE__ ) . 'detect-device.php');
 /**
 * Never worry about cache again!
 */
function my_load_scripts($hook) {
 
    // create my own version codes
    $my_js_ver  = date("ymd-Gis", filemtime( plugin_dir_path( __FILE__ ) . 'js/custom.js' ));
    $my_css_ver = date("ymd-Gis", filemtime( plugin_dir_path( __FILE__ ) . 'style.css' ));
     
	
	wp_register_script( 'shaka-cdn', plugins_url( 'js/shaka-player.compiled.js', __FILE__ ), array('jquery'), '3.0.4', true );
	wp_enqueue_script ('shaka-cdn');
	wp_register_script( 'shaka-ui-cdn',  plugins_url( 'js/shaka-player.ui.min.js', __FILE__ ), array('jquery'), '3.0.4', true );
	wp_enqueue_script ('shaka-ui-cdn');
	wp_register_style( 'shaka-cdn', plugins_url( 'css/controls.min.css', __FILE__ ) );
	wp_enqueue_style ('shaka-cdn');
	
    wp_enqueue_script( 'custom_js', plugins_url( 'js/custom.js', __FILE__ ), array('jquery'), $my_js_ver );
    wp_register_style( 'my_css',    plugins_url( 'style.css',    __FILE__ ), false,   $my_css_ver );
    wp_enqueue_style ( 'my_css' );
 
}
add_action('wp_enqueue_scripts', 'my_load_scripts');


function shaka_player_shortcode($atts){
ob_start();
	$args = shortcode_atts( 
		array(
			'm3u8'   => '',
			'mpd' => '',
			'poster' => '',
			'autoplay' => 'no'
		), 
		$atts
	);
	
		
	
	if($args['autoplay'] == 'yes'){
			$autoplays = 'autoplay';
	}else{
			$autoplays = $args['autoplay'];
	}
	
	$link = $args['mpd'];
	
	
	if ( wonderplugin_is_device('Mobile') )
	{
		$link = $args['mpd'];
	}

	if ( wonderplugin_is_device('iPhone,iPad') )
	{
		$link = $args['m3u8'];
	}

?>

<script defer src="https://github.com/videojs/mux.js/releases/latest/download/mux.js"></script>
<video data-shaka-player id="video" style="width:100%;height:100%" poster="<?php echo $args['poster']; ?>" <?php echo $autoplays; ?>></video>
<script>
const manifestUri =
    '<?php echo $link; ?>';


async function init() {
    // When using the UI, the player is made automatically by the UI object.
    const video = document.getElementById('video');
    const ui = video['ui'];
    const controls = ui.getControls();
    const player = controls.getPlayer();

    // Attach player and ui to the window to make it easy to access in the JS console.
    window.player = player;
    window.ui = ui;

    // Listen for error events.
    player.addEventListener('error', onPlayerErrorEvent);
    controls.addEventListener('error', onUIErrorEvent);

    // Try to load a manifest.
    // This is an asynchronous process.
    try {
        await player.load(manifestUri);
        // This runs if the asynchronous load is successful.
        console.log('The video has now been loaded!');
    } catch (error) {
        onPlayerError(error);
    }
}

function onPlayerErrorEvent(errorEvent) {
    // Extract the shaka.util.Error object from the event.
    onPlayerError(event.detail);
}

function onPlayerError(error) {
    // Handle player error
    console.error('Error code', error.code, 'object', error);
}

function onUIErrorEvent(errorEvent) {
    // Extract the shaka.util.Error object from the event.
    onPlayerError(event.detail);
}

function initFailed(errorEvent) {
    // Handle the failure to load; errorEvent.detail.reasonCode has a
    // shaka.ui.FailReasonCode describing why.
    console.error('Unable to load the UI library!');
}

// Listen to the custom shaka-ui-loaded event, to wait until the UI is loaded.
document.addEventListener('shaka-ui-loaded', init);
// Listen to the custom shaka-ui-load-failed event, in case Shaka Player fails
// to load (e.g. due to lack of browser support).
document.addEventListener('shaka-ui-load-failed', initFailed);
</script>

	<?php
	return ob_get_clean();
}
add_shortcode('shaka-player', 'shaka_player_shortcode');
