DROP TABLE IF EXISTS `#__rsform_verticalresponse`;

DELETE FROM #__rsform_config WHERE SettingName = 'verticalresponse.key';
DELETE FROM #__rsform_config WHERE SettingName = 'verticalresponse.secret';
DELETE FROM #__rsform_config WHERE SettingName = 'verticalresponse.token';
DELETE FROM #__rsform_config WHERE SettingName = 'verticalresponse.usessl';