DROP TABLE IF EXISTS `#__rsform_payment`;

DELETE FROM #__rsform_config WHERE SettingName = 'payment.currency'
DELETE FROM #__rsform_config WHERE SettingName = 'payment.thousands'
DELETE FROM #__rsform_config WHERE SettingName = 'payment.decimal'
DELETE FROM #__rsform_config WHERE SettingName = 'payment.nodecimals'
DELETE FROM #__rsform_config WHERE SettingName = 'payment.mask'
DELETE FROM #__rsform_config WHERE SettingName = 'payment.totalmask'

DELETE FROM #__rsform_component_types WHERE ComponentTypeId IN (21, 22, 23, 27, 28);
DELETE FROM #__rsform_component_type_fields WHERE ComponentTypeId IN (21, 22, 23, 27, 28);

