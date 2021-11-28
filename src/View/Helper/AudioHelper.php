<?php
/**
 * Tatoeba Project, free collaborative creation of multilingual corpuses project
 * Copyright (C) 2016  Gilles Bedel
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace App\View\Helper;

use App\View\Helper\AppHelper;


class AudioHelper extends AppHelper
{
    public $helpers = array(
        'AudioLicense',
        'Form',
        'Html',
        'Url',
    );

    private function defaultAttribUrl($username) {
        return array(
            'controller' => 'user',
            'action' => 'profile',
            $username
        );
    }

    public function displayAudioInfo($audio) {
        $username = $this->_View->safeForAngular($audio->author);
        $attribUrl = $this->_View->safeForAngular($audio->attribution_url);
        if (!empty($attribUrl)) {
            $username = $this->Html->link($username, $attribUrl);
        }
        $license = $this->AudioLicense->getLicenseName($audio->license);
?>
<h3>
  <audio-button audios="<?= h(json_encode([$audio])) ?>"></audio-button>
  <span><?= format(__('by {username}'), compact('username')) ?></span>
</h3>
<?= format(__('License: {license}'), compact('license')) ?>
<?php
    }

    public function displayAudioEditForm($audio) {
        echo $this->Form->create('Audio', [
            'type' => 'post',
            'url' => [
                'controller' => 'audio',
                'action' => 'edit',
                $audio->id
            ],
        ]);
        echo __d('admin', 'Enabled');
        echo $this->Form->control('enabled', [
            'label' => false,
            'type' => 'radio',
            'options' => array(
                1 => __d('admin', 'Yes'),
                0 => __d('admin', 'No')
            ),
            'value' => $audio->enabled,
        ]);

        echo $this->Form->control('author', [
            'label' => __d('admin', 'Author'),
            'value' => $this->_View->safeForAngular($audio->author),
        ]);
        echo $this->Form->submit(__d('admin', 'Submit'));
        echo $this->Form->end();
    }

    public function formatLicenceMessage($audioSettings, $username) {
        $url = empty($audioSettings['audio_attribution_url']) ?
               $this->defaultAttribUrl($username) :
               $audioSettings['audio_attribution_url'];
        $userLink = $this->Html->link($username, $url);

        $license = $audioSettings['audio_license'];
        if (empty($license)) {
            $msg = __('You may not reuse the following audio recordings '.
                      'outside the Tatoeba project because {userName} has '.
                      'not chosen any license for them.');
        } elseif ($license == 'Public domain') {
            $msg = __('The following audio recordings by '.
                      '{userName}, are licensed under the public domain.');
        } elseif ($this->AudioLicense->isKnownLicense($license)) {
            $license = $this->AudioLicense->getLicenseName($license);
            $msg = __('The following audio recordings by '.
                      '{userName}, are licensed under the {licenseName} '.
                      'license.');
        } else {
            $msg = __('The following audio recordings by '.
                      '{userName}, are licensed under an unknown license.');
        }
        return format($msg, array(
            'userName' => $userLink,
            'licenseName' => $license
        ));
    }
}
?>
