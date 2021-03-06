<?php
/**
 * @author Semih Serhat Karakaya <karakayasemi@itu.edu.tr>
 *
 * @copyright Copyright (c) 2018, ownCloud GmbH
 * @license GPL-2.0
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation; either version 2 of the License, or (at your option)
 * any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
 *
 */
namespace OCA\BruteForceProtection\Tests\Jobs;
use OCA\BruteForceProtection\BruteForceProtectionConfig;
use OCA\BruteForceProtection\Db\FailedLoginAttemptMapper;
use OCA\BruteForceProtection\Jobs\ExpireOldAttempts;
use Test\TestCase;

/**
 * Class ExpireOldAttemptsTest
 *
 * @group DB
 * @package OCA\BruteForceProtection\Tests\Jobs
 */
class ExpireOldAttemptsTest extends TestCase {
	/** @var FailedLoginAttemptMapper | \PHPUnit\Framework\MockObject\MockObject $mapper */
	private $mapper;
	/** @var BruteForceProtectionConfig | \PHPUnit\Framework\MockObject\MockObject $config */
	private $config;
	/** @var int $thresholdConfigVal */
	private $thresholdConfigVal = 60;
	/** @var int $thresholdConfigVal */
	private $banPeriodConfigVal = 300;
	/** @var ExpireOldAttempts $expireAttempts */
	private $expireAttempts;
	public function setUp(): void {
		$this->mapper = $this->getMockBuilder(FailedLoginAttemptMapper::class)
			->disableOriginalConstructor()
			->getMock();
		$this->config = $this->getMockBuilder(BruteForceProtectionConfig::class)
			->disableOriginalConstructor()
			->getMock();
		$this->expireAttempts = new ExpireOldAttempts($this->mapper, $this->config);
	}
	public function testExecute() {
		/** @var \OC\BackgroundJob\JobList $jobList */
		$argument = [];
		$this->config->expects($this->exactly(1))
			->method('getBruteForceProtectionTimeThreshold')
			->willReturn($this->thresholdConfigVal);
		$this->config->expects($this->exactly(1))
			->method('getBruteForceProtectionBanPeriod')
			->willReturn($this->banPeriodConfigVal);
		$this->mapper->expects($this->exactly(1))
			->method('deleteOldFailedLoginAttempts')
			->with($this->thresholdConfigVal+$this->banPeriodConfigVal);
		$this->expireAttempts->run($argument);
	}
}
