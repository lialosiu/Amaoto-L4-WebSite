<?php

function _c(array $p = array())
{
    if (is_array($p))
        foreach ($p as $t) {
            if ($t) return $t;
        }
    return NULL;
}

$array = array();
$array['installed'] = AmaotoOption::getValueByKey('installed');
$array['site-name'] = _c(array(AmaotoOption::getValueByKey('site-name'), 'Amaoto System'));
$array['version'] = '0.8.141024';
$array['copyright-first-year'] = _c(array(AmaotoOption::getValueByKey('copyright-first-year'), date('Y')));
$array['copyright-year'] = ($array['copyright-first-year'] == date('Y')) ? $array['copyright-first-year'] : $array['copyright-first-year'] . '-' . date('Y');
$array['copyright-name'] = _c(array(AmaotoOption::getValueByKey('copyright-name'), 'Amaoto System'));

return $array;