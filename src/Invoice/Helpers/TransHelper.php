<?php
Namespace frontend\modules\invoice\application\helpers;

Use  Yii;
Use  frontend\modules\invoice\application\libraries\Lang;
use  frontend\modules\invoice\application\helpers\DirectoryHelper;
Use  yii\base\Component;
use  frontend\modules\invoice\application\models\ci\Mdl_settings;

Class TransHelper extends Component
{
    
public function trans($line, $id = '', $default = null)
{
    $lang = new Lang();
    $lang_string = $lang->line($line);

    // Fall back to default language if the current language has no translated string
    if (empty($lang_string)) {
        $current_language = '';//Yii::$app->params['userdata_user_language'];

        if (empty($current_language)) {
            // todo gives error at startup, fix later
            $current_language = 'English'; //get_setting('default_language');
        }

        // Load the default language and translate the string
        $this->set_language('English');
        $lang_string = $lang->line($line);

        // Restore the application language to its previous setting
        $this->set_language($current_language);
    }

    // Fall back to the $line value if the default language has no translation either
    if (empty($lang_string)) {
        $lang_string = $default != null ? $default : $line;
    }

    if ($id != '') {
        $lang_string = '<label for="' . $id . '">' . $lang_string . '</label>';
    }

    return $lang_string;
}

public function set_language($language)
{
    $mdl_settings = new Mdl_settings();
    $mdl_settings->load_settings();  

    // Clear the current loaded language
    $lang = new Lang();
    $lang->_is_loaded = [];
    $lang->_language = [];

    // Load system language if no custom language is set
    $default_lang = isset($mdl_settings) ? $mdl_settings->setting('default_language') : 'English';
    $new_language = ('system' ? $default_lang : $language);

    // Set the new language
    $lang->load('ip', $new_language);
    $lang->load('form_validation', $new_language);
    $lang->load('custom', $new_language);
    $lang->load('gateway', $new_language);
}

public function get_available_languages()
{
    $directory = new DirectoryHelper();
    $apppath = $dir = Yii::getAlias('@app').'/modules/invoice/application/language';
    $languages = $directory->directory_map($apppath, true);
    sort($languages);

    for ($i = 0; $i < count($languages); $i++) {
        $languages[$i] = str_replace(['\\', '/'], '', $languages[$i]);
    }

    return $languages;
}
}