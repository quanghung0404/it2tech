DROP TABLE IF EXISTS `#__rsform_campaignmonitor`;

DELETE FROM #__rsform_config WHERE SettingName = 'campaignmonitor.api';
DELETE FROM #__rsform_config WHERE SettingName = 'campaignmonitor.client';
DELETE FROM #__rsform_config WHERE SettingName = 'campaignmonitor.usessl';
