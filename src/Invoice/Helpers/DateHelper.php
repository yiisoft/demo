<?php
declare(strict_types=1);

namespace App\Invoice\Helpers;

use App\Invoice\Setting\SettingRepository;

Use  \DateTime;

Class DateHelper
{
    
public function date_formats()
{
    return [
        'd/m/Y' => [
            'setting' => 'd/m/Y',
            'datepicker' => 'dd/mm/yyyy',
        ],
        'd-m-Y' => [
            'setting' => 'd-m-Y',
            'datepicker' => 'dd-mm-yyyy',
        ],
        'd-M-Y' => [
            'setting' => 'd-M-Y',
            'datepicker' => 'dd-M-yyyy',
        ],
        'd.m.Y' => [
            'setting' => 'd.m.Y',
            'datepicker' => 'dd.mm.yyyy',
        ],
        'j.n.Y' => [
            'setting' => 'j.n.Y',
            'datepicker' => 'd.m.yyyy',
        ],
        'd M,Y' => [
            'setting' => 'd M,Y',
            'datepicker' => 'dd M,yyyy',
        ],
        'm/d/Y' => [
            'setting' => 'm/d/Y',
            'datepicker' => 'mm/dd/yyyy',
        ],
        'm-d-Y' => [
            'setting' => 'm-d-Y',
            'datepicker' => 'mm-dd-yyyy',
        ],
        'm.d.Y' => [
            'setting' => 'm.d.Y',
            'datepicker' => 'mm.dd.yyyy',
        ],
        'Y/m/d' => [
            'setting' => 'Y/m/d',
            'datepicker' => 'yyyy/mm/dd',
        ],
        'Y-m-d' => [
            'setting' => 'Y-m-d',
            'datepicker' => 'yyyy-mm-dd',
        ],
        'Y.m.d' => [
            'setting' => 'Y.m.d',
            'datepicker' => 'yyyy.mm.dd',
        ],
    ];
}

public function date_from_mysql($date, $ignore_post_check = false,SettingRepository $s)
{
    if ($date <> '0000-00-00') {
        if (!$_POST or $ignore_post_check) {
            $date = DateTime::createFromImmutable($date );
            //$date = DateTime::createFromFormat('Y-m-d', $date);
            return $date->format($s->setting('date_format'));
        }
        return $date;
    }
    return '';
}

public function date_from_timestamp($timestamp,SettingRepository $s)
{
    $date = new DateTime();
    $date->setTimestamp($timestamp);
    return $date->format($s->setting('date_format'));
}

public function date_to_mysql($date, $s)
{
   $date = DateTime::createFromFormat($s->setting('date_format'), $date);
   return $date->format('Y-m-d');
}


public function is_date($date,SettingRepository $s)
{
    $format = $s->setting('date_format');
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}

public function date_format_setting(SettingRepository $s)
{
    $date_format = $s->setting('date_format');

    $date_formats = $this->date_formats();

    return $date_formats[$date_format]['setting'];
}

function date_format_datepicker(SettingRepository $s)
{
    $date_format = $s->setting('date_format');
    
    $date_formats = $this->date_formats();
    
    if (empty($date_format)){return $date_formats['d-m-Y']['datepicker'];}
    if (!empty($date_format)){return $date_formats[$date_format]['datepicker'];}
}

public function increment_user_date($date, $increment,SettingRepository $s)
{
    $mysql_date = $this->date_to_mysql($date);

    $new_date = new DateTime($mysql_date);
    $new_date->add(new DateInterval('P' . $increment));

    return $new_date->format($s->setting('date_format'));
}

public function increment_date($date, $increment)
{
    $new_date = new DateTime($date);
    $new_date->add(new DateInterval('P' . $increment));
    return $new_date->format('Y-m-d');
}
}