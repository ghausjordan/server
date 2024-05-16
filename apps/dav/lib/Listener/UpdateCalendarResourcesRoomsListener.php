<?php

declare(strict_types=1);

/**
 * @copyright Copyright (c) 2024 Richard Steinmetz <richard@steinmetz.cloud>
 *
 * @author Richard Steinmetz <richard@steinmetz.cloud>
 *
 * @license AGPL-3.0-or-later
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\DAV\Listener;

use OCA\DAV\BackgroundJob\UpdateCalendarResourcesRoomsBackgroundJob;
use OCA\DAV\Events\ScheduleResourcesRoomsUpdateEvent;
use OCP\BackgroundJob\IJobList;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;

/** @template-implements IEventListener<ScheduleResourcesRoomsUpdateEvent> */
class UpdateCalendarResourcesRoomsListener implements IEventListener {

	public function __construct(
		private IJobList $jobList,
	) {
	}

	public function handle(Event $event): void {
		if (!($event instanceof ScheduleResourcesRoomsUpdateEvent)) {
			return;
		}

		$jobs = $this->jobList->getJobsIterator(
			UpdateCalendarResourcesRoomsBackgroundJob::class,
			null,
			0,
		);
		foreach ($jobs as $job) {
			$this->jobList->resetBackgroundJob($job);
		}
	}
}
