<?php

echo $view->checkBox('VirusCheckStatus', 'enabled')
    ->setAttribute('uncheckedValue', 'disabled');

echo $view->checkBox('SpamCheckStatus', 'enabled')
    ->setAttribute('uncheckedValue', 'disabled');

echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_HELP);