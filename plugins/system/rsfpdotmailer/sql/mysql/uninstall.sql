DROP TABLE IF EXISTS `#__rsform_dotmailer`;

DELETE FROM #__rsform_config WHERE SettingName = 'dotmailer.username';
DELETE FROM #__rsform_config WHERE SettingName = 'dotmailer.password';
