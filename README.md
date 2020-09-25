Covid Stat Checker
===========================

This project aims to provide a wrapper command to retrieve data from the UK Government's Coronavirus data.

Data is retrieved from https://coronavirus.data.gov.uk/

Steps to use:
--------------

Update App\Command\GetLatestStatsCommand to include local areas of interest

Via command line, run the following command:

```
php bin/console covid-stat-checker:get-latest-covid-stats
```
