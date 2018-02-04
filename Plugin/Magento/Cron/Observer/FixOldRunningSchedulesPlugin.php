<?php
/**
 * A Magento 2 module named Alekseon/CleanRunningJobs
 * Copyright (C) 2017 Alekseon
 * https://alekseon.com/
 * 
 * This file is part of Alekseon/CleanRunningJobs.
 * 
 * Alekseon/CleanRunningJobs is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Alekseon\CleanRunningJobs\Plugin\Magento\Cron\Observer;

use \Magento\Cron\Observer\ProcessCronQueueObserver;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class FixOldRunningSchedulesPlugin
{
    /**
     * @var \Magento\Cron\Model\ScheduleFactory
     */
    private $scheduleFactory;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $dateTime;

    /**
     * FixOldRunningSchedules constructor.
     * @param \Magento\Cron\Model\ScheduleFactory $scheduleFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     */
    public function __construct(
        \Magento\Cron\Model\ScheduleFactory $scheduleFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
    ) {
        $this->dateTime = $dateTime;
        $this->scheduleFactory = $scheduleFactory;
    }

    /**
     * @param $subject
     * @param \Magento\Framework\Event\Observer $observer
     * @return array
     */
    public function beforeExecute($subject, \Magento\Framework\Event\Observer $observer)
    {
        $runningLifetimeInMinutes = 180;

        $runningSchedules = $this->scheduleFactory->create()->getCollection()->addFieldToFilter(
            'status',
            \Magento\Cron\Model\Schedule::STATUS_RUNNING
        );

        $runningTimeLimit = $this->dateTime->gmtTimestamp() - $runningLifetimeInMinutes * ProcessCronQueueObserver::SECONDS_IN_MINUTE;
        foreach($runningSchedules as $schedule) {
            if (strtotime($schedule->getExecutedAt()) < $runningTimeLimit) {
                $schedule->setMessages(__('Schedule not finished after %1 minutes.', $runningLifetimeInMinutes));
                $schedule->setStatus(\Magento\Cron\Model\Schedule::STATUS_ERROR);
                $schedule->save();
            }
        }

        return [$observer];
    }
}
