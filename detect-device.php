<?php


class WonderPlugin_Cond_Plugin {

	function __construct() {

		$this->init();
	}
	
	function init() {
						
		add_shortcode( 'wonderplugin_cond', array($this, 'shortcode_handler') );
		
		add_filter('widget_text', 'do_shortcode');
		
		add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array($this, 'modify_plugin_action_links') );
	}
	
	function shortcode_handler($atts, $content = null) {
		
		$show = true;
		
		// device include
		if ( isset($atts['deviceinclude']) )
		{
			$show = false;
			
			$deviceinclude = explode(',', strtolower($atts['deviceinclude']));
			foreach($deviceinclude as $device)
			{
				if ($this->check_device($device))
				{
					$show = true;
					break;
				}
			}
			
			if (!$show)
				return;
		}
		
		// device exclude
		if ( isset($atts['deviceexclude']) )
		{				
			$deviceexclude = explode(',', strtolower($atts['deviceexclude']));
			foreach($deviceexclude as $device)
			{
				if ($this->check_device($device))
				{
					$show = false;
					break;
				}
			}
				
			if (!$show)
				return;
		}
		
		// browser include
		if ( isset($atts['browserinclude']) )
		{
			$show = false;
				
			$browserinclude = explode(',', strtolower($atts['browserinclude']));
			foreach($browserinclude as $browser)
			{
				if ($this->check_browser($browser))
				{
					$show = true;
					break;
				}
			}
				
			if (!$show)
				return;
		}
		
		// browser exclude
		if ( isset($atts['browserexclude']) )
		{
			$browserexclude = explode(',', strtolower($atts['browserexclude']));
			foreach($browserexclude as $browser)
			{
				if ($this->check_browser($browser))
				{
					$show = false;
					break;
				}
			}
		
			if (!$show)
				return;
		}
			
		if ( isset($atts['starttime']) )
		{
			if ( current_time( 'timestamp' ) < strtotime( $atts['starttime'] ) )	
				$show = false;	
			
			if (!$show)
				return;
		}

		if ( isset($atts['endtime']) )
		{
			if ( current_time( 'timestamp' ) > strtotime( $atts['endtime'] ) )	
				$show = false;	

			if (!$show)
				return;
		}

		if ($show)
			return do_shortcode($content);
		
	}
	
	function check_device($device)
	{
		if (!isset($_SERVER['HTTP_USER_AGENT']))
			return false;
		
		$matched = false;
		switch ($device)
		{
			case 'ipod':
				$matched = (strpos($_SERVER['HTTP_USER_AGENT'], 'iPod') !== false);
				break;
			case 'iphone':
				$matched = (strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') !== false);
				break;
			case 'ipad':
				$matched = (strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') !== false);
				break;
			case 'android':
				$matched = (strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false);
				break;
			case 'ios':
				$matched = (strpos($_SERVER['HTTP_USER_AGENT'], 'iPod') !== false
						|| strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') !== false
						|| strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') !== false);
				break;
			case 'mobile':
				$matched = (strpos($_SERVER['HTTP_USER_AGENT'], 'iPod') !== false 
						|| strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') !== false 
						|| strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') !== false 
						|| strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false 
						|| strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false
						|| strpos($_SERVER['HTTP_USER_AGENT'], 'Silk/') !== false
						|| strpos($_SERVER['HTTP_USER_AGENT'], 'Kindle') !== false
						|| strpos($_SERVER['HTTP_USER_AGENT'], 'BlackBerry') !== false
						|| strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== false
						|| strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mobi') !== false);
				break;
			case 'windows':
				$matched = (strpos($_SERVER['HTTP_USER_AGENT'], 'Windows') !== false);
				break;
			case 'mac':
				$matched = (strpos($_SERVER['HTTP_USER_AGENT'], 'Mac') !== false);
				break;
			case 'linux':
				$matched = (strpos($_SERVER['HTTP_USER_AGENT'], 'Linux') !== false);
				break;
		}
		
		return $matched;
	}
	
	function check_browser($browser)
	{
		if (!isset($_SERVER['HTTP_USER_AGENT']))
			return false;
		
		$matched = false;
		switch ($browser)
		{
			case 'chrome':
				$matched = ((strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') !== false
						|| strpos($_SERVER['HTTP_USER_AGENT'], 'CriOS') !== false)
							&& strpos($_SERVER['HTTP_USER_AGENT'], 'Edge') === false);
				break;
			case 'firefox':
				$matched = (strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox') !== false);
				break;
			case 'safari':
				$matched = (strpos($_SERVER['HTTP_USER_AGENT'], 'Safari') !== false
						&& strpos($_SERVER['HTTP_USER_AGENT'], 'CriOS') === false
						&& strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') === false
						&& strpos($_SERVER['HTTP_USER_AGENT'], 'Edge') === false);
				break;
			case 'edge':
				$matched = (strpos($_SERVER['HTTP_USER_AGENT'], 'Edge') !== false);
				break;
			case 'opera':
				$matched = (strpos($_SERVER['HTTP_USER_AGENT'], 'Opera') !== false 
						|| strpos($_SERVER['HTTP_USER_AGENT'], 'OPR/') !== false);				
				break;
			case 'ie6':
				$matched = (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6') !== false);
				break;
			case 'ie7':
				$matched = (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 7') !== false);
				break;
			case 'ie8':
				$matched = (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 8') !== false);
				break;
			case 'ie9':
				$matched = (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 9') !== false);
				break;
			case 'ie10':
				$matched = (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 10') !== false);
				break;
			case 'ie11':
				$matched = (strpos($_SERVER['HTTP_USER_AGENT'], 'Trident/7') !== false &&
						strpos($_SERVER['HTTP_USER_AGENT'], 'rv:11') !== false);
				break;
			case 'ie':
				$matched = (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false 
						|| (strpos($_SERVER['HTTP_USER_AGENT'], 'Trident/7') !== false 
							&& strpos($_SERVER['HTTP_USER_AGENT'], 'rv:11') !== false));
				break;
		}
		
		return $matched;
	}	
	
	function modify_plugin_action_links( $links ) {
		
		$links[] = '<a href="https://www.wonderplugin.com/wordpress-conditional-display-for-mobile/#tutorial" target="_blank">Online Tutorial</a>';
		
		return $links;
	}
}

$wonderplugin_cond_plugin = new WonderPlugin_Cond_Plugin();

// PHP API
function wonderplugin_is_device($device_list)
{	
	global $wonderplugin_cond_plugin;
	
	$matched = false;
	
	$devices = explode(',', strtolower($device_list));
	
	foreach($devices as $device)
	{		
		if ($wonderplugin_cond_plugin->check_device($device))
		{
			$matched = true;
			break;
		}
	}
	
	return $matched;
}

function wonderplugin_is_browser($browser_list)
{
	global $wonderplugin_cond_plugin;
	
	$matched = false;
	
	$browsers = explode(',', strtolower($browser_list));
	
	foreach($browsers as $browser)
	{
		if ($wonderplugin_cond_plugin->check_browser($browser))
		{
			$matched = true;
			break;
		}
	}
	
	return $matched;
}
