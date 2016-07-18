DROP TABLE IF EXISTS `#__rsform_constantcontact`;

DELETE FROM #__rsform_config WHERE SettingName = 'cc.api';
DELETE FROM #__rsform_config WHERE SettingName = 'cc.token';
