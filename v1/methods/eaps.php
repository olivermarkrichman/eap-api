<?php

function process($request,$eap_id){
	switch ($request){
		case 'get':
		if (!empty($eap_id) && is_numeric($eap_id)){
			get_one($eap_id);
		} elseif (empty($eap_id)){
			get_all();
		}
		break;
		case 'post':
			create();
		break;
		case 'put':
		if (!empty($eap_id) && is_numeric($eap_id)){
			edit($eap_id);
		}
		break;
		case 'delete':
		if (!empty($eap_id) && is_numeric($eap_id)){
			remove($eap_id);
		}
		break;
	}
}
