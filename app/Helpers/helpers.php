<?php
/**
 * File name: helpers.php
 * Last modified: 2020.06.11 at 16:10:52
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 */

use InfyOm\Generator\Common\GeneratorCuisine;
use InfyOm\Generator\Utils\GeneratorCuisinesInputUtil;
use InfyOm\Generator\Utils\HTMLCuisineGenerator;
use Symfony\Component\Debug\Exception\FatalThrowableError;
// use Carbon\Carbon;
use App\Models\Stock;
use App\Models\StockHistory;
use App\Models\OrderHistory;
use App\Models\Order;
use Illuminate\Support\Facades\DB;




use App\Models\Restaurant;

/**
 * @param $bytes
 * @param int $precision
 * @return string
 */
function formatedSize($bytes, $precision = 1)
{
    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    $bytes /= pow(1024, $pow);

    return round($bytes, $precision) . ' ' . $units[$pow];
}

function getMediaColumn($mediaModel, $mediaCollectionName = '', $extraClass = '', $mediaThumbnail = 'icon')
{
    $extraClass = $extraClass == '' ? ' rounded ' : $extraClass;

    if ($mediaModel->hasMedia($mediaCollectionName)) {
        return "<img class='" . $extraClass . "' style='width:50px' src='" . $mediaModel->getFirstMediaUrl($mediaCollectionName, $mediaThumbnail) . "' alt='" . $mediaModel->getFirstMedia($mediaCollectionName)->name . "'>";
    }else{
        return "<img class='" . $extraClass . "' style='width:50px' src='" . asset('images/image_default.png') . "' alt='image_default'>";
    }
}

/**
 * @param $modelObject
 * @param string $attributeName
 * @return null|string|string[]
 */
function getDateColumn($modelObject, $attributeName = 'updated_at')
{
    if (setting('is_human_date_format', false)) {
        $html = '<p data-toggle="tooltip" data-placement="bottom" title="${date}">${dateHuman}</p>';
    } else {
        $html = '<p data-toggle="tooltip" data-placement="bottom" title="${dateHuman}">${date}</p>';
    }
    if (!isset($modelObject[$attributeName])) {
        return '';
    }
    $dateObj = new Carbon\Carbon($modelObject[$attributeName]);
    $replace = preg_replace('/\$\{date\}/', $dateObj->format(setting('date_format', 'l jS F Y (h:i:s)')), $html);
    $replace = preg_replace('/\$\{dateHuman\}/', $dateObj->diffForHumans(), $replace);
    return $replace;
}

function getPriceColumn($modelObject, $attributeName = 'price')
{

    if ($modelObject[$attributeName] != null && strlen($modelObject[$attributeName]) > 0) {
        $modelObject[$attributeName] = number_format((float)$modelObject[$attributeName], 2, '.', '');
        if (setting('currency_right', false) != false) {
            return $modelObject[$attributeName] . "<span>" . setting('default_currency') . "</span>";
        } else {
            return "<span>" . setting('default_currency') . "</span>" . $modelObject[$attributeName];
        }
    }
    return '-';
}

function getPrice($price = 0)
{
    if (setting('currency_right', false) != false) {
        return number_format((float)$price, 2, '.', '') . "<span>" . setting('default_currency') . "</span>";
    } else {
        return "<span>" . setting('default_currency') . "</span>" . number_format((float)$price, 2, '.', ' ');
    }
}

/**
 * generate boolean column for datatable
 * @param $column
 * @return string
 */
function getBooleanColumn($column, $attributeName)
{
    if (isset($column)) {
        if ($column[$attributeName]) {
            return "<span class='badge badge-success'>" . trans('lang.yes') . "</span>";
        } else {
            return "<span class='badge badge-danger'>" . trans('lang.no') . "</span>";
        }
    }
}


function getOrderStatut($column, $attributeName)
{
    // if (isset($column)) {
        if ($column[$attributeName] == 1) {
            return "<span class='badge badge-secondary'>" . $column[$attributeName] . "</span>";
        }if ($column[$attributeName] == 2) {
            return "<span class='badge badge-danger'>" . $column[$attributeName] . "</span>";
        }if ($column[$attributeName] == 3) {
            return "<span class='badge badge-info'>" . $column[$attributeName] . "</span>";
        }if ($column[$attributeName] == 4) {
            return "<span class='badge badge-warning'>" . $column[$attributeName] . "</span>";
        }if ($column[$attributeName] == 5) {
            return "<span class='badge badge-success'>" . $column[$attributeName] . "</span>";
        }
        else{
            return "<span class='badge badge-success'>" . $column[$attributeName] . "</span>";
        }
    // }
}

