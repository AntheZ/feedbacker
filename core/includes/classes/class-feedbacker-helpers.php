<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class Feedbacker_Helpers
 *
 * This class contains repetitive functions that
 * are used globally within the plugin.
 *
 * @package		FEEDBACKER
 * @subpackage	Classes/Feedbacker_Helpers
 * @author		Antacid
 * @since		1.0.0
 */
class Feedbacker_Helpers{

	/**
	 * ######################
	 * ###
	 * #### CALLABLE FUNCTIONS
	 * ###
	 * ######################
	 */

	/**
	 * HELPER COMMENT START
	 *
	 * Within this class, you can define common functions that you are 
	 * going to use throughout the whole plugin. 
	 * 
	 * Down below you will find a demo function called output_text()
	 * To access this function from any other class, you can call it as followed:
	 * 
	 * FEEDBACKER()->helpers->output_text( 'my text' );
	 * 
	 */
	 
	/**
	 * Output some text
	 *
	 * @param	string	$text	The text to output
	 * @since	1.0.0
	 *
	 * @return	void
	 */
	 public function output_text( $text = '' ){
		 echo $text;
	 }

	 /**
	  * HELPER COMMENT END
	  */

	/**
	 * Get current user info
	 *
	 * @return	array
	 */
	public function get_current_user_info() {
	    $current_user = wp_get_current_user();
	    $user_id = $current_user->ID;
	    $subscription_status = pmpro_getMembershipLevelForUser($user_id);
	    
	    return array(
	        'user_id' => $user_id,
	        'subscription_status' => $subscription_status ? $subscription_status->name : 'Немає підписки'
	    );
	}

	/**
	 * Get all users info
	 *
	 * @return	array
	 */
	public function get_all_users_info() {
	    $users = get_users();
	    $users_info = array();
	    
	    foreach ($users as $user) {
	        $subscription_status = pmpro_getMembershipLevelForUser($user->ID);
	        $users_info[] = array(
	            'user_id' => $user->ID,
	            'user_email' => $user->user_email,
	            'subscription_status' => $subscription_status ? $subscription_status->name : 'Немає підписки'
	        );
	    }
	    
	    return $users_info;
	}

}
