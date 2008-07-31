<?php

/***************************************************************************

Plugin Name:  Blog Stats by W3Counter
Plugin URI:   http://www.w3counter.com/resources/wordpress-plugin
Description:  Displays statistics for your blog recorded by W3Counter.
Version:      1.0.2
Author:       Dan Grossman
Author URI:   http://www.dangrossman.info

**************************************************************************/

class W3Counter {

	function W3Counter() {
	
		add_action('wp_dashboard_setup', array(&$this, 'register_widget'));
		add_filter('wp_dashboard_widgets', array(&$this, 'add_widget'));
        register_sidebar_widget(array('W3Counter Tracker', 'widgets'), array(&$this, 'widget_sidebar'));
        register_widget_control(array('W3Counter Tracker', 'widgets'), array(&$this, 'widget_sidebar_control'));
        
	}
	
	function register_widget() {
	
		wp_register_sidebar_widget('w3counter', 
			__('Blog Stats by W3Counter', 'w3counter'), 
			array(&$this, 'widget'), 
			array('all_link' => 'http://www.w3counter.com/stats/signin', 'width' => 'full')
		);
		
		wp_register_widget_control('w3counter', 
			__('Blog Stats by W3Counter', 'w3counter'), 
			array(&$this, 'widget_control'), 
			array(), 
			array('widget_id' => 'w3counter')
		);
		
	}
	
	function add_widget($widgets) {
	
		global $wp_registered_widgets;
		
		if (!isset($wp_registered_widgets['w3counter']) ) return $widgets;
		array_splice($widgets, 0, 0, 'w3counter');
		
		return $widgets;
		
	}
	
    function widget($args = null) {
    
    	if (!empty($args))
        	extract($args, EXTR_SKIP);

        echo $before_widget;

        echo $before_title;
        echo $widget_name;
        echo $after_title;

        if (!$widget_options = get_option('dashboard_widget_options' ))
                $widget_options = array();

        if (!isset($widget_options[$widget_id]))
                $widget_options[$widget_id] = array();

        if (!isset($widget_options[$widget_id]['id'])) {
                echo "<b>Blog Stats by W3Counter</b> has not yet been configured. Click the edit link above.";
        } else {
        
        		$id = $widget_options[$widget_id]['id'];
    			$key = md5($widget_options[$widget_id]['password']);
    			    		
                ?>

                <script type="text/javascript">
                /* <![CDATA[ */
                jQuery( function($) {
                        var w3counter = $('#w3counter div.dashboard-widget-content');
                        var h = parseInt( w3counter.parent().height() ) - parseInt( w3counter.prev().height()) - 20;
                        var w = w3counter.width();
                        w3counter.html('<iframe src="http://www.w3counter.com/stats/wp-dashboard/<?php echo $id; ?>?key=<?php echo $key; ?>&width=' + w.toString() + '" style="width: ' + w.toString() + 'px; height: ' + h.toString() + 'px; border: none; margin: 0; padding: 0" frameborder="0"></iframe>');
                } );
                /* ]]> */
                </script>

                <?php
        }

        echo $after_widget;
        
    }
	
	function widget_control($args) {
	
		extract($args, EXTR_SKIP);
		
		$widget_id = 'w3counter';
		
		if (!$widget_options = get_option('dashboard_widget_options'))
			$widget_options = array();

		if (!isset($widget_options[$widget_id]))
			$widget_options[$widget_id] = array();

		if ('POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['widget-w3counter'])) {
			$widget_options[$widget_id] = stripslashes_deep($_POST['widget-w3counter']);												
			update_option('dashboard_widget_options', $widget_options);
		}
		
		echo "<p><label for='w3counter-id'>";
		_e('What is your W3Counter website ID?', 'w3counter');
		echo "</label><br /><input type='text' name='widget-w3counter[id]' size='4' value='" . $widget_options[$widget_id]['id'] . "'></p>";
		
		echo "<p><label for='w3counter-password'>If you did not enable public stats when adding your website, then what is your W3Counter password?</label>";
		echo "<br /><input type='password' name='widget-w3counter[password]' size='10' value='" . $widget_options[$widget_id]['password'] . "'></p>";
		
		
	}
	
	function widget_sidebar($args) {
	
	        extract($args, EXTR_SKIP);
	
	        $widget_id = 'w3counter';
	
	        if (!$widget_options = get_option('dashboard_widget_options'))
	                $widget_options = array();
	
	        if (!isset($widget_options[$widget_id]))
	                $widget_options[$widget_id] = array();
	
	        if ($widget_options[$widget_id]['align'] == 'center') {
	                echo "<div style='text-align: center; margin: 0; padding: 0'>";
	        }
	
	        echo $before_widget;
	
	        ?>
<!-- Begin W3Counter Tracking Code -->
<script type="text/javascript" src="http://www.w3counter.com/tracker.js"></script>
<script type="text/javascript">
<?php
if (!empty($_COOKIE['comment_author_' . COOKIEHASH])) {
        echo "_w3counter_label = '" . $_COOKIE['comment_author_' . COOKIEHASH] . "';\n";
}
?>
w3counter(<?php echo $widget_options[$widget_id]['id']; ?>);
</script>
<noscript>
<div><a href="http://www.w3counter.com"><img src="http://www.w3counter.com/tracker.php?id=<?php echo $widget_options[$widget_id]['id']; ?>" style="border: 0" alt="W3Counter Web Stats" /></a></div>
</noscript>
<!-- End W3Counter Tracking Code-->
	        <?php
	
	        if ($widget_options[$widget_id]['align'] == 'center') {
	                echo "</div>";
	        }
	
	        echo $after_widget;
	
	}
	
    function widget_sidebar_control($args = null) {
    
    		if (!empty($args))
            	extract($args, EXTR_SKIP);

            $widget_id = 'w3counter';

            if (!$widget_options = get_option('dashboard_widget_options'))
                    $widget_options = array();

            if (!isset($widget_options[$widget_id])) {
                    $widget_options[$widget_id] = array();
                    $widget_options[$widget_id]['align'] = 'left';
            }

            if ('POST' == $_SERVER['REQUEST_METHOD'] && isset($_POST['widget-w3counter'])) {
                    if (!isset($_POST['widget-w3counter']['align'])) {
                            $_POST['widget-w3counter']['align'] = 'left';
                    } else {
                            $_POST['widget-w3counter']['align'] = 'center';
                    }
                    $widget_options[$widget_id] = stripslashes_deep($_POST['widget-w3counter']);
                    update_option('dashboard_widget_options', $widget_options);
            }

            echo "<p><label for='w3counter-id'>";
            _e('What is your W3Counter website ID?', 'w3counter');
            echo "</label><br /><input type='text' name='widget-w3counter[id]' size='4' value='" . $widget_options[$widget_id]['id'] . "'></p>";

            echo "<p><label for='w3counter-align'>";
            _e('Center the tracking image?', 'w3counter');
            echo "</label><br /><input type='checkbox' name='widget-w3counter[align]' ";
            if ($widget_options[$widget_id]['align'] == 'center')
                    echo "checked='checked'";
            echo "/> Yes</p>";
            
    }

	
}

add_action('plugins_loaded', create_function('', 'global $w3counter; if (empty($w3counter)) $w3counter = new W3Counter();'));
add_action('widgets_init', create_function('', 'global $w3counter; if (empty($w3counter)) $w3counter = new W3Counter();'));

?>