function getBooleanColumnSwitch($column, $attributeName)
{
    if (isset($column)) {
        if ($column[$attributeName]) {
            return "<input type='checkbox' class='checkbox' checked />";
        } else {
            return "<input type='checkbox' class='checkbox' />";
        }
    }
}

/**
 * generate not boolean column for datatable
 * @param $column
 * @return string
 */
function getNotBooleanColumn($column, $attributeName)
{
    if (isset($column)) {
        if ($column[$attributeName]) {
            return "<span class='badge badge-danger'>" . trans('lang.yes') . "</span>";
        } else {
            return "<span class='badge badge-success'>" . trans('lang.no') . "</span>";
        }
    }
}

/**
 * generate order payment column for datatable
 * @param $column
 * @return string
 */
function getPayment($column, $attributeName)
{
    if (isset($column) && $column[$attributeName]) {
        return "<span class='badge badge-success'>" . getStatus($column[$attributeName]) . "</span> ";
    } else {
        return "<span class='badge badge-danger'>" . trans('lang.order_not_paid') . "</span>";
    }
}

/**
 * @param array $array
 * @param $baseUrl
 * @param string $idAttribute
 * @param string $titleAttribute
 * @return string
 */
function getLinksColumn($array = [], $baseUrl, $idAttribute = 'id', $titleAttribute = 'title')
{
    $html = '<a href="${href}" class="text-bold text-dark">${title}</a>';
    $result = [];
    foreach ($array as $link) {
        $replace = preg_replace('/\$\{href\}/', url($baseUrl, $link[$idAttribute]), $html);
        $replace = preg_replace('/\$\{title\}/', $link[$titleAttribute], $replace);
        $result[] = $replace;
    }
    return implode(', ', $result);
}

/**
 * @param array $array
 * @param $routeName
 * @param string $idAttribute
 * @param string $titleAttribute
 * @return string
 */
function getLinksColumnByRouteName($array = [], $routeName, $idAttribute = 'id', $titleAttribute = 'title')
{
    $html = '<a href="${href}" class="text-bold text-dark">${title}</a>';
    $result = [];
    foreach ($array as $link) {
        $replace = preg_replace('/\$\{href\}/', route($routeName, $link[$idAttribute]), $html);
        $replace = preg_replace('/\$\{title\}/', $link[$titleAttribute], $replace);
        $result[] = $replace;
    }
    return implode(', ', $result);
}

function getArrayColumn($array = [], $titleAttribute = 'title', $extraClass = '', $separator = ', ')
{
    $result = [];
    foreach ($array as $link) {
        $title = $link[$titleAttribute];
//        $replace = preg_replace('/\$\{href\}/', url($baseUrl, $link[$idAttribute]), $html);
//        $replace = preg_replace('/\$\{title\}/', $link[$titleAttribute], $replace);
        $html = "<span class='{$extraClass}'>{$title}</span>";
        $result[] = $html;
    }
    return implode($separator, $result);
}

function getEmailColumn($column, $attributeName)
{
    if (isset($column)) {
        if ($column[$attributeName]) {
            return "<a class='btn btn-outline-secondary btn-sm' href='mailto:" . $column[$attributeName] . "'><i class='fa fa-envelope mr-1'></i>" . $column[$attributeName] . "</a>";
        } else {
            return '';
        }
    }
}

/**
 * get available languages on the application
 */
function getAvailableLanguages()
{
    $dir = base_path('resources/lang');
    $languages = array_diff(scandir($dir), array('..', '.'));
    $languages = array_map(function ($value) {
        if($value =='fr'){
            return ['id' => $value, 'value' => 'Français'];
            // return ['id' => $value, 'value' => trans('lang.app_setting_' . $value)];
        }
        
    }, $languages);

    // if(($key = array_search('0', $languages, TRUE)) !== 'fr') {
        // unset($languages['Portuguese']);
    // }

    return array_column($languages, 'value', 'id');
}

/**
 * get all languages
 */

