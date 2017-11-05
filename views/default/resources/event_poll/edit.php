<?php

elgg_require_js('event_poll/event_poll');

$page_type = elgg_extract('page_type', $vars);
$guid = elgg_extract('guid', $vars, 0);

$vars['id'] = 'event-poll-edit';
$vars['name'] = 'event_poll_edit';
// just in case a feature adds an image upload
$vars['enctype'] = 'multipart/form-data';

elgg_push_breadcrumb(elgg_echo('item:object:event_calendar'), 'event_calendar/list');

$body_vars = [];
$event = get_entity((int)$guid);
if (elgg_instanceof($event, 'object', 'event_calendar') && $event->canEdit()) {
	$body_vars['event'] = $event;
	$body_vars['form_data'] =  event_poll_prepare_edit_form_vars($event);
	// start date is the start of the month for this event
	$body_vars['start_date'] = gmdate("Y-m",$event->start_date)."-1";

	$event_container = get_entity($event->container_guid);
	if (elgg_instanceof($event_container, 'group')) {
		elgg_push_breadcrumb($event_container->name, 'event_calendar/group/' . $event_container->getGUID());
	}
	elgg_push_breadcrumb($event->title, $event->getURL());

	if ($page_type == 'edit') {
		$title = elgg_echo('event_poll:edit_title');
		elgg_push_breadcrumb(elgg_echo('event_poll:edit_title'));
		$content = elgg_view_form('event_poll/edit', $vars, $body_vars);
	} else {
		$title = elgg_echo('event_poll:add_title');
		elgg_push_breadcrumb(elgg_echo('event_poll:add_title'));
		$content = elgg_view_form('event_poll/edit', $vars, $body_vars);
	}

} else {
	$title = elgg_echo('event_poll:error_title');
	$content = elgg_echo('event_poll:error_event_poll_edit');
}

$params = [
	'title' => $title,
	'content' => $content,
	'filter' => '',
];

$body = elgg_view_layout("content", $params);

echo elgg_view_page($title, $body);
