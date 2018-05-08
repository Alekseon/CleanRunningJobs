## Magento 2 Extension - Alekseon/CleanRunningJobs

In case your Magento 2 app is closed/shutdown/explode when there is running cronjob it will stay in "running" status forever.

This might be the reason why your Magento 2 will create more and more jobs in cron_schedule table and that will result that your Magento 2 will use 100% of CPU after few weeks.

This extension fix this issue by marking "running" cronjobs as error if they are older than 3 hours.



More informations you will find on our blog: https://alekseon.com/en/blog/post/magento-2-slow-and-cpu-usage-gets-high-this-might-be-the-reason/




### Installation
* From your CLI run: ```composer require alekseon/module-cleanrunningjobs```
* Flush your cache.
* Upgrade database: ```bin/magento setup:upgrade```

