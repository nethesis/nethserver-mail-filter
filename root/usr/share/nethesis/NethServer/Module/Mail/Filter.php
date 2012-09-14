<?php
namespace NethServer\Module\Mail;

/*
 * Copyright (C) 2012 Nethesis S.r.l.
 *
 * This script is part of NethServer.
 *
 * NethServer is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * NethServer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with NethServer.  If not, see <http://www.gnu.org/licenses/>.
 */

use Nethgui\System\PlatformInterface as Validate;
use Nethgui\Controller\Table\Modify as Table;

/**
 * Mail filter properties for Amavis
 *
 * @author Davide Principi <davide.principi@nethesis.it>
 * @since 1.0
 */
class Filter extends \Nethgui\Controller\AbstractController
{
    public $spamTagLevel;
    public $spamDsnLevel;

    public function initialize()
    {
        $this->spamTagLevel = $this->getPlatform()
            ->getDatabase('configuration')
            ->getProp('amavisd', 'SpamTagLevel')
        ;
        $this->spamDsnLevel = $this->getPlatform()
            ->getDatabase('configuration')
            ->getProp('amavisd', 'SpamDsnLevel')
        ;

        $this->declareParameter('VirusCheckStatus', Validate::SERVICESTATUS, array('configuration', 'amavisd', 'VirusCheckStatus'));
        $this->declareParameter('SpamCheckStatus', Validate::SERVICESTATUS, array('configuration', 'amavisd', 'SpamCheckStatus'));
        $this->declareParameter('BlockAttachmentStatus', Validate::SERVICESTATUS, array('configuration', 'amavisd', 'BlockAttachmentStatus'));
        $this->declareParameter('SpamSubjectPrefixStatus', Validate::SERVICESTATUS, array('configuration', 'amavisd', 'SpamSubjectPrefixStatus'));
        $this->declareParameter('SpamSubjectPrefixString', $this->createValidator()->maxLength(16), array('configuration', 'amavisd', 'SpamSubjectPrefixString'));
        $this->declareParameter('SpamTag2Level', $this->createValidator()->lessThan($this->spamDsnLevel)->greatThan($this->spamTagLevel), array('configuration', 'amavisd', 'SpamTag2Level'));
        $this->declareParameter('SpamKillLevel', $this->createValidator()->lessThan($this->spamDsnLevel)->greatThan($this->spamTagLevel), array('configuration', 'amavisd', 'SpamKillLevel'));

        $this->declareParameter('AddressAcl', Validate::ANYTHING, array(
            array('configuration', 'amavisd', 'RecipientWhiteList'),
            array('configuration', 'amavisd', 'SenderWhiteList'),
            array('configuration', 'amavisd', 'SenderBlackList'),
        ));
    }

    public function readAddressAcl($recipientWhiteList, $senderWhiteList, $senderBlackList)
    {
        $addressAcl = '';

        $addressAcl .= implode(":RW\r\n", explode(',', $recipientWhiteList)) . ":RW\r\n";
        $addressAcl .= implode(":SW\r\n", explode(',', $senderWhiteList)) . ":SW\r\n";
        $addressAcl .= implode(":SB\r\n", explode(',', $senderBlackList)) . ":SB\r\n";

        return $addressAcl;
    }

    public function writeAddressAcl($addressAcl)
    {
        $acls = array();

        foreach (explode("\n", $addressAcl) as $line) {
            $parts = array();
            if (preg_match('/^\s*([^:\s]+):([^\s]+)/', $line, $parts) > 0) {
                $acls[$parts[2]][] = $parts[1];
            }
        }
        
        return array(
            implode(',', array_unique($acls['RW'])), // $recipientWhiteList
            implode(',', array_unique($acls['SW'])), // $senderWhiteList
            implode(',', array_unique($acls['SB']))  // $senderBlackList
        );
    }

    public function validate(\Nethgui\Controller\ValidationReportInterface $report)
    {
        $this->getValidator('SpamTag2Level')->lessThan($this->parameters['SpamKillLevel']);
        parent::validate($report);
    }

    protected function onParametersSaved($changedParameters)
    {
        $this->getPlatform()->signalEvent('nethserver-mail-filter-save@post-process');
    }

    public function prepareView(\Nethgui\View\ViewInterface $view)
    {
        parent::prepareView($view);

        if ($this->parameters['VirusCheckStatus'] === 'enabled'
            && $this->antivirusDatabaseIsObsolete()) {
            $view->getCommandList('/Notification')->showMessage($view->translate('AVDB_OBSOLETE'), \Nethgui\Module\Notification\AbstractNotification::NOTIFY_ERROR);
        }
    }

    private function antivirusDatabaseIsObsolete()
    {
        $max = 0;
        $fileList = glob('/var/clamav/*.{cvd,cld}', GLOB_BRACE);
        foreach ($fileList as $file) {
            $changeTime = filemtime($file);
            if ($changeTime > $max) {
                $max = $changeTime;
            }
        }

        if (time() - $max > 3600 * 24 * 5) {
            return TRUE;
        }

        return FALSE;
    }

}
