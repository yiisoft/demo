<?php
Namespace frontend\modules\invoice\application\helpers;

use yii\base\Component;

Class H2HproductHelper extends Component
{
    
public function format_product($client)
{
    if ($client->surname != "") {
        return $client->name . " " . $client->surname;
    }

    return $client->name;
}


public function format_gender($gender)
{
    if ($gender == 0) {
        return trans('gender_male');
    }

    if ($gender == 1) {
        return trans('gender_female');
    }

    return trans('gender_other');
}
}