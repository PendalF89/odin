<?php

namespace App\Checkers;

use App\RobotScan;
use App\UptimeScan;
use GuzzleHttp\Client;
use App\Website;
use GuzzleHttp\TransferStats;
use Illuminate\Support\Str;
use SebastianBergmann\Diff\Differ;


class Uptime
{
    private $website;

    public function __construct(Website $website)
    {
        $this->website = $website;
    }

    public function run()
    {
        $this->fetch();
//        $this->compare();
    }

    private function fetch()
    {
        $client = new Client();

        $response_time = 3001;

        $response = $client->request('GET', $this->website->url, [
            'on_stats' => function ($stats) use (&$response_time) {
                $response_time = $stats->getTransferTime();
            },
            'verify' => false,
            'allow_redirects' => true,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0+(compatible; UptimeRobot/2.0; http://www.uptimerobot.com/; Odin)'
            ],
        ]);

        $scan = new UptimeScan([
            'response_status' => sprintf('%s (%d)', $response->getReasonPhrase(), $response->getStatusCode()),
            'response_time' => $response_time,
            'was_online' => Str::contains($response->getBody(), $this->website->uptime_keyword)
        ]);

        $this->website->uptimes()->save($scan);
    }

    private function compare()
    {
        $scans = $this->website->last_robot_scans;

        if ($scans->isEmpty() || $scans->count() === 1) {
            return;
        }

        $diff = (new Differ)->diff($scans->last()->txt, $scans->first()->txt);

        $scans->first()->diff = $diff;
        $scans->first()->save();
    }
}