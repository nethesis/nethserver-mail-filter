<?php


if(strlen($view->getModule()->rblServers) > 0) {
  $rblCheckbox = $view->checkbox('RblStatus', 'enabled')
    ->setAttribute('uncheckedValue', 'disabled');
} else {
  $rblCheckbox = $view->checkbox('RblStatus', 'enabled', $view::STATE_DISABLED)
    ->setAttribute('value', '');
}

$spfCheckbox = $view->checkBox('SpfStatus', 'enabled')
    ->setAttribute('uncheckedValue', 'disabled');

$virusCheckbox = $view->checkBox('VirusCheckStatus', 'enabled')
    ->setAttribute('uncheckedValue', 'disabled');

$spamCheckbox = $view->fieldsetSwitch('SpamCheckStatus', 'enabled', $view::FIELDSETSWITCH_CHECKBOX | $view::FIELDSETSWITCH_EXPANDABLE)
    ->setAttribute('uncheckedValue', 'disabled')
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

$fileTypesCheckbox = $view->checkBox('BlockAttachmentStatus', 'enabled', $view::STATE_DISABLED)
    ->setAttribute('uncheckedValue', 'disabled');


echo $view->fieldset()->setAttribute('template', $T('SMTP session checks'))
    ->insert($rblCheckbox)
    ->insert($spfCheckbox)
    ->insert($fileTypesCheckbox)
;

echo $view->fieldset()->setAttribute('template', $T('Message content checks'))
    ->insert($virusCheckbox)
    ->insert($spamCheckbox)
;

echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_HELP);