DROP TABLE IF EXISTS `#__rsform_getresponse`;

DELETE FROM #__rsform_config WHERE SettingName = 'getresponse.key';
DELETE FROM #__rsform_config WHERE SettingName = 'getresponse.usessl';
