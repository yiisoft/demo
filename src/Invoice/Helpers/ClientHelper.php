<?php

declare(strict_types=1);

Namespace App\Invoice\Helpers;

use App\Invoice\Setting\SettingRepository; 

Class ClientHelper 
{
    public function format_client($client)
    {
        if ($client->getClient_surname() != "") {
            return $client->getClient_name() . " " . $client->getClient_surname();
        }
        return $client->getClient_name();
    }

    public function format_gender($gender, SettingRepository $s)
    {
        if ($gender == 0) {
            return $s->trans('gender_male');
        }

        if ($gender == 1) {
            return $s->trans('gender_female');
        }

        return $s->trans('gender_other');
    }
}