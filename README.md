Covid Stat Checker
===========================

This project aims to provide a wrapper command to retrieve data from the UK Government's Coronavirus data.

Data is retrieved from https://coronavirus.data.gov.uk/, and follows guidelines set up in https://coronavirus.data.gov.uk/developers-guide. 

Steps to use:
--------------

Clone the repository, navigate to the project directory, and install using Composer:

```
composer install
```

Update App\Command\GetLatestStatsCommand to include local areas of interest

Via command line, run the following command:

```
php bin/console covid-stat-checker:get-latest-covid-stats
```

I hope this is of use.

Please stay safe, and stay healthy.
