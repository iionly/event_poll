<?php

$guid = get_input('event_guid');
$message_option = get_input('message_option');
$message = get_input('message');

$event = get_entity($guid);
if (elgg_instanceof($event, 'object', 'event_calendar') && $event->canEdit()) {
	$guids = [];
	$invitees = event_poll_get_invitees($guid);
	if ($message_option == 'all') {
		foreach ($invitees as $user) {
			$guids[] = $user->guid;
		}
	} else {
		$voted_guids = event_poll_get_voted_guids($guid);
		foreach ($invitees as $user) {
			if (!in_array($user->guid, voted_guids)) {
				$guids[] = $user->guid;
			}
		}
	}

	$subject = elgg_echo('event_poll:schedule_message:subject', [$event->title]);
	$body = $message . "\n\n" . elgg_get_site_url() . 'event_poll/vote/' . $guid;
	$sender_guid = elgg_get_logged_in_user_guid();
	notify_user($guids, $sender_guid, $subject, $body, [], 'email');
	foreach($guids as $guid) {
		messages_send($subject, $body, $guid, $sender_guid, 0, false, false);
	}

	system_message(elgg_echo('event_poll:schedule_message:response'));
	forward($event->getURL());
} else {
	register_error(elgg_echo('event_poll:error_event_poll_edit'));
	forward();
}
