<?php

elgg_load_library('elgg:event_calendar');
elgg_load_library('elgg:event_poll');

$event = $vars['event'];

if ($event && $event->event_poll) {
	$owner = $event->getOwnerEntity();
	echo '<h3>' . elgg_echo('event_poll:vote_subtitle', [$event->title, $owner->name]) . '</h3>';

	$event_poll = unserialize($event->event_poll);

	if (is_array($event_poll) && count($event_poll) > 0) {
	
		if ((!($current_schedule_slot = event_poll_get_current_schedule_slot($event))) || $event->canEdit()) {
			echo '<p class="mtm">' . elgg_echo('event_poll:vote_explanation') . '</p>';
	
			echo elgg_view_field([
				'#type' => 'hidden',
				'name' => 'event_guid',
				'value' => $event->guid,
			]);
			$current_user = elgg_get_logged_in_user_entity();
			$times_choices = event_poll_get_times($event->guid);
			$invitees = event_poll_get_invitees($event->guid);
			$voted_guids = event_poll_get_voted_guids($event->guid);
			$current_schedule_slot = event_poll_get_current_schedule_slot($event);

			$table_rows = event_poll_display_vote_table_header($event_poll);

			@list($table_extra, $others) = event_poll_display_invitees($event_poll, $times_choices, $invitees, $voted_guids, $current_user->guid);
			$table_rows .= $table_extra;

			// current user
			$table_rows .= '<tr><td class="event-poll-name-td">' . $current_user->name . '</td>';
			foreach ($event_poll as $date) {
				$iso_date = $date['iso_date'];
				foreach($date['times_array'] as $time) {
					$minutes = $time['minutes'];
					if ($minutes == '-') {
						$table_rows .= '<td class="event-poll-vote-current-td">&nbsp</td>';
					} else {
						$name = "{$iso_date}__{$minutes}";
						if (isset($times_choices[$current_user->guid]) && in_array($name, $times_choices[$current_user->guid])) {
							$table_rows .= '<td class="event-poll-vote-current-td">' . elgg_view('input/checkbox', ['class' => 'event-poll-vote-checkbox', 'name' => $name, 'value' => 1, 'checked' => 'checked']) . '</td>';
						} else {
							$table_rows .= '<td class="event-poll-vote-current-td">' . elgg_view('input/checkbox', ['class' => 'event-poll-vote-checkbox', 'name' => $name, 'value' => 1]) . '</td>';
						}
					}
				}
			}
			// add the none option
			if (isset($times_choices[$current_user->guid]) && in_array('none', $times_choices[$current_user->guid])) {
				$table_rows .= '<td class="event-poll-vote-current-td">' . elgg_view('input/checkbox', ['class' => 'event-poll-vote-none-checkbox', 'name' => 'none', 'value' => 1, 'checked' => 'checked']) . '</td>';
			} else {
				$table_rows .= '<td class="event-poll-vote-current-td">' . elgg_view('input/checkbox', ['class' => 'event-poll-vote-none-checkbox', 'name' => 'none', 'value' => 1]).'</td>';
			}
			$table_rows .= '</tr>';

			if ($event->canEdit()) {
				// schedule bit
				$table_rows .= '<tr><td class="event-poll-name-td">' . elgg_echo('event_poll:choose_time') . '</td>';
				foreach ($event_poll as $date) {
					$iso_date = $date['iso_date'];
					foreach($date['times_array'] as $time) {
						$minutes = $time['minutes'];
						if ($minutes == '-') {
							$table_rows .= '<td class="event-poll-vote-current-td">&nbsp</td>';
						} else {
							$value = "{$iso_date}__{$minutes}";
							$table_rows .= '<td class="event-poll-vote-current-td">';
							if ($current_schedule_slot == $value) {
								$table_rows .= '<input type="radio" name="schedule_slot" value="' . $value . '" checked="checked">';
							} else {
								$table_rows .= '<input type="radio" name="schedule_slot" value="' . $value . '">';
							}
							$table_rows .= '</td>';
						}
					}
				}
				// add the none option
				$table_rows .= '<td>&nbsp;</td>';
				$table_rows .= '</tr>';
			}
			$table = '<table id="event-poll-vote-table">';
			$table .= $table_rows . '</table>';
			echo $table;

			// other invitees
			if ($others) {
				echo '<div id="event-poll-vote-others-wrapper">';
				echo '<p>' . elgg_echo('event_poll:vote:other') . '</p>';
				foreach($others as $o) {
					echo '<p>' . $o->name . '</p>';
				}
				echo '</div>';
			}

			$html = '<div class="mtm mbm">';
			if ($event->canEdit()) {
				// This extra stuff appears only if a time for the event has been selected
				$html .= '<div id="event-poll-vote-event-data-wrapper">';
				$html .= '<div class="event-calendar-edit-form-block">';
				$html .= '<h2>' . elgg_echo('event_calendar:reminders:label') . '</h2>';
				$html .= elgg_view('event_calendar/reminder_section', $vars);
				$html .= '</div>';

				$html .= elgg_view('event_calendar/personal_manage_section', $vars);
				$html .= elgg_view('event_calendar/share_section', $vars);
				$html .= '</div></div>';
			} else {
				$html .= '<div id="event-poll-vote-message-wrapper">';
				$html .= elgg_view_field([
					'#type' => 'plaintext',
					'#label' => elgg_echo('event_poll:vote_message:label'),
					'name' => 'message',
					'id' => 'event-poll-vote-message',
					'value' => elgg_echo('event_poll:vote_message:explanation'),
				]);
				$html .= '</div></div>';
			}

			$html .= '<div id="event-poll-vote-button-wrapper">';
			$html .= elgg_view('input/submit', ['value' => elgg_echo('event_poll:vote_button')]);
			$html .= '</div>';

			echo $html;

		} else {
			echo '<p class="mtm">' . elgg_echo('event_poll:scheduled_explanation') . '</p>';
			echo '<p><label>' . elgg_echo('event_poll:scheduled_label') . '</label>' . event_calendar_get_formatted_time($event) . '</p>';
		}
	}
}