function getLanguages()
{

    return array(
        'aa' => 'Afar',
        'ab' => 'Abkhaz',
        'ae' => 'Avestan',
        'af' => 'Afrikaans',
        'ak' => 'Akan',
        'am' => 'Amharic',
        'an' => 'Aragonese',
        'ar' => 'Arabic',
        'as' => 'Assamese',
        'av' => 'Avaric',
        'ay' => 'Aymara',
        'az' => 'Azerbaijani',
        'ba' => 'Bashkir',
        'be' => 'Belarusian',
        'bg' => 'Bulgarian',
        'bh' => 'Bihari',
        'bi' => 'Bislama',
        'bm' => 'Bambara',
        'bn' => 'Bengali',
        'bo' => 'Tibetan Standard, Tibetan, Central',
        'br' => 'Breton',
        'bs' => 'Bosnian',
        'ca' => 'Catalan; Valencian',
        'ce' => 'Chechen',
        'ch' => 'Chamorro',
        'co' => 'Corsican',
        'cr' => 'Cree',
        'cs' => 'Czech',
        'cu' => 'Old Church Slavonic, Church Slavic, Church Slavonic, Old Bulgarian, Old Slavonic',
        'cv' => 'Chuvash',
        'cy' => 'Welsh',
        'da' => 'Danish',
        'de' => 'German',
        'dv' => 'Divehi; Dhivehi; Maldivian;',
        'dz' => 'Dzongkha',
        'ee' => 'Ewe',
        'el' => 'Greek, Modern',
        'en' => 'English',
        'eo' => 'Esperanto',
        'es' => 'Spanish; Castilian',
        'et' => 'Estonian',
        'eu' => 'Basque',
        'fa' => 'Persian',
        'ff' => 'Fula; Fulah; Pulaar; Pular',
        'fi' => 'Finnish',
        'fj' => 'Fijian',
        'fo' => 'Faroese',
        'fr' => 'French',
        'fy' => 'Western Frisian',
        'ga' => 'Irish',
        'gd' => 'Scottish Gaelic; Gaelic',
        'gl' => 'Galician',
        'gn' => 'GuaranÃƒÂ­',
        'gu' => 'Gujarati',
        'gv' => 'Manx',
        'ha' => 'Hausa',
        'he' => 'Hebrew (modern)',
        'hi' => 'Hindi',
        'ho' => 'Hiri Motu',
        'hr' => 'Croatian',
        'ht' => 'Haitian; Haitian Creole',
        'hu' => 'Hungarian',
        'hy' => 'Armenian',
        'hz' => 'Herero',
        'ia' => 'Interlingua',
        'id' => 'Indonesian',
        'ie' => 'Interlingue',
        'ig' => 'Igbo',
        'ii' => 'Nuosu',
        'ik' => 'Inupiaq',
        'io' => 'Ido',
        'is' => 'Icelandic',
        'it' => 'Italian',
        'iu' => 'Inuktitut',
        'ja' => 'Japanese (ja)',
        'jv' => 'Javanese (jv)',
        'ka' => 'Georgian',
        'kg' => 'Kongo',
        'ki' => 'Kikuyu, Gikuyu',
        'kj' => 'Kwanyama, Kuanyama',
        'kk' => 'Kazakh',
        'kl' => 'Kalaallisut, Greenlandic',
        'km' => 'Khmer',
        'kn' => 'Kannada',
        'ko' => 'Korean',
        'kr' => 'Kanuri',
        'ks' => 'Kashmiri',
        'ku' => 'Kurdish',
        'kv' => 'Komi',
        'kw' => 'Cornish',
        'ky' => 'Kirghiz, Kyrgyz',
        'la' => 'Latin',
        'lb' => 'Luxembourgish, Letzeburgesch',
        'lg' => 'Luganda',
        'li' => 'Limburgish, Limburgan, Limburger',
        'ln' => 'Lingala',
        'lo' => 'Lao',
        'lt' => 'Lithuanian',
        'lu' => 'Luba-Katanga',
        'lv' => 'Latvian',
        'mg' => 'Malagasy',
        'mh' => 'Marshallese',
        'mi' => 'Maori',
        'mk' => 'Macedonian',
        'ml' => 'Malayalam',
        'mn' => 'Mongolian',
        'mr' => 'Marathi (Mara?hi)',
        'ms' => 'Malay',
        'mt' => 'Maltese',
        'my' => 'Burmese',
        'na' => 'Nauru',
        'nb' => 'Norwegian BokmÃƒÂ¥l',
        'nd' => 'North Ndebele',
        'ne' => 'Nepali',
        'ng' => 'Ndonga',
        'nl' => 'Dutch',
        'nn' => 'Norwegian Nynorsk',
        'no' => 'Norwegian',
        'nr' => 'South Ndebele',
        'nv' => 'Navajo, Navaho',
        'ny' => 'Chichewa; Chewa; Nyanja',
        'oc' => 'Occitan',
        'oj' => 'Ojibwe, Ojibwa',
        'om' => 'Oromo',
        'or' => 'Oriya',
        'os' => 'Ossetian, Ossetic',
        'pa' => 'Panjabi, Punjabi',
        'pi' => 'Pali',
        'pl' => 'Polish',
        'ps' => 'Pashto, Pushto',
        'pt' => 'Portuguese',
        'qu' => 'Quechua',
        'rm' => 'Romansh',
        'rn' => 'Kirundi',
        'ro' => 'Romanian, Moldavian, Moldovan',
        'ru' => 'Russian',
        'rw' => 'Kinyarwanda',
        'sa' => 'Sanskrit (Sa?sk?ta)',
        'sc' => 'Sardinian',
        'sd' => 'Sindhi',
        'se' => 'Northern Sami',
        'sg' => 'Sango',
        'si' => 'Sinhala, Sinhalese',
        'sk' => 'Slovak',
        'sl' => 'Slovene',
        'sm' => 'Samoan',
        'sn' => 'Shona',
        'so' => 'Somali',
        'sq' => 'Albanian',
        'sr' => 'Serbian',
        'ss' => 'Swati',
        'st' => 'Southern Sotho',
        'su' => 'Sundanese',
        'sv' => 'Swedish',
        'sw' => 'Swahili',
        'ta' => 'Tamil',
        'te' => 'Telugu',
        'tg' => 'Tajik',
        'th' => 'Thai',
        'ti' => 'Tigrinya',
        'tk' => 'Turkmen',
        'tl' => 'Tagalog',
        'tn' => 'Tswana',
        'to' => 'Tonga (Tonga Islands)',
        'tr' => 'Turkish',
        'ts' => 'Tsonga',
        'tt' => 'Tatar',
        'tw' => 'Twi',
        'ty' => 'Tahitian',
        'ug' => 'Uighur, Uyghur',
        'uk' => 'Ukrainian',
        'ur' => 'Urdu',
        'uz' => 'Uzbek',
        've' => 'Venda',
        'vi' => 'Vietnamese',
        'vo' => 'VolapÃƒÂ¼k',
        'wa' => 'Walloon',
        'wo' => 'Wolof',
        'xh' => 'Xhosa',
        'yi' => 'Yiddish',
        'yo' => 'Yoruba',
        'za' => 'Zhuang, Chuang',
        'zh' => 'Chinese',
        'zu' => 'Zulu',
    );

}

function generateCustomField($fields, $fieldsValues = null)
{
    $htmlFields = [];
    $startSeparator = '<div style="flex: 50%;max-width: 50%;padding: 0 4px;" class="column">';
    $endSeparator = '</div>';
    foreach ($fields as $field) {
        $dynamicVars = [
            '$RANDOM_VARIABLE$' => 'var' . time() . rand() . 'ble',
            '$FIELD_NAME$' => $field->name,
            '$DISABLED$' => $field->disabled === true ? '"disabled" => "disabled",' : '',
            '$REQUIRED$' => $field->required === true ? '"required" => "required",' : '',
            '$MODEL_NAME_SNAKE$' => getOnlyClassName($field->custom_field_model),
            '$FIELD_VALUE$' => 'null',
            '$INPUT_ARR_SELECTED$' => '[]',

        ];
        $gf = new \InfyOm\Generator\Common\GeneratorField();
        if ($fieldsValues) {
            foreach ($fieldsValues as $value) {
                if ($field->id === $value->customField->id) {
                    $dynamicVars['$INPUT_ARR_SELECTED$'] = $value->value ? $value->value: '[]';
                    $dynamicVars['$FIELD_VALUE$'] = '\'' . addslashes($value->value) . '\'';
                    $gf->validations[] = $value->value;
                    continue;
                }
            }
        }
        // dd($gf->validations);
        $gf->htmlType = $field['type'];
        $gf->htmlValues = $field['values'];
        $gf->dbInput = '';
        if ($field['type'] === 'selects') {
            $gf->htmlType = 'select';
            $gf->dbInput = 'hidden,mtm';
        }
        $fieldTemplate = \InfyOm\Generator\Utils\HTMLFieldGenerator::generateCustomFieldHTML($gf, config('infyom.laravel_generator.templates', 'adminlte-templates'));


        if (!empty($fieldTemplate)) {
            foreach ($dynamicVars as $variable => $value) {
                $fieldTemplate = str_replace($variable, $value, $fieldTemplate);
            }
            $htmlFields[] = $fieldTemplate;
        }
//    dd($fieldTemplate);
    }
    foreach ($htmlFields as $index => $field) {
        if (round(count($htmlFields) / 2) == $index + 1) {
            $htmlFields[$index] = $htmlFields[$index] . "\n" . $endSeparator . "\n" . $startSeparator;
        }
    }
    $htmlFieldsString = implode("\n\n", $htmlFields);
    $htmlFieldsString = $startSeparator . "\n" . $htmlFieldsString . "\n" . $endSeparator;
//    dd($htmlFieldsString);
    $renderedHtml = "";
    try {
        $renderedHtml = render(Blade::compileString($htmlFieldsString));
//        dd($renderedHtml);
    } catch (FatalThrowableError $e) {
    }
    return $renderedHtml;
}

/**
 * render php code in string give with compiling data
 *
 * @param $__php
 * @param null $__data
 * @return string
 * @throws FatalThrowableError
 */
function render($__php, $__data = null)
{
    $obLevel = ob_get_level();
    ob_start();
    if ($__data) {
        extract($__data, EXTR_SKIP);
    }
    try {
        eval('?' . '>' . $__php);
    } catch (Exception $e) {
        while (ob_get_level() > $obLevel) ob_end_clean();
        throw $e;
    } catch (Throwable $e) {
        while (ob_get_level() > $obLevel) ob_end_clean();
        throw new FatalThrowableError($e);
    }
    return ob_get_clean();
}

/**
 * get custom field value from custom fields collection given
 * @param null $customFields
 * @param $request
 * @return array
 */
function getCustomFieldsValues($customFields = null, $request = null)
{

    if (!$customFields) {
        return [];
    }
    if (!$request) {
        return [];
    }
    $customFieldsValues = [];
    foreach ($customFields as $cf) {
        $value = $request->input($cf->name);
        $view = $value;
        $fieldType = $cf->type;
        if ($fieldType === 'selects') {
            $view = GeneratorFieldsInputUtil::prepareKeyValueArrFromLabelValueStr($cf->values);
            $view = array_filter($view, function ($v) use ($value) {
                return in_array($v, $value);
            });
            $view = implode(', ', array_flip($view));
            $value = json_encode($value);
        } elseif ($fieldType === 'select' || $fieldType === 'radio') {
            $view = GeneratorFieldsInputUtil::prepareKeyValueArrFromLabelValueStr($cf->values);
            $view = array_flip($view)[$value];
        } elseif ($fieldType === 'boolean') {
            $view = getBooleanColumn(['0' => $view], '0');

        } elseif ($fieldType === 'password') {
            $view = str_repeat('•', strlen($value));
            $value = bcrypt($value);
        } elseif ($fieldType === 'date') {
            $view = getDateColumn(['date' => $view], 'date');
        } elseif ($fieldType === 'email') {
            $view = getEmailColumn(['email' => $view], 'email');
        } elseif ($fieldType === 'textarea') {
            $view = strip_tags($view);
        }


        $customFieldsValues[] = [
            'custom_field_id' => $cf->id,
            'value' => $value,
            'view' => $view
        ];
    }

    return $customFieldsValues;
}


/**
 * convert an array to assoc array using one attribute in the array
 * 0 => [
 *      name => 'The_Name'
 *      title => 'TITLE'
 * ]
 *
 * to
 *
 * The_Name => [
 *      name => 'The_Name'
 *      title => 'TITLE'
 * ]
 */
function convertToAssoc($collection, $key)
{
    $newCollection = [];
    foreach ($collection as $c) {
        $newCollection[$c[$key]] = $c;
    }
    return $newCollection;
}

/**
 * Get class name by giving the full name of th class
 * Ex:
 * $fullClassName = App\Models\UserModel
 * $isSnake = true
 * return
 * user_model
 * $fullClassName = App\Models\UserModel
 * $isSnake = false
 * return
 * UserModel
 * @param $fullClassName
 * @param bool $isSnake
 * @return mixed|string
 */
