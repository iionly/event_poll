<?php

$event = $vars['event'];

$message_options = [
	elgg_echo('event_poll:schedule_message:options:all') => 'all',
	elgg_echo('event_poll:schedule_message:options:not_responded') => 'not_responded',
];

echo elgg_view_field([
	'#type' => 'hidden',
	'name' => 'event_guid',
	'value' => $event->guid,
]);

$html = '<div id="event-poll-schedule-message-wrapper">';
$html .= '<h3>' . elgg_echo('event_poll:schedule_message:subtitle') . '</h3>';
$html .= elgg_view_field([
	'#type' => 'radio',
	'#label' => elgg_echo('event_poll:schedule_message:options:label'),
	'name' => 'message_option',
	'id' => 'event-poll-schedule-options',
	'value' => 'all',
	'options' => $message_options,
]);
$html .= elgg_view_field([
	'#type' => 'plaintext',
	'#label' => elgg_echo('event_poll:schedule_message:message:label'),
	'name' => 'message',
	'id' => 'event-poll-schedule-message',
]);
$html .= elgg_view_field([
	'#type' => 'submit',
	'value' => elgg_echo('event_poll:schedule_message_button'),
	'id' => 'event-poll-schedule-message-button',
]);
$html .= '</div>';

echo $html;
