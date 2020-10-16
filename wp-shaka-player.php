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
 
 
 /**
 * Never worry about cache again!
 */
function my_load_scripts($hook) {
 
    // create my own version codes
    $my_js_ver  = date("ymd-Gis", filemtime( plugin_dir_path( __FILE__ ) . 'js/custom.js' ));
    $my_css_ver = date("ymd-Gis", filemtime( plugin_dir_path( __FILE__ ) . 'style.css' ));
     
	
	wp_register_script( 'shaka-cdn', '//cdnjs.cloudflare.com/ajax/libs/shaka-player/3.0.4/shaka-player.compiled.js', null, null, true );
	wp_enqueue_script ('shaka-cdn');
		wp_register_script( 'shaka-ui-cdn', '//cdnjs.cloudflare.com/ajax/libs/shaka-player/3.0.4/shaka-player.ui.min.js', null, null, true );
	wp_enqueue_script ('shaka-ui-cdn');
	wp_register_style( 'shaka-cdn', '//cdnjs.cloudflare.com/ajax/libs/shaka-player/3.0.4/controls.min.css' );
	wp_enqueue_style ('shaka-cdn');
	wp_register_style( 'fa-cdn', '//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css' );
	wp_enqueue_style ('fa-cdn');
	
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
	
	//if ( wonderplugin_is_device('iPhone,iPad') )
		//{
			//$link = $args['m3u8'];
		//}
	
	//if ( wonderplugin_is_device('Mobile') )
	//{
		//$link = $args['mpd'];
	//}

	//if ( wonderplugin_is_device('iPhone,iPad') )
	//{
		//$link = $args['m3u8'];
	//}


	//if ( wonderplugin_is_browser('Chrome') )
	//{
		//$link = $args['url_android'];
	//}



	?>
<style>
#video-container {
  display: inline-block;
  position: relative;
  text-align: center;
  color: white;
}
#video-container p {
    position: absolute;
    /* top: 50%; */
    /* left: 50%; */
    bottom: 8px;
    right: 16px;
    transform: translate(-50%, -50%);
    font-size: 50px;
    opacity: 0.10;
}

</style>

<script defer src="https://github.com/videojs/mux.js/releases/latest/download/mux.js"></script>
<div id="video-container" align="center">
	<p class="custom-message"><?php echo get_current_user_id(); ?></p>
	<video id="video" width="100%" poster="<?php echo $args['poster']; ?>" <?php echo $autoplays; ?> ><p class="custom-message"><?php echo get_current_user_id(); ?></p></video>
</div>
<!-- <button onclick="var el=document.getElementById('video-container'); el.requestFullscreen();">Click here</button> -->
	<script>

	
		
		const manifestUri =
			'<?php echo $link; ?>';


		function initApp() {
		  // Install built-in polyfills to patch browser incompatibilities.
		  shaka.polyfill.installAll();
		
		  // Check to see if the browser supports the basic APIs Shaka needs.
		  if (shaka.Player.isBrowserSupported()) {
			// Everything looks good!
			initPlayer();
		  } else {
			// This browser does not have the minimum set of APIs we need.
			console.error('Browser not supported!');
		  }
		}
		
		async function initPlayer() {
		  // Create a Player instance.
		  const video = document.getElementById('video');
		  // Get a reference to the video container (a simple div which wraps the video)
		  const videoContainer = document.getElementById('video-container');;
		  // Construct a player
		  const player = new shaka.Player(video);
		  // Construct the UI overlay
		  const ui = new shaka.ui.Overlay(player, videoContainer, video);
		  const controls = ui.getControls();	
		
		  // Attach player to the window to make it easy to access in the JS console.
		  window.player = player;

		 video.addEventListener('fullscreenchange', (event) => {
			  if (document.fullscreenElement) {
	
				console.log(`Element: ${document.fullscreenElement.id} entered fullscreen mode.`);

			  } else {
				console.log('Leaving full-screen mode.');
			  }
			});

		  // Listen for error events.
		  player.addEventListener('error', onErrorEvent);
		
		  // Try to load a manifest.
		  // This is an asynchronous process.
		  try {
			await player.load(manifestUri);
			// This runs if the asynchronous load is successful.
			console.log('The video has now been loaded!');
			  console.log(window);
		  } catch (e) {
			// onError is executed if the asynchronous load fails.
			onError(e);
		  }
		}
		
		function onErrorEvent(event) {
		  // Extract the shaka.util.Error object from the event.
		  onError(event.detail);
		}
		
		function onError(error) {
		  // Log the error.
		  console.error('Error code', error.code, 'object', error);
		}
		
		document.addEventListener('DOMContentLoaded', initApp);
	</script>
	<?php
	return ob_get_clean();
}
add_shortcode('shaka-player', 'shaka_player_shortcode');
