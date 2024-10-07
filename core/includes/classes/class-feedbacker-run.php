<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * HELPER COMMENT START
 * 
 * This class is used to bring your plugin to life. 
 * All the other registered classed bring features which are
 * controlled and managed by this class.
 * 
 * Within the add_hooks() function, you can register all of 
 * your WordPress related actions and filters as followed:
 * 
 * add_action( 'my_action_hook_to_call', array( $this, 'the_action_hook_callback', 10, 1 ) );
 * or
 * add_filter( 'my_filter_hook_to_call', array( $this, 'the_filter_hook_callback', 10, 1 ) );
 * or
 * add_shortcode( 'my_shortcode_tag', array( $this, 'the_shortcode_callback', 10 ) );
 * 
 * Once added, you can create the callback function, within this class, as followed: 
 * 
 * public function the_action_hook_callback( $some_variable ){}
 * or
 * public function the_filter_hook_callback( $some_variable ){}
 * or
 * public function the_shortcode_callback( $attributes = array(), $content = '' ){}
 * 
 * 
 * HELPER COMMENT END
 */

/**
 * Class Feedbacker_Run
 *
 * Thats where we bring the plugin to life
 *
 * @package		FEEDBACKER
 * @subpackage	Classes/Feedbacker_Run
 * @author		Antacid
 * @since		1.0.0
 */
class Feedbacker_Run{

	/**
	 * Our Feedbacker_Run constructor 
	 * to run the plugin logic.
	 *
	 * @since 1.0.0
	 */
	function __construct(){
		$this->add_hooks();
	}

	/**
	 * ######################
	 * ###
	 * #### WORDPRESS HOOKS
	 * ###
	 * ######################
	 */

