<?php
/**
 * 
 * @author Muli Cohen <mulikohn@gmail.com>
 * 
 * @contributer Matan Green
 * 
 */
namespace IfSo\PublicFace\Services\TriggersService\Triggers;

require_once( plugin_dir_path ( __DIR__ ) . 'trigger-base.class.php');

class UserRole extends TriggerBase {
	public function __construct() {
		parent::__construct('UserRole');
	}

	public function handle($trigger_data) { 
		$rule = $trigger_data->get_rule();
		$content = $trigger_data->get_content();

		$user_exists = in_array("administrator", $this->user_role(get_current_user_id())); //add argument
		
		if($user_exists) 
			return $content;
		return false;
		
	}

	private function user_role($user_id) {
		$user_meta = get_userdata($user_id);
		$user_roles=$user_meta->roles;
		return $user_roles;
	}
}
