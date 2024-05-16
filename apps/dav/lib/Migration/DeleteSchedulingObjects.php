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
namespace OCA\DAV\Migration;

use OCA\DAV\BackgroundJob\DeleteOutdatedSchedulingObjects;
use OCA\DAV\CalDAV\CalDavBackend;
use OCP\AppFramework\Utility\ITimeFactory;
use OCP\BackgroundJob\IJobList;
use OCP\Migration\IOutput;
use OCP\Migration\IRepairStep;

class DeleteSchedulingObjects implements IRepairStep {
	public function __construct(private IJobList $jobList,
		private ITimeFactory $time,
		private CalDavBackend $calDavBackend) {
	}

	public function getName(): string {
		return 'Handle outdated scheduling events';
	}

	public function run(IOutput $output): void {
		$output->info('Cleaning up old scheduling events');
		$time = $this->time->getTime() - (60 * 60);
		$this->calDavBackend->deleteOutdatedSchedulingObjects($time, 50000);
		if (!$this->jobList->has(DeleteOutdatedSchedulingObjects::class, [])) {
			$output->info('Adding background job to delete old scheduling objects');
			$this->jobList->add(DeleteOutdatedSchedulingObjects::class, []);
		}
	}
}
