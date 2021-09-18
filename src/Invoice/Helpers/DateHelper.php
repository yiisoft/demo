<?php
declare(strict_types=1);

namespace App\Invoice\Helpers;

use App\Invoice\Setting\SettingRepository as SRepo;

Use \DateTime;

Class DateHelper
{

private SRepo $s;
    
public function __construct(SRepo $s)
{
    $this->s = $s;
}

public function style()
{
    $this->s->load_settings();    
    $format = $this->s->setting('date_format');
    $formats = $this->date_formats();
    return $formats[$format]['setting'];
}

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

public function date_from_mysql($datetimeimmutable)
{
    return DateTime::createFromImmutable($datetimeimmutable)->format($this->style());    
}

public function date_from_timestamp($timestamp)
{
    return DateTime::setTimestamp($timestamp)->format($this->style());
}

public function date_to_mysql($date)
{
    return DateTime::createFromFormat($this->style(), $date);
}

public function is_date($date)
{
    $d = DateTime::createFromFormat($this->style(), $date);
    return $d && $d->format($this->style()) == $date;
}

function date_format_datepicker()
{
    $date_formats = $this->date_formats();    
    if (empty($this->style())){
        return $date_formats['d-m-Y']['datepicker'];
    } else
    {
        return $date_formats[$this->style()]['datepicker'];
    }
}

public function increment_user_date($date, $increment)
{
    $s->load_settings();
    
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