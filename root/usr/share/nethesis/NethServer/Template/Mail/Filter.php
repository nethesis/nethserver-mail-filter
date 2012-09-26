<?php

/* @var $view Nethgui\Renderer\Xhtml */

$virusCheckbox = $view->checkBox('VirusCheckStatus', 'enabled')
    ->setAttribute('uncheckedValue', 'disabled');

$view->includeTranslations(array(
    'New SB',
    'New RW',
    'New SW',
    'Delete',
    'Done',
    'Update',
    'allow To',
    'allow From',
    'deny To',
    'deny From',
));
$view->includeFile('NethServer/Js/nethserver.collectioneditor.filter.js');
$view->includeFile('NethServer/Css/nethserver.collectioneditor.filter.css');

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
    ->insert(
    $view->fieldset('', $view::FIELDSET_EXPANDABLE)->setAttribute('template', $T('Addresses ACL'))
    ->insert(
        $view->collectionEditor('AddressAcl', $view::LABEL_NONE)
        ->setAttribute('class', 'Filter')
        ->setAttribute('dimensions', '10x30')
    )
    )
;

$fileTypesCheckbox = $view->checkBox('BlockAttachmentStatus', 'enabled', $view::STATE_DISABLED)
    ->setAttribute('uncheckedValue', 'disabled');

echo $view->panel()
    ->insert($fileTypesCheckbox)
    ->insert($virusCheckbox)
    ->insert($spamCheckbox)
;

echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_HELP);