	/**
	 * Registers all WordPress and plugin related hooks
	 *
	 * @access	private
	 * @since	1.0.0
	 * @return	void
	 */
	private function add_hooks(){
	
		add_action( 'plugin_action_links_' . FEEDBACKER_PLUGIN_BASE, array( $this, 'add_plugin_action_link' ), 20 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_backend_scripts_and_styles' ), 20 );
		add_action( 'plugins_loaded', array( $this, 'add_wp_webhooks_integrations' ), 9 );
	
		add_action('admin_menu', array($this, 'feedbacker_add_admin_menu'));
		add_action('admin_init', array($this, 'register_chatgpt_api_key_setting'));
	}

	/**
	 * ######################
	 * ###
	 * #### WORDPRESS HOOK CALLBACKS
	 * ###
	 * ######################
	 */

	/**
	* Adds action links to the plugin list table
	*
	* @access	public
	* @since	1.0.0
	*
	* @param	array	$links An array of plugin action links.
	*
	* @return	array	An array of plugin action links.
	*/
	public function add_plugin_action_link( $links ) {

		$links['our_shop'] = sprintf( '<a href="%s" title="Custom Link" style="font-weight:700;">%s</a>', 'https://test.test', __( 'Custom Link', 'feedbacker' ) );

		return $links;
	}

	/**
	 * Enqueue the backend related scripts and styles for this plugin.
	 * All of the added scripts andstyles will be available on every page within the backend.
	 *
	 * @access	public
	 * @since	1.0.0
	 *
	 * @return	void
	 */
	public function enqueue_backend_scripts_and_styles() {
		wp_enqueue_style( 'feedbacker-backend-styles', FEEDBACKER_PLUGIN_URL . 'core/includes/assets/css/backend-styles.css', array(), FEEDBACKER_VERSION, 'all' );
		wp_enqueue_script( 'feedbacker-backend-scripts', FEEDBACKER_PLUGIN_URL . 'core/includes/assets/js/backend-scripts.js', array(), FEEDBACKER_VERSION, false );
		wp_localize_script( 'feedbacker-backend-scripts', 'feedbacker', array(
			'plugin_name'   	=> __( FEEDBACKER_NAME, 'feedbacker' ),
		));
	}

	/**
	 * ####################
	 * ### WP Webhooks 
	 * ####################
	 */

	/*
	 * Register dynamically all integrations
	 * The integrations are available within core/includes/integrations.
	 * A new folder is considered a new integration.
	 *
	 * @access	public
	 * @since	1.0.0
	 *
	 * @return	void
	 */
	public function add_wp_webhooks_integrations(){

		// Abort if WP Webhooks is not active
		if( ! function_exists('WPWHPRO') ){
			return;
		}

		$custom_integrations = array();
		$folder = FEEDBACKER_PLUGIN_DIR . 'core' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'integrations';

		try {
			$custom_integrations = WPWHPRO()->helpers->get_folders( $folder );
		} catch ( Exception $e ) {
			WPWHPRO()->helpers->log_issue( $e->getTraceAsString() );
		}

		if( ! empty( $custom_integrations ) ){
			foreach( $custom_integrations as $integration ){
				$file_path = $folder . DIRECTORY_SEPARATOR . $integration . DIRECTORY_SEPARATOR . $integration . '.php';
				WPWHPRO()->integrations->register_integration( array(
					'slug' => $integration,
					'path' => $file_path,
				) );
			}
		}
	}

	public function feedbacker_add_admin_menu() {
	    add_menu_page(
	        'Feedbacker',
	        'Feedbacker',
	        'manage_options',
	        'feedbacker',
	        array($this, 'feedbacker_admin_page'),
	        'dashicons-feedback',
	        20
	    );
	}

	public function feedbacker_admin_page() {
	    $modules = $this->get_modules_list();
	    $current_user_info = FEEDBACKER()->helpers->get_current_user_info();
	    $all_users_info = FEEDBACKER()->helpers->get_all_users_info();
	    $product_info = '';
	    $ai_review = '';
	    if (isset($_POST['publish_comment'])) {
	        $product_url = sanitize_text_field($_POST['product_url']);
	        if (strpos($product_url, 'epicentrk.ua') !== false) {
	            require_once FEEDBACKER_PLUGIN_DIR . 'modules/epicentrk.php';
	            $epicentrk = new Epicentrk_Module();
	            $product_info = $epicentrk->process_url($product_url);

	            require_once FEEDBACKER_PLUGIN_DIR . 'integrations/ai_review_generator.php';
	            $ai_generator = new AI_Review_Generator();
	            $ai_review = $ai_generator->generate_review($product_info);
	        } else {
	            $product_info = "URL не належить до epicentrk.ua";
	        }
	    }
	    // Решта коду залишається без змін
	    ?>
	    <div class="wrap">
	        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
	        <p>Ласкаво просимо до адміністративної панелі Feedbacker!</p>
	        
	        <h2>Інформація про поточного користувача:</h2>
	        <p>ID користувача: <?php echo $current_user_info['user_id']; ?></p>
	        <p>Статус підписки: <?php echo $current_user_info['subscription_status']; ?></p>
	        
	        <h2>Доступні модулі:</h2>
	        <ul>
	            <?php foreach ($modules as $module): ?>
	                <li><?php echo esc_html($module); ?></li>
	            <?php endforeach; ?>
	        </ul>
	        
	        <h2>Список всіх користувачів:</h2>
	        <table class="wp-list-table widefat fixed striped">
	            <thead>
	                <tr>
	                    <th>ID</th>
	                    <th>Email</th>
	                    <th>Статус підписки</th>
	                </tr>
	            </thead>
	            <tbody>
	                <?php foreach ($all_users_info as $user): ?>
	                    <tr>
	                        <td><?php echo $user['user_id']; ?></td>
	                        <td><?php echo $user['user_email']; ?></td>
	                        <td><?php echo $user['subscription_status']; ?></td>
	                    </tr>
	                <?php endforeach; ?>
	            </tbody>
	        </table>

	        <h2>Публікація коментаря</h2>
	        <form method="post" action="">
	            <input type="text" name="product_url" placeholder="URL посилання на сторінку товару" style="width: 300px;">
	            <input type="submit" name="publish_comment" value="Опублікувати коментар" class="button button-primary">
	            <span>Статус: <?php echo $this->get_comment_status(); ?></span>
	        </form>
	        <?php if (!empty($product_info)): ?>
	            <h3>Інформація про товар:</h3>
	            <textarea rows="10" cols="100" readonly><?php echo esc_textarea($product_info); ?></textarea>
	        <?php endif; ?>
	        <?php if (!empty($ai_review)): ?>
	            <h3>Згенерований ШІ відгук:</h3>
	            <textarea rows="10" cols="100" readonly><?php echo esc_textarea($ai_review); ?></textarea>
	        <?php endif; ?>
	    </div>
	    <?php
	}

	public function get_modules_list() {
	    $modules_dir = FEEDBACKER_PLUGIN_DIR . 'modules/';
	    $modules = array();
	    
	    if (is_dir($modules_dir)) {
	        $files = scandir($modules_dir);
	        foreach ($files as $file) {
	            if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
	                $modules[] = pathinfo($file, PATHINFO_FILENAME);
	            }
	        }
	    }
	    
	    return $modules;
	}

	private function get_comment_status() {
	    // Тут має бути логіка визначення статусу коментаря
	    // Наразі повертаємо заглушку
	    return "функціонал поки не працює";
	}

	public function register_chatgpt_api_key_setting() {
	    register_setting('general', 'chatgpt_api_key');
	    add_settings_field(
	        'chatgpt_api_key',
	        'ChatGPT API Key',
	        array($this, 'chatgpt_api_key_callback'),
	        'general'
	    );
	}

	public function chatgpt_api_key_callback() {
	    $api_key = get_option('chatgpt_api_key');
	    echo '<input type="text" name="chatgpt_api_key" value="' . esc_attr($api_key) . '" />';
	}

}