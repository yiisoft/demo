<?php

declare(strict_types=1);

namespace App\Invoice\Helpers;

use Yiisoft\Aliases\Aliases;

Class CountryHelper
{
public function get_country_list($cldr)
{
    $new_aliases = new Aliases(['@helpers' => __DIR__, '@country_list' => '@helpers/Country-list']);
    $file = $new_aliases->get('@country_list') .'/'. $cldr . '\country.php';
    $default_english = $new_aliases->get('@country_list') . '/en/country.php';
    if (file_exists($file)) {
        return (include $file);
    } else {
        return (include $default_english);
    }
}

public function get_country_name($cldr, $countrycode)
{
    $countries = $this->get_country_list($cldr);
    return (isset($countries[$countrycode]) ? $countries[$countrycode] : $countrycode);
}
}