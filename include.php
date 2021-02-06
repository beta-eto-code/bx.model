<?php
$arJsConfig = array(
    'model.ui' => array(
        'js' => '/local/modules/bx.model/lib/ui/js/script.js',
        'css' => '/bitrix/css/main/grid/webform-button.css',
        'rel' => [],
    )
);

foreach ($arJsConfig as $ext => $arExt) {
    \CJSCore::RegisterExt($ext, $arExt);
}