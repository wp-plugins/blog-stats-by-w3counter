<?php

/***************************************************************************

Plugin Name:  Blog Stats by W3Counter
Plugin URI:   http://www.w3counter.com/resources/wordpress-plugin
Description:  Display stats for your blog on the dashboard and add the tracking code to your site with a widget
Version:      2.0
Author:       Dan Grossman
Author URI:   http://www.dangrossman.info

**************************************************************************/

function w3counter_init() {
        add_action('admin_menu', 'w3counter_config_page');
        add_action('admin_menu', 'w3counter_stats_page');
}
add_action('init', 'w3counter_init');

function w3counter_admin_init() {
        if ( function_exists( 'get_plugin_page_hook' ) )
                $hook = get_plugin_page_hook( 'w3counter-stats-display', 'index.php' );
        else
                $hook = 'dashboard_page_w3counter-stats-display';
        add_action('admin_head-'.$hook, 'w3counter_stats_script');
}
add_action('admin_init', 'w3counter_admin_init');

function w3counter_config_page() {
        if ( function_exists('add_submenu_page') )
                add_submenu_page('options-general.php', __('W3Counter'), __('W3Counter'), 'manage_options', 'w3counter-config', 'w3counter_conf');

}

function w3counter_stats_page() {
        if ( function_exists('add_submenu_page') )
                add_submenu_page('index.php', __('W3Counter Stats'), __('W3Counter Stats'), 'manage_options', 'w3counter-stats-display', 'w3counter_stats_display');

}

function w3counter_stats_script() {
        ?>
	<script type="text/javascript">
	function resizew3Iframe() {
	    var height = document.documentElement.clientHeight;
	    height -= document.getElementById('w3counter-stats-frame').offsetTop;
	    height += 100; // magic padding
	
	    document.getElementById('w3counter-stats-frame').style.height = height +"px";
	
	};
	function resizew3IframeInit() {
	        document.getElementById('w3counter-stats-frame').onload = resizew3Iframe;
	        window.onresize = resizew3Iframe;
	}
	addLoadEvent(resizew3IframeInit);
	</script><?php
}


function w3counter_stats_display() {
	
	$w3counter_id = get_option('w3counter_id');
	$w3counter_user = get_option('w3counter_user');
	$w3counter_pass = get_option('w3counter_pass');

	if (empty($w3counter_user)) {

		echo "<div class='wrap'><p>You must configure the plugin first. Click W3Counter under the Settings menu.</p></div>";
		
	} else {

		$url = 'http://www.w3counter.com/stats/' . $w3counter_id . '?wordpress=1&username=' . $w3counter_user . '&password=' . $w3counter_pass;
	
        	?>
       		<div class="wrap">
        	<iframe src="<?php echo $url; ?>" width="100%" height="100%" frameborder="0" id="w3counter-stats-frame"></iframe>
        	</div>
        	<?php

	}
}

function w3counter_conf() {

	if (isset($_POST['w3counter_id'])) {
		update_option('w3counter_id', $_POST['w3counter_id']);
		update_option('w3counter_user', $_POST['w3counter_user']);
		update_option('w3counter_pass', $_POST['w3counter_pass']);
	}
	$w3counter_id = get_option('w3counter_id');
	$w3counter_user = get_option('w3counter_user');
	$w3counter_pass = get_option('w3counter_pass');
?>

<div class="wrap">
        <div id="icon-options-general" class="icon32"><br /></div>

        <h2>W3Counter Configuration</h2>

        <form method="post" action="">

		<?php if (isset($_POST['w3counter_id'])) echo 'Changes saved.<br />'; ?>
	
                <table class="form-table">
                        <tr valign="top">
				<th scope="row"><label>Your Website ID:</label></th>
				<td>
					<input type="text" name="w3counter_id" value="<?php echo $w3counter_id; ?>" />
				</td>
			</tr>
			<tr>
				<th></th>
				<td>
					Your website ID is the number you see in the address bar when viewing your web stats at <a href="http://www.w3counter.com/stats/" target="_blank">W3Counter.com</a>. 
					The image below shows where you can find it. Enter the number for the website associated with this blog.<br /><br />
					<img src="http://www.w3counter.com/stats/images/plugin.png" />
				</td>
			</tr>
                        <tr valign="top">
                                <th scope="row"><label>Your W3Counter Username:</label></th>
                                <td>
                                        <input type="text" name="w3counter_user" value="<?php echo $w3counter_user; ?>" />
                                </td>
                        </tr>
			<tr>
				<th scope="row"><label>Your W3Counter Password:</label></th>
				<td>
					<input type="password" name="w3counter_pass" value="<?php echo $w3counter_pass; ?>" />
                                </td>
                        </tr>
		</table>

		<br /><br />
		<input type="submit" value="Save Changes" class="button" />

	</form>
</div>

<?php 
}

class WP_Widget_W3Counter extends WP_Widget {

        function WP_Widget_W3Counter() {
		$widget_ops = array('classname' => 'WP_Widget_W3Counter', 'description' => __( 'Adds the W3Counter tracking code to your sidebar') );
                $this->WP_Widget('w3counter', __('W3Counter'), $widget_ops);
        }

        function widget( $args, $instance ) {
                extract($args);
		
		$w3counter_id = get_option('w3counter_id');

                echo $before_widget;
                echo '<div id="w3counter_wrap">';
?>
<!-- Begin W3Counter Tracking Code -->
<script type="text/javascript" src="http://www.w3counter.com/tracker.js"></script>
<script type="text/javascript">
<?php
if (!empty($_COOKIE['comment_author_' . COOKIEHASH])) {
        echo "_w3counter_label = '" . $_COOKIE['comment_author_' . COOKIEHASH] . "';\n";
}
?>
w3counter(<?php echo $w3counter_id; ?>);
</script>
<noscript>
<div><a href="http://www.w3counter.com"><img src="http://www.w3counter.com/tracker.php?id=<?php echo $w3counter_id; ?>" style="border: 0" alt="W3Counter" /></a></div>
</noscript>
<!-- End W3Counter Tracking Code-->
<?php
                echo '</div>';
                echo $after_widget;
        }

        function update( $new_instance, $old_instance ) {
                $instance = $old_instance;
                return $instance;
        }

        function form( $instance ) {
	}
}

add_action('widgets_init', 'w3counter_widget_init');
function w3counter_widget_init() {
	register_widget('WP_Widget_W3Counter');
}

?>