<?php

elgg_load_library('elgg:event_calendar');
elgg_require_js('event_poll/event_poll');

$filter = elgg_extract('filter', $vars);

//event_calendar_handle_event_poll_add_items();
$filter_override = elgg_view('event_poll/filter_menu', array('filter' => $filter));
$options = array(
	'type' => 'object',
	'subtype' => 'event_calendar',
	'metadata_name_value_pairs' => array(array('name' => 'schedule_type', 'value' => 'poll')),
	'offset' => get_input('offset', 0),
	'limit' => 10,
	'full_view' => false,
);
if ($filter == 'all') {
	$title = elgg_echo('event_poll:list:title:show_all');
} else if ($filter == 'mine') {
	$title = elgg_echo('event_poll:list:title:show_mine');
	$options['owner_guid'] = elgg_get_logged_in_user_guid();
} else {
	$title = elgg_echo('event_poll:list:title:show_friends');
	$friendguids = array();
	$logged_in_user = elgg_get_logged_in_user_entity();
	if ($friends = $logged_in_user->getFriends(array('limit' => false))) {
		foreach ($friends as $friend) {
			$friendguids[] = $friend->getGUID();
		}
	}
	$options['owner_guids'] = $friendguids;
}

$content = elgg_list_entities($options, 'elgg_get_entities_from_metadata', 'event_poll_list_polls');

elgg_push_breadcrumb(elgg_echo('item:object:event_calendar'), 'event_calendar/list');
elgg_push_breadcrumb($title);

$params = array('title' => $title, 'content' => $content, 'filter_override' => $filter_override);

$body = elgg_view_layout("content", $params);

echo elgg_view_page($title, $body);