function getOnlyClassName($fullClassName, $isSnake = true)
{
    $modelNames = preg_split('/\\\\/', $fullClassName);
    if ($isSnake) {
        return snake_case(end($modelNames));
    }
    return end($modelNames);

}

function getModelsClasses(string $dir, array $excepts = null)
{
    if ($excepts === null) {
        $excepts = [
            'App\Models\Upload',
            'App\Models\CustomField',
            'App\Models\Media',
            'App\Models\CustomFieldValue',
        ];
    }
    $customFieldModels = array();
    $cdir = scandir($dir);
    foreach ($cdir as $key => $value) {
        if (!in_array($value, array(".", ".."))) {
            if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
                $customFieldModels[$value] = getModelsClasses($dir . DIRECTORY_SEPARATOR . $value);
            } else {
                $fullClassName = "App\\Models\\" . basename($value, '.php');
                if (!in_array($fullClassName, $excepts)) {
                    $customFieldModels[$fullClassName] = trans('lang.' . snake_case(basename($value, '.php')) . '_plural');
                }

            }
        }
    }
    return $customFieldModels;
}

function getNeededArray($delimiter = '|', $string = '', $input)
{
    $array = explode($delimiter, $string, 2);
    if (count($array) === 1) {
        return [$array[0] => $input];
    } else {
        return [$array[0] => getNeededArray($delimiter, $array[1], $input)];
    }
}


function getStatus($status)
{
    if ($status== 'Paid') {
        return "Payé";
    } else if ($status== 'Not Paid') {
        return "Non Payé";
    } else if ($status== 'Waiting for Client') {
        return "Attente de client";
    } else{
        return $status;
    }
}

class Coordinate {
    public $latitude    = 0;
    public $longitude   = 0;
    public $latRadian   = 0;
    public $longRadian  = 0;
    public function __construct($latitude, $longitude, $name = '', $description = '') {
        $this->latitude    = $latitude;
        $this->longitude   = $longitude;
        $this->name        = $name;
        $this->description = $description;

        $this->latRadian   = deg2rad($this->latitude);
        $this->longRadian  = deg2rad($this->longitude);
    }
}


