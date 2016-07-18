DROP TABLE IF EXISTS `#__rsform_mailchimp`;

DELETE FROM #__rsform_config WHERE SettingName = 'mailchimp.key';
DELETE FROM #__rsform_config WHERE SettingName = 'mailchimp.secure';
