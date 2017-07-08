<?php

elgg_register_event_handler('init', 'system', 'event_poll_init');

function event_poll_init() {

	elgg_register_library('elgg:event_poll', elgg_get_plugins_path() . 'event_poll/models/model.php');

	// Register a page handler, so we can have nice URLs
	elgg_register_page_handler('event_poll', 'event_poll_page_handler');

	//add to the css
	elgg_extend_view('css/elgg', 'event_poll/css');

	// entity menu
	elgg_register_plugin_hook_handler('register', 'menu:entity', 'event_poll_entity_menu_setup');

	// page menu
	elgg_register_plugin_hook_handler('register', 'menu:page', 'event_poll_pagemenu');

	// register actions
	$action_path = elgg_get_plugins_path() . 'event_poll/actions/event_poll';
	elgg_register_action("event_poll/edit", "$action_path/edit.php");
	elgg_register_action("event_poll/delete", "$action_path/delete.php");
	elgg_register_action("event_poll/set_poll", "$action_path/set_poll.php");
	elgg_register_action("event_poll/invite", "$action_path/invite.php");
	elgg_register_action("event_poll/vote", "$action_path/vote.php");
	elgg_register_action("event_poll/schedule", "$action_path/schedule.php");
	elgg_register_action("event_poll/schedule_message", "$action_path/schedule_message.php");
}

function event_poll_pagemenu($hook, $type, $return, $params) {
	$context = elgg_get_context();
	if (!in_array($context, array('event_calendar', 'event_calendar:view', 'event_poll'))) {
		return $return;
	}

	elgg_load_library('elgg:event_calendar');
	elgg_load_library('elgg:event_poll');

	$poe = elgg_get_page_owner_entity();
	if (elgg_instanceof($poe, 'group')) {
		$group_guid = $poe->guid;
	} else {
		$group_guid = 0;
	}

	if (event_calendar_can_add($group_guid)) {
		if ($group_guid) {
			$url_schedule_event =  "event_calendar/schedule/$group_guid";
		} else {
			$url_schedule_event =  "event_calendar/schedule";
		}

		$schedule_event = new ElggMenuItem('event-calendar-1schedule', elgg_echo('event_calendar:schedule_event'), $url_schedule_event);
		$schedule_event->setSection('event_poll');
		$return[] = $schedule_event;
	}

	$url_list_polls =  "event_poll/list/all";
	$list_polls = new ElggMenuItem('event-calendar-2list-polls', elgg_echo('event_calendar:list_polls'), $url_list_polls);
	$list_polls->setSection('event_poll');
	$return[] = $list_polls;

	return $return;
}

/**
 * Dispatches event poll pages.
 *
 * URLs take the form of
 *  New event poll:		event_poll/add/<event_guid>
 *  Edit event poll:	event_poll/edit/<event_guid>
 *  Vote in poll:		event_poll/vote/<event_guid>
 *  Schedule event:		event_poll/schedule/<event_guid>
 *  List polls:			event_poll/list/<filter>
 *
 * @param array $page
 * @return NULL
 */
function event_poll_page_handler($page) {
	elgg_load_library('elgg:event_poll');
	$page_type = $page[0];
	$resource_vars = [];
	switch ($page_type) {
		case 'add':
		case 'edit':
			elgg_gatekeeper();
			$resource_vars['page_type'] = $page_type;
			$resource_vars['guid'] = $page[1];
			echo elgg_view_resource('event_poll/edit', $resource_vars);
			break;
		case 'vote':
			elgg_gatekeeper();
			$resource_vars['guid'] = $page[1];
			echo elgg_view_resource('event_poll/vote', $resource_vars);
			break;
		case 'schedule':
			elgg_gatekeeper();
			$resource_vars['guid'] = $page[1];
			echo elgg_view_resource('event_poll/schedule', $resource_vars);
			break;
		case 'list':
			elgg_gatekeeper();
			$resource_vars['filter'] = $page[1];
			echo elgg_view_resource('event_poll/list', $resource_vars);
			break;
		case 'get_times_dropdown':
			elgg_gatekeeper();
			echo elgg_view_resource('event_poll/get_times_dropdown', $resource_vars);
			break;
		default:
			return false;
	}
	return true;
}

function event_poll_entity_menu_setup($hook, $type, $return, $params) {
	if (elgg_in_context('widgets')) {
		return $return;
	}

	$entity = $params['entity'];
	$handler = elgg_extract('handler', $params, false);
	if ($handler != 'event_poll') {
		return $return;
	}

	$new_return = array();
	if ($entity->canEdit()) {
		$options = array(
			'name' => 'event_poll_delete',
			'text' => elgg_view_icon('delete'),
			'title' => elgg_echo('event_poll:delete'),
			'href' => 'action/event_poll/delete?guid=' . $entity->guid,
			'confirm' => elgg_echo('event_poll:deleteconfirm'),
			'is_action' => true,
		);
		$new_return[] = ElggMenuItem::factory($options);
	}
	
	return $new_return;
}
