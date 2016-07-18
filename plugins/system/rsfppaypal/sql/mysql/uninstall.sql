DROP TABLE IF EXISTS `#__rsform_payment`;

DELETE FROM #__rsform_config WHERE SettingName = 'payment.email';
DELETE FROM #__rsform_config WHERE SettingName = 'payment.return';
DELETE FROM #__rsform_config WHERE SettingName = 'payment.test';
DELETE FROM #__rsform_config WHERE SettingName = 'payment.cancel';
DELETE FROM #__rsform_config WHERE SettingName = 'payment.language';
DELETE FROM #__rsform_config WHERE SettingName = 'payment.tax.type';
DELETE FROM #__rsform_config WHERE SettingName = 'payment.tax.value';

DELETE FROM #__rsform_component_types WHERE ComponentTypeId = 500;
DELETE FROM #__rsform_component_type_fields WHERE ComponentTypeId = 500;
