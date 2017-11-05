<?php

elgg_require_js('event_poll/event_poll');

$guid = elgg_extract('guid', $vars, 0);

$vars['id'] = 'event-poll-vote';
$vars['name'] = 'event_poll_vote';
// just in case a feature adds an image upload
$vars['enctype'] = 'multipart/form-data';

elgg_push_breadcrumb(elgg_echo('item:object:event_calendar'), 'event_calendar/list');

$body_vars = [];
$event = get_entity((int)$guid);
if (elgg_instanceof($event, 'object', 'event_calendar')) {
	$body_vars['event'] = $event;
	$body_vars['form_data'] = event_poll_prepare_vote_form_vars($event);
	$event_container = get_entity($event->container_guid);
	if (elgg_instanceof($event_container, 'group')) {
		elgg_push_breadcrumb($event_container->name, 'event_calendar/group/' . $event_container->getGUID());
	}
	elgg_push_breadcrumb($event->title, $event->getURL());

	$title = elgg_echo('event_poll:vote_title');
	elgg_push_breadcrumb(elgg_echo('event_poll:vote_title'));
	$content = elgg_view_form('event_poll/vote', $vars, $body_vars);
} else {
	$content = elgg_echo('event_poll:error_event_poll_edit');
}

$params = [
	'title' => $title,
	'content' => $content,
	'filter' => '',
];

$body = elgg_view_layout("content", $params);

echo elgg_view_page($title, $body);
