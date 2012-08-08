<?php

echo $view->checkBox('VirusCheckStatus', 'enabled')
    ->setAttribute('uncheckedValue', 'disabled');

echo $view->fieldsetSwitch('SpamCheckStatus', 'enabled', $view::FIELDSETSWITCH_CHECKBOX | $view::FIELDSETSWITCH_EXPANDABLE)
    ->setAttribute('uncheckedValue', 'disabled')
    ->insert($view->checkBox('SpamFolder', 'junkmail')->setAttribute('uncheckedValue', ''))
    ;

echo $view->checkBox('BlockAttachmentStatus', 'enabled')
    ->setAttribute('uncheckedValue', 'disabled');

echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_HELP);