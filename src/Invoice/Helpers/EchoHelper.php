<?php
Namespace frontend\modules\invoice\application\helpers;

Use  frontend\modules\invoice\application\models\Mdl_settings;
use  frontend\modules\invoice\application\helpers\TransHelper;
Use  yii\helpers\Url;
Use  yii\base\Component;
Use Yii;

Class EchoHelper extends Component
{

function htmlsc($output)
{
    return htmlspecialchars($output, ENT_QUOTES);
}

/**
 * Echo something with escaped HTML special chars
 *
 * @param mixed $output
 *
 * @return void
 */
function _htmlsc($output)
{
    echo htmlspecialchars($output, ENT_QUOTES);
}

/**
 * Echo something with escaped HTML entities
 *
 * @param mixed $output
 *
 * @return void
 */
function _htmle($output)
{
    echo htmlentities($output);
}

/**
 * Echo a language string with the trans helper
 *
 * @param string $line
 * @param string $id
 * @param null|string $default
 *
 * @return void
 */
function _trans($line, $id = '', $default = null)
{
    $trans_helper = new TransHelper();
    echo $trans_helper->trans($line, $id, $default);
}

/**
 * Echo for the auto link function with special chars handling
 *
 * @param $str
 * @param string $type
 * @param bool $popup
 *
 * @return void
 */
function _auto_link($str, $type = 'both', $popup = false)
{
    echo auto_link(htmlsc($str), $type, $popup);
}

/**
 * Output the standard CSRF protection field
 *
 * @return void
 */
function _csrf_field()
{
    echo '<input type="hidden" name="' . Yii::$app->request->csrfParam;
    echo '" value="' . Yii::$app->request->getCsrfToken() . '">';            
}

/**
 * Returns the correct URL for a asset within the theme directory
 * Also appends the current version to the asset to prevent browser caching issues
 *
 * @param string $asset
 *
 * @return void
 */
function _theme_asset($asset)
{
    $mdl_settings = new Mdl_settings();
    echo Url::toRoute('/') . 'assets/' . $mdl_settings->get_setting('system_theme', 'invoiceplane');
    echo '/' . $asset . '?v=' . $mdl_settings->get_setting('current_version');
}

/**
 * Returns the correct URL for a asset within the core directory
 * Also appends the current version to the asset to prevent browser caching issues
 *
 * @param string $asset
 *
 * @return void
 */
function _core_asset($asset)
{
    $mdl_settings = new Mdl_settings();
    $addon = $mdl_settings->get_setting('current_version');
    if (empty($addon)){$addon = '1.5.11';}
    echo '/core/' . $asset . '?v='.$addon;
}
}