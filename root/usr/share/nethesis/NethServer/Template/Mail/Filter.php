<?php

echo $view->checkBox('VirusCheckStatus', 'enabled')
    ->setAttribute('uncheckedValue', 'disabled');

if(strlen($view->getModule()->rblServers) > 0) {
  $rblCheckbox = $view->checkbox('RblStatus', 'enabled')
    ->setAttribute('uncheckedValue', 'disabled');
} else {
  $rblCheckbox = $view->checkbox('RblStatus', 'enabled', $view::STATE_DISABLED)
    ->setAttribute('value', '');
}

echo $view->fieldsetSwitch('SpamCheckStatus', 'enabled', $view::FIELDSETSWITCH_CHECKBOX | $view::FIELDSETSWITCH_EXPANDABLE)
    ->setAttribute('uncheckedValue', 'disabled')
    ->insert($rblCheckbox)
    ->insert($view->slider('SpamTag2Level', $view::LABEL_ABOVE)
        ->setAttribute('min', $view->getModule()->spamTagLevel + 0.1)
        ->setAttribute('max', $view->getModule()->spamDsnLevel - 0.1)
        ->setAttribute('step', 0.1)
        ->setAttribute('label', $T('SpamTag2Level ${0}'))        
        )
    ->insert($view->slider('SpamKillLevel', $view::LABEL_ABOVE)
        ->setAttribute('min', $view->getModule()->spamTagLevel + 0.1)
        ->setAttribute('max', $view->getModule()->spamDsnLevel - 0.1)
        ->setAttribute('step', 0.1)
        ->setAttribute('label', $T('SpamKillLevel ${0}'))
        )    
    ->insert(
        $view->fieldsetSwitch('SpamSubjectPrefixStatus', 'enabled', $view::FIELDSETSWITCH_CHECKBOX | $view::FIELDSETSWITCH_EXPANDABLE)
        ->setAttribute('uncheckedValue', 'disabled')
        ->insert($view->textInput('SpamSubjectPrefixString', $view::LABEL_NONE))
    )
;

echo $view->checkBox('BlockAttachmentStatus', 'enabled', $view::STATE_DISABLED)
    ->setAttribute('uncheckedValue', 'disabled');

echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_HELP);