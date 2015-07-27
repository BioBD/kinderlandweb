<?php

	function renderMenu($permissions) {
		
		if(in_array(SYSTEM_ADMIN, $permissions)){
			return APPPATH . 'views/include/admin_left_menu.php';
		}
			
		if(in_array(DIRECTOR, $permissions)){
			return APPPATH . 'views/include/director_left_menu.php';
		}

		if(in_array(SECRETARY, $permissions)){
			return APPPATH . 'views/include/secretary_left_menu.php';
		}

		return APPPATH . 'views/include/secretary_left_menu.php';

	}

?>