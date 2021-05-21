<?php
Namespace frontend\modules\invoice\application\helpers;

Use frontend\modules\invoice\application\models\ci\Mdl_settings;

Class NumberHelper {

public static function getSettings()
{
    $mdl_settings = new Mdl_settings();
    $mdl_settings->load_settings();
    return $mdl_settings;
}
    
public static function format_currency($amount)
{
    $mdl_settings = NumberHelper::getSettings();
    $currency_symbol =$mdl_settings->setting('currency_symbol');
    $currency_symbol_placement = $mdl_settings->setting('currency_symbol_placement');
    $thousands_separator = $mdl_settings->setting('thousands_separator');
    $decimal_point = $mdl_settings->setting('decimal_point');

    if ($currency_symbol_placement == 'before') {
        return $currency_symbol . number_format($amount, ($decimal_point) ? 2 : 0, $decimal_point, $thousands_separator);
    } elseif ($currency_symbol_placement == 'afterspace') {
        return number_format($amount, ($decimal_point) ? 2 : 0, $decimal_point, $thousands_separator) . '&nbsp;' . $currency_symbol;
    } else {
        return number_format($amount, ($decimal_point) ? 2 : 0, $decimal_point, $thousands_separator) . $currency_symbol;
    }
}

public static function format_amount($amount = null)
{
    $mdl_settings = NumberHelper::getSettings();    
    if ($amount) {
        $thousands_separator = $mdl_settings->setting('thousands_separator');
        $decimal_point = $mdl_settings->setting('decimal_point');

        return number_format($amount, ($decimal_point) ? 2 : 0, $decimal_point, $thousands_separator);
    }
    return null;
}

public static function standardize_amount($amount)
{
    $mdl_settings = NumberHelper::getSettings();
    $thousands_separator = $mdl_settings->setting('thousands_separator');
    $decimal_point = $mdl_settings->setting('decimal_point');

    $amount = str_replace($thousands_separator, '', $amount);
    $amount = str_replace($decimal_point, '.', $amount);

    return $amount;
}
}