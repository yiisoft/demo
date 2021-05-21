<?php
Namespace frontend\modules\invoice\application\helpers;

use frontend\modules\invoice\application\components\Utilities;

Class ClientHelper 
{
    
public function format_client($client)
{
    if ($client->client_surname != "") {
        return $client->client_name . " " . $client->client_surname;
    }

    return $client->client_name;
}


public function format_gender($gender)
{
    if ($gender == 0) {
        return Utilities::trans('gender_male');
    }

    if ($gender == 1) {
        return Utilities::trans('gender_female');
    }

    return Utilities::trans('gender_other');
}
}