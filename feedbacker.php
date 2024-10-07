<?php
/**
 * Feedbacker
 *
 * @package       FEEDBACKER
 * @author        Antacid
 * @license       gplv2
 * @version       1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:   Feedbacker
 * Plugin URI:    https://graphql.gateway-api.stream/
 * Description:   Short description of Feedbacker plugin
 * Version:       1.0.0
 * Author:        Antacid
 * Author URI:    https://author-web-site.gateway-api.stream/
 * Text Domain:   feedbacker
 * Domain Path:   /languages
 * License:       GPLv2
 * License URI:   https://www.gnu.org/licenses/gpl-2.0.html
 *
 * You should have received a copy of the GNU General Public License
 * along with Feedbacker. If not, see <https://www.gnu.org/licenses/gpl-2.0.html/>.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * HELPER COMMENT START
 * 
 * This file contains the main information about the plugin.
 * It is used to register all components necessary to run the plugin.
 * 
 * The comment above contains all information about the plugin 
 * that are used by WordPress to differenciate the plugin and register it properly.
 * It also contains further PHPDocs parameter for a better documentation
 * 
 * The function FEEDBACKER() is the main function that you will be able to 
 * use throughout your plugin to extend the logic. Further information
 * about that is available within the sub classes.
 * 
 * HELPER COMMENT END
 */

// Plugin name
define( 'FEEDBACKER_NAME',			'Feedbacker' );

// Plugin version
define( 'FEEDBACKER_VERSION',		'1.0.0' );

// Plugin Root File
define( 'FEEDBACKER_PLUGIN_FILE',	__FILE__ );

// Plugin base
define( 'FEEDBACKER_PLUGIN_BASE',	plugin_basename( FEEDBACKER_PLUGIN_FILE ) );

// Plugin Folder Path
define( 'FEEDBACKER_PLUGIN_DIR',	plugin_dir_path( FEEDBACKER_PLUGIN_FILE ) );

// Plugin Folder URL
define( 'FEEDBACKER_PLUGIN_URL',	plugin_dir_url( FEEDBACKER_PLUGIN_FILE ) );

/**
 * Load the main class for the core functionality
 */
require_once FEEDBACKER_PLUGIN_DIR . 'core/class-feedbacker.php';

/**
 * The main function to load the only instance
 * of our master class.
 *
 * @author  Antacid
 * @since   1.0.0
 * @return  object|Feedbacker
 */
function FEEDBACKER() {
	return Feedbacker::instance();
}

FEEDBACKER();
