<?php

declare(strict_types=1);

/**
 * @copyright Copyright (c) 2024 Marcel Klehr <mklehr@gmx.net>
 *
 * @author Marcel Klehr <mklehr@gmx.net>
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
 */

namespace OCP\TaskProcessing;

/**
 * This is a task type interface that is implemented by task processing
 * task types
 * @since 30.0.0
 */
interface ITaskType {
	/**
	 * Returns the unique id of this task type
	 *
	 * @since 30.0.0
	 * @return string
	 */
	public function getId(): string;

	/**
	 * Returns the localized name of this task type
	 *
	 * @since 30.0.0
	 * @return string
	 */
	public function getName(): string;

	/**
	 * Returns the localized description of this task type
	 *
	 * @since 30.0.0
	 * @return string
	 */
	public function getDescription(): string;

	/**
	 * Returns the shape of the input array
	 *
	 * @since 30.0.0
	 * @psalm-return ShapeDescriptor[]
	 */
	public function getInputShape(): array;

	/**
	 * Returns the shape of the output array
	 *
	 * @since 30.0.0
	 * @psalm-return ShapeDescriptor[]
	 */
	public function getOutputShape(): array;
}
