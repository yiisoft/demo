<?php
declare(strict_types=1);

namespace App\Invoice\Helpers;

use App\Invoice\Setting\SettingRepository as SRepo;
use \DateTime;
use \DateInterval;

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

public function datepicker()
{
    $this->s->load_settings();    
    $format = $this->s->setting('date_format');
    $formats = $this->date_formats();
    return $formats[$format]['datepicker'];
}

public function display()
{
    $this->s->load_settings();    
    $format = $this->s->setting('date_format');
    $formats = $this->date_formats();
    return $formats[$format]['display'];
}

public function date_formats()
{
    return [
        'd/m/Y' => [
            'setting' => 'd/m/Y',
            'datepicker' => 'dd/mm/yy',            
            'display' => 'dd/mm/yyyy',
        ],
        'd-m-Y' => [
            'setting' => 'd-m-Y',
            'datepicker' => 'dd-mm-yy',
            'display' => 'dd-mm-yyyy',
        ],
        'd-M-Y' => [
            'setting' => 'd-M-Y',
            'datepicker' => 'dd-M-yy',
            'display' => 'dd-M-yyyy',
        ],
        'd.m.Y' => [
            'setting' => 'd.m.Y',
            'datepicker' => 'dd.mm.yy',
            'display' => 'dd.mm.yyyy',
        ],
        'j.n.Y' => [
            'setting' => 'j.n.Y',
            'datepicker' => 'd.m.yy',            
            'display' => 'd.m.yyyy',
        ],
        'd M,Y' => [
            'setting' => 'd M,Y',
            'datepicker' => 'dd M,yy',            
            'display' => 'dd M,yyyy',
        ],
        'm/d/Y' => [
            'setting' => 'm/d/Y',
            'datepicker' => 'mm/dd/yy',            
            'display' => 'mm/dd/yyyy',
        ],
        'm-d-Y' => [
            'setting' => 'm-d-Y',
            'datepicker' => 'mm-dd-yy',
            'display'=> 'mm-dd-yyyy'
        ],
        'm.d.Y' => [
            'setting' => 'm.d.Y',
            'datepicker' => 'mm.dd.yy',
            'display'=>'mm.dd.yyyy'
        ],
        'Y/m/d' => [
            'setting' => 'Y/m/d',
            'datepicker' => 'yy/mm/dd',
            'display' => 'yyyy/mm/dd'
        ],
        'Y-m-d' => [
            'setting' => 'Y-m-d',
            'datepicker' => 'yy-mm-dd',
            'display' => 'yyyy-mm-dd'
        ],
        'Y.m.d' => [
            'setting' => 'Y.m.d',
            'datepicker' => 'yy.mm.dd',
            'display' => 'yyyy.mm.dd'
        ],
    ];
}

public function getTime_from_DateTime($datetimeimmutable)
{
    return DateTime::createFromImmutable($datetimeimmutable)->format('H:m:s');    
}

public function getYear_from_DateTime($datetimeimmutable)
{
    return DateTime::createFromImmutable($datetimeimmutable)->format('Y');    
}

public function date_from_mysql($datetimeimmutable)
{
    return DateTime::createFromImmutable($datetimeimmutable)->format($this->style());    
}

public function date_for_payment_form($datetimeimmutable)
{
    return DateTime::createFromImmutable($datetimeimmutable)->format($this->style());    
}

public function date_from_timestamp($timestamp)
{
    return DateTime::setTimestamp($timestamp)->format($this->style());
}

public function is_date($date)
{
    $d = DateTime::createFromFormat($this->style(), $date);
    return $d && $d->format($this->style()) == $date;
}

public function getDate($date) 
{
    if ($date && $date !== "0000-00-00") { 
               $date = $this->date_from_mysql($date); 
           } else { 
               $date = null; 
    }
    return $date;        
}

public function increment_user_date($date, $increment)
{
    $this->s->load_settings();
    
    $mysql_date = $this->date_from_mysql($date);

    $new_date = new DateTime($mysql_date);
    $new_date->add(new DateInterval('P' . $increment));

    return $new_date->format($this->s->setting('date_format'));
}

public function increment_date($date, $increment)
{
    $new_date = new DateTime($date);
    $new_date->add(new DateInterval('P' . $increment));
    return $new_date->format('Y-m-d');
}

function ensureDateTime ( $input, $immutable = NULL ) {
    if ( ! $input instanceof \DateTimeInterface ) {
        if ( in_array( $input, ['0000-00-00', '0000-00-00 00:00:00'], true ) ) {
            $input = false;
        } elseif ( $immutable ) {
            $input = new \DateTimeImmutable( $input );
        } else {
            $input = new \DateTime( $input );
        }
    } elseif ( true === $immutable && $input instanceof \DateTime ) {
        $input = new \DateTimeImmutable( $input->format(TIMESTAMPFORMAT), $input->getTimezone() );
    } elseif ( false === $immutable && $input instanceof \DateTimeImmutable ) {
        $input = new \DateTime( $input->format(TIMESTAMPFORMAT), $input->getTimezone() );
    }
    return $input;
}

}