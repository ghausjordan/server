<?php

declare(strict_types=1);

/**
 * @copyright 2024 Anna Larch <anna.larch@gmx.net>
 *
 * @author 2024 Anna Larch <anna.larch@gmx.net>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */
namespace OCA\DAV\Listener;

use OCA\DAV\BackgroundJob\DeleteOutdatedSchedulingObjects;
use OCP\BackgroundJob\IJobList;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\User\Events\UserFirstTimeLoggedInEvent;

/** @template-implements IEventListener<UserFirstTimeLoggedInEvent> */
class DeleteOutdatedSchedulingObjectsJobListener implements IEventListener {
	public function __construct(private IJobList $jobList) {
	}

	/**
	 * In case the user has set their default calendar to the deleted one
	 */
	public function handle(Event $event): void {
		$this->jobList->add(DeleteOutdatedSchedulingObjects::class, ['userId' => $event->getUser()->getUID()]);
	}
}
