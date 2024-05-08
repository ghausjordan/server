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
use OCP\BackgroundJob\TimedJob;
use OCP\Calendar\BackendTemporarilyUnavailableException;
use OCP\Calendar\Resource\IBackend as IResourceBackend;
use Psr\Log\LoggerInterface;

class DeleteOutdatedSchedulingObjectsForResources extends TimedJob {
	public function __construct(
		private IResourceBackend $resourceBackend,
		private CalDavBackend $calDavBackend,
		private LoggerInterface $logger,
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
		try {
			$resources = $this->resourceBackend->listAllResources();
		} catch (BackendTemporarilyUnavailableException $e) {
			return;
		}

		$count = 0;
		foreach ($resources as $resource) {
			$principal = 'principals/calendar-resources/calendar_resource_management-' . $resource;
			$children = $this->calDavBackend->getSchedulingObjects($principal);
			foreach($children as $object) {
				if ($object['lastmodified'] < ($this->time->getTime() - 24 * 60 * 60)) {
					$this->calDavBackend->deleteSchedulingObject($principal, $object['uri']);
					$count++;
				}
			}
		}
		$this->logger->info("Removed $count outdated scheduling objects for resources");
	}
}
