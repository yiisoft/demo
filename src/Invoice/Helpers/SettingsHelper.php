<?php
namespace frontend\modules\invoice\application\helpers;

use frontend\modules\invoice\application\models\ci\Mdl_settings;
use yii\base\Component;

Class SettingsHelper extends Component
{

private $mdl_settings;    

public function init()
{
    parent::init();
    $this->mdl_settings = new Mdl_settings();
    $this->mdl_settings->load_settings();
}
    
public function get_setting($setting_key, $default = '', $escape = false)
{
    $value = $this->mdl_settings->setting($setting_key, $default);
    return $escape ? htmlsc($value) : $value;
}

public function check_select($value1, $value2 = null, $operator = '==', $checked = false)
{
    $select = $checked ? 'checked="checked"' : 'selected="selected"';

    // Instant-validate if $value1 is a bool value
    if (is_bool($value1) && $value2 === null) {
        echo $value1 ? $select : '';
        return;
    }

    switch ($operator) {
        case '==':
            $echo_selected = $value1 == $value2 ? true : false;
            break;
        case '!=':
            $echo_selected = $value1 != $value2 ? true : false;
            break;
        case 'e':
            $echo_selected = empty($value1) ? true : false;
            break;
        case '!e':
            $echo_selected = empty($value1) ? true : false;
            break;
        default:
            $echo_selected = $value1 ? true : false;
            break;
    }

    echo $echo_selected ? $select : '';
}
}