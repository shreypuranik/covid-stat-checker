<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Console\Helper\Table;

class GetLatestStatsCommand extends Command
{
  protected static $defaultName = 'covid-stat-checker:get-latest-covid-stats';

  protected $localities = [
      //Put comma separated list of localities here
  ];

  protected $regions = [
    'East Midlands',
    'East of England',
    'London',
    'North East',
    'North West',
    'South East',
    'South West',
    'West Midlands',
    'Yorkshire and The Humber'
  ];

  public function __construct(HttpClientInterface $httpClient)
  {
    $this->client = $httpClient;

    parent::__construct();
  }


  protected function configure()
  {
    $this->setDescription('Gets latest case numbers for defined localities.');
    $this->setHelp('Localities are defined in the codebase');
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
        $allLocalities = [];
        $tableData = [];
        foreach($this->localities as $locality) {
          $apiUrl = 'https://api.coronavirus.data.gov.uk/v1/data?filters=areaType=ltla;areaName=' . $locality . '&latestBy=cumCasesByPublishDate&structure=%7B%22date%22:%22date%22,%22value%22:%22cumCasesByPublishDate%22%7D';


          $response = $this->client->request(
            'GET',
            $apiUrl
          );

          $content = $response->toArray();

          $data = $content['data'][0];
          $data['locality'] = $locality;

          $allLocalities[] = $data;
          $tableData[] = [
            $locality,
            $data['value']
          ];
        }

        $regionTableData = [];
        foreach($this->regions as $region) {
          $apiUrl = 'https://api.coronavirus.data.gov.uk/v1/data?filters=areaType=region;areaName=' . $region . '&latestBy=cumDeaths28DaysByPublishDate&structure=%7B%22date%22:%22date%22,%22value%22:%22cumDeaths28DaysByPublishDate%22%7D';
          $response = $this->client->request(
            'GET',
            $apiUrl
          );

          $content = $response->toArray();
          $data = $content['data'][0];
          $regionTableData[] = [$region, $data['value']];

        }

        $vaccinationDataApiUrl = 'https://coronavirus.data.gov.uk/api/v1/data?filters=areaType=overview;areaName=United%2520Kingdom&structure=%7B%22date%22:%22date%22,%22newPeopleReceivingFirstDose%22:%22newPeopleReceivingFirstDose%22,%22cumPeopleReceivingFirstDose%22:%22cumPeopleReceivingFirstDose%22%7D';
        $response = $this->client->request(
            'GET',
            $vaccinationDataApiUrl
        );

        $vaccinationDataContent = $response->toArray();
        $peopleWithFirstDose = $vaccinationDataContent['data'][0]['cumPeopleReceivingFirstDose'];

        $dateTime = new \DateTime();

        $output->writeLn(['']);

        $output->writeLn([
          'Latest COVID-19 Stats',
          '====================='
        ]);

        $output->writeLn(['']);

        $output->writeLn(['Regions in England (Fatalities Count)']);
        $regionFatalitiesTable = new Table($output);
        $regionFatalitiesTable->setHeaders(['Region', 'Total Fatalities']);
        $regionFatalitiesTable->setRows($regionTableData);
        $regionFatalitiesTable->render();

        $output->writeLn(['']);

        $output->writeLn(['Local Areas of Interest']);
        $table = new Table($output);
        $table->setHeaders(['Local Area', 'Case Count']);
        $table->setRows($tableData);
        $table->render();

        $output->writeLn(['']);

        $output->writeln([
            'Total population with first dose of vaccination: ' . number_format($peopleWithFirstDose)
        ]);

        $output->writeLn(['']);

        $output->writeLn([
          'Data retrieved at ' . $dateTime->format('l jS F Y H:i:s')
        ]);

        return Command::SUCCESS;
    }
}
