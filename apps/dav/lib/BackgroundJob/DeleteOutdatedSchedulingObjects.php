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
namespace OCA\DAV\BackgroundJob;

use OCA\DAV\CalDAV\CalDavBackend;
use OCP\AppFramework\Utility\ITimeFactory;
use OCP\BackgroundJob\IJobList;
use OCP\BackgroundJob\TimedJob;
use OCP\IUserManager;
use Psr\Log\LoggerInterface;

class DeleteOutdatedSchedulingObjects extends TimedJob {
	public function __construct(
		private CalDavBackend $calDavBackend,
		private LoggerInterface $logger,
		private IUserManager $manager,
		private IJobList $jobList,
		ITimeFactory $timeFactory,
	) {
		parent::__construct($timeFactory);
		$this->setInterval(60 * 60 * 24 * 7);
		$this->setTimeSensitivity(self::TIME_INSENSITIVE);
	}

	/**
	 * @param array $argument
	 */
	protected function run($argument) {
		$userId = $argument['userId'];
		if (!$this->manager->userExists($userId)) {
			$this->logger->info("$userId doesn't exist, removing job");
			$this->jobList->remove(self::class, $argument);
			return;
		}

		$principal = 'principals/users/'.$userId;
		$children = $this->calDavBackend->getSchedulingObjects($principal);
		$count = 0;
		foreach($children as $object) {
			if ($object['lastmodified'] < ($this->time->getTime() - 24 * 60 * 60)) {
				$this->calDavBackend->deleteSchedulingObject($principal, $object['uri']);
				$count++;
			}
		}

		$this->logger->info("Removed $count outdated scheduling objects for user $userId");
	}
}
