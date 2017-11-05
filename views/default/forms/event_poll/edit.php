<?php

elgg_require_js('event_poll/event_poll');

$event = $vars['event'];
$container = get_entity($event->container_guid);
if (elgg_instanceof($container, 'group')) {
	$container_title = $container->name;
} else {
	$container_title = elgg_echo('event_calendar:site_calendar');
}

$invitation_subject = elgg_echo('event_poll:invitation_subject', [$event->title]);
$invitation_body = elgg_echo('event_poll:invitation_body');
$event_calendar_time_format = elgg_get_plugin_setting('timeformat', 'event_calendar');
if (!$event_calendar_time_format) {
	$event_calendar_time_format = '24';
}

?>

<div class="event-poll-info-wrapper">
	<div class="event-poll-info-item"><label><?php echo elgg_echo('event_calendar:title_label')?></label><span><?php echo $event->title; ?></span></div>
	<div class="event-poll-info-item"><label><?php echo elgg_echo('event_calendar:venue_label')?></label><span><?php echo $event->venue; ?></span></div>
	<div class="event-poll-info-item"><label><?php echo elgg_echo('event_calendar:calendar_label')?></label><span><?php echo $container_title; ?></span></div>
</div>

<div id="event-poll-stage1-wrapper">
	<h3><?php echo elgg_echo('event_poll:select_length:title'); ?></h3>
	<?php echo elgg_view('event_poll/event_length'); ?>
</div>

<h3 id ="event-poll-title1"><?php echo elgg_echo('event_poll:select_days:title'); ?></h3>
<h3 id ="event-poll-title2"><?php echo elgg_echo('event_poll:select_times:title'); ?></h3>
<h3 id ="event-poll-title3"><?php echo elgg_echo('event_poll:days_and_times:title').' <a id="event-poll-edit-link" href="#">'.elgg_echo('event_poll:edit').'</a>'; ?></h3>

<?php
echo elgg_view_field([
	'#type' => 'hidden',
	'id' => 'event-poll-event-guid',
	'value' => $event->guid,
]);
echo elgg_view_field([
	'#type' => 'hidden',
	'id' => 'event-poll-event-groupguid',
	'value' => $vars['group_guid'],
]);
echo elgg_view_field([
	'#type' => 'hidden',
	'id' => 'event-poll-event-url',
	'value' => elgg_get_site_url() . 'ajax/view/event_calendar/popup?guid=' . $event->guid,
]);
echo elgg_view_field([
	'#type' => 'hidden',
	'id' => 'event-poll-month-number',
	'value' => date('n')-1,
]);
echo elgg_view_field([
	'#type' => 'hidden',
	'id' => 'event-poll-hour-number',
	'value' => date('G'),
]);
echo elgg_view_field([
	'#type' => 'hidden',
	'id' => 'event-poll-time-format',
	'value' => $event_calendar_time_format,
]);
echo elgg_view_field([
	'#type' => 'hidden',
	'id' => 'event-poll-stage',
	'value' => '1',
]);

?>

<div id="event-poll-date-times-table-wrapper"></div>
<div id="event-poll-stage3-wrapper">
	<div id="event-poll-date-times-table-read-only-wrapper"></div>
	<?php
		echo elgg_view_field([
			'#type' => 'userpicker',
			'#label' => elgg_echo('event_poll:choose_invitees:title'),
			'id' => 'event-poll-title-choose-invitees',
		]);
		echo '<h3 id ="event-poll-title-message-to-invitees">' . elgg_echo('event_poll:message_to_invitees:title') . '</h3>';
		echo elgg_view_field([
			'#type' => 'text',
			'#label' => elgg_echo('event_poll:subject:label'),
			'name' => 'invitation_subject',
			'value' => $invitation_subject,
			'id' => 'event-poll-invitation-subject',
		]);
		echo elgg_view_field([
			'#type' => 'plaintext',
			'#label' => elgg_echo('event_poll:body:label'),
			'name' => 'invitation_body',
			'value' => $invitation_body,
		]);
	?>
</div>

<?php

echo elgg_view('input/submit', [
	'id' => 'event-poll-back-button',
	'name' => 'back',
	'value' => elgg_echo('event_poll:button:back'),
]) . " ";
echo elgg_view('input/submit', [
	'id' => 'event-poll-next2-button',
	'name' => 'next2',
	'value' => elgg_echo('event_poll:button:next'),
]) . " ";
echo elgg_view('input/submit', [
	'id' => 'event-poll-back2-button',
	'name' => 'back',
	'value' => elgg_echo('event_poll:button:back'),
]) . " ";
echo elgg_view('input/submit', [
	'id' => 'event-poll-send-button',
	'name' => 'next2',
	'value' => elgg_echo('event_poll:button:send'),
]) . " ";

?>

<div class="event-poll-button-separator"></div>
<div class="event-poll-calendar-wrapper" id="calendar"></div>
<div id="event-poll-date-container">
	<h3><?php echo elgg_echo('event_poll:selected_days'); ?></h3>
	<div id="event-poll-date-wrapper"></div>
</div>

<div class="event-poll-button-separator"></div>

<?php

echo elgg_view('input/submit', [
	'id' => 'event-poll-next-button',
	'name' => 'next',
	'value' => elgg_echo('event_poll:button:next'),
]) . " ";