function distance($latitude1, $longitude1, $latitude2, $longitude2) {
    
    $latitude1  = floatval($latitude1);
    $latitude2  = floatval($latitude2);
    $longitude1  = floatval($longitude1);
    $longitude2  = floatval($longitude2);
    $point1 = new Coordinate($latitude1, $longitude1);
    $point2 = new Coordinate($latitude2, $longitude2);
    $a     = 6378137;
    $b     = 6356752.3141;
    $f     = ($a - $b) / $a;  //flattening of the ellipsoid
    $L     = $point2->longRadian - $point1->longRadian;  //difference in longitude
    $U1    = atan((1 - $f) * tan($point1->latRadian));  //U is 'reduced latitude'
    $U2    = atan((1 - $f) * tan($point2->latRadian));
    $sinU1 = sin($U1);
    $sinU2 = sin($U2);
    $cosU1 = cos($U1);
    $cosU2 = cos($U2);

    $lambda  = $L;
    $lambdaP = 2 * pi();
    $i = 20;

    while(abs($lambda - $lambdaP) > 1e-12 and
          --$i > 0) {
        $sinLambda = sin($lambda);
        $cosLambda = cos($lambda);
        $sinSigma  = sqrt(($cosU2 * $sinLambda) * ($cosU2 * $sinLambda) + ($cosU1 * $sinU2 - $sinU1 * $cosU2 * $cosLambda) * ($cosU1 * $sinU2 - $sinU1 * $cosU2 * $cosLambda));

        if($sinSigma == 0)
            return 0;  //co-incident points

        $cosSigma   = $sinU1 * $sinU2 + $cosU1 * $cosU2 * $cosLambda;
        $sigma      = atan2($sinSigma, $cosSigma);
        $sinAlpha   = $cosU1 * $cosU2 * $sinLambda / $sinSigma;
        $cosSqAlpha = 1 - $sinAlpha * $sinAlpha;
        $cos2SigmaM = $cosSigma - 2 * $sinU1 * $sinU2 / $cosSqAlpha;
        if(is_nan($cos2SigmaM))
            $cos2SigmaM = 0;  //equatorial line: cosSqAlpha=0 (6)
        $c = $f / 16 * $cosSqAlpha * (4 + $f * (4 - 3 * $cosSqAlpha));
        $lambdaP = $lambda;
        $lambda = $L + (1 - $c) * $f * $sinAlpha * ($sigma + $c * $sinSigma * ($cos2SigmaM + $c * $cosSigma * (-1 + 2 * $cos2SigmaM * $cos2SigmaM)));
    }

    if($i == 0)
        return false;  //formula failed to converge

    $uSq = $cosSqAlpha * ($a * $a - $b * $b) / ($b * $b);
    $A   = 1 + $uSq / 16384 * (4096 + $uSq * (-768 + $uSq * (320 - 175 * $uSq)));
    $B   = $uSq / 1024 * (256 + $uSq * (-128 + $uSq * (74 - 47 * $uSq)));
    $deltaSigma = $B * $sinSigma * ($cos2SigmaM + $B / 4 * ($cosSigma * (-1 + 2 * $cos2SigmaM * $cos2SigmaM) - $B / 6 * $cos2SigmaM * (-3 + 4 * $sinSigma * $sinSigma) * (-3 + 4 * $cos2SigmaM * $cos2SigmaM)));
    $d = $b * $A * ($sigma - $deltaSigma);
    return number_format($d, 3, '.', '');
}


    function GetRestaurantOrder($lat,$lang){
        // Get all active restaurants
        $restaurants = Restaurant::select('latitude','longitude','id','start_date','end_date')->where('active',1)->get();
        if(count($restaurants)>0){
            foreach($restaurants as $point){
                $point->distance = floatval(distance($lat,$lang,$point->latitude,$point->longitude));
            }
            $array = $restaurants->toArray();
            // Sort restaurant's distance
            usort($array, function($a, $b){
                if ($a["distance"] == $b["distance"])
                    return (0);
                return (($a["distance"] < $b["distance"]) ? -1 : 1);
             });
            //  Get the restaurant with small distance and open 
            $restau = new stdClass();
            // $restau =$array['0'];
            foreach($array as $row){
                // return $row;exit();
                if($row['start_date'] <=  Carbon\Carbon::now()->format('H:i:s') && $row['end_date'] >=  Carbon\Carbon::now()->format('H:i:s')){
                    $restau = $row ;
                    break;
                }
            }
            ;
            if((empty((array) $restau))){
                $restau = $array['0'];
            }
            return $restau;

            
             // $restaurant = array_reduce($array,function($A,$B){
            //     return $A['distance'] < $B['distance'] ? $A : $B;
            // }, array_shift($array));
            
        }
    }


    function addToStock($id,$qty,$task){
        $stock = Stock::findOrfail($id);
        if($stock){
            StockHistory::create([
                'quantity' => $qty,
                'stock_id' => $id,
                'user_id' => auth()->user()->id ,
                'task' =>$task
            ]);
            return 'success';
        }
    }


    function AddOrderHistory($id,$del_time,$statuID,$design){
        $order = Order::findOrfail($id);
        if($order){
            OrderHistory::create([
                'order_id' => $order->id,
                'statut_id' => $statuID,
                'text' => $design ,
                'delivery_time' =>$del_time
            ]);
        }
        
    }

    function getDeliveryFees($orderID){
        $order = Order::findOrfail($orderID);
        $total = 0;
        // return($order->foodOrders);
        if($order){
            foreach ($order->foodOrders as $foodOrder) {
                foreach ($foodOrder->extras as $extra) {
                    $foodOrder->price += $extra->price;
                }
                
                $total += $foodOrder->price * $foodOrder->quantity;
            }
            $fees=0;
            $settings = DB::table('app_settings')->where('key','average_price')->first();
            //  Without Coupon
            if($settings){
                if($total >= (float)$settings->value){
                    $fees_row = DB::table('app_settings')->where('key','more_price_fees')->first();
                    $fees = (float)$fees_row->value;
                }else if($total < (float)$settings->value){
                    $fees_row = DB::table('app_settings')->where('key','less_price_fees')->first();
                    $fees = (float)$fees_row->value;
                }
                
            }
            return $fees;    
        }
        
    }


    function generateCode($orderId){
        $lowerCase= ['a','b','C','B','c','d','O','Z','e','f','g','h','A','N','I','x'];

        $keys = array_rand( $lowerCase,3);

        $qrcode =  $orderId.$lowerCase[$keys[1]].$lowerCase[$keys[2]].$lowerCase[$keys[0]];
        // if(Order::where('qrcode',$qrcode)->exists()){}
        
        return $qrcode;
    }
