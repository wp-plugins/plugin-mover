<?php
/**
Plugin Name: Plugin Mover
Description: A Plugin Mover.
Version: 1.0
Author: WP-Plugin-Dev.com
Author URI: http://www.wp-plugin-dev.com
Requires at least: 3.5
Tested up to: 4.0
Text Domain:   plugin-mover
Domain Path:   /

This Plugin is licensed under GPL

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.


Special use case if you need to move plugins to a different folder from the WordPress backend.

Ressources and help came from
http://stackoverflow.com/questions/23541269/how-to-add-custom-bulk-actions-in-wordpress-list-tables
http://stackoverflow.com/questions/8446247/how-to-move-one-directory-to-another-directory-in-php
http://twitter.com/der_kronn




*/


include("plugin-mover-admin-page.php");
add_action( 'admin_footer-plugins.php', 'bulk_footer_plugin_mover' );
add_action( 'admin_action_plugin_mover', 'bulk_request_plugin_mover' );
add_action('admin_menu', 'add_plugin_move_page');
 
function bulk_footer_plugin_mover() {
$plugin_mover_dir=get_option("plugin_mover_directory");
if ($plugin_mover_dir!=""){
    # global $typenow; if( $typenow != 'page' ) return; // if used on edit.php screen
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('<option>').val('plugin_mover').text('<?php _e("Move to ","plugin-move"); echo $plugin_mover_dir; ?>')
                .appendTo("select[name='action'], select[name='action2']");
        });
    </script>
    
<?php
    }
    else{}
}

function bulk_request_plugin_mover() {
	$checked=$_POST['checked'];
	
	foreach ($checked as $thisPlugin){
		$plugin_mover_dir=get_option("plugin_mover_directory");
		$url = get_home_path()."wp-content/";
		$plugin_directory=explode("/",$thisPlugin);
		$old_folder_name = $plugin_directory[0];
		$new_folder_name = $plugin_directory[0];
		
		$oldfolderpath = $url."/plugins/".$old_folder_name;
		$newfolderpath = $url."/".$plugin_mover_dir."/".$new_folder_name;
				
		rename($oldfolderpath,$newfolderpath);
		
		$msg = __("Plugin ","plugin-mover").$plugin_directory[0].__(" successfully moved to ","plugin-mover").$plugin_mover_dir;
		add_action( 'admin_notices', array(
			new WPPluginMoverMessenger($thisPlugin, $msg),
			'adminMessage'
		));
	}
}

class WPPluginMoverMessenger {
	public function __construct($plugin, $msg) {
		$this->plugin = $plugin;
		$this->msg    = $msg;
	}
	
	public function adminMessage() {
    ?>
    <div id="message" class="updated">
		<p><?php echo $this->msg; ?></p>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				$('input[value="<?php echo $this->plugin; ?>"]').parents('tr').remove();
			});
		</script>
    </div>
    <?php
	}
}



function add_plugin_move_page(){
add_management_page( __("Plugin Mover","plugin-mover"), __("Plugin Mover","plugin-mover"), 'manage_options', 'pluginmove', 'plugin_move_page' );
}



?>