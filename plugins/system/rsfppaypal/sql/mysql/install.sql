INSERT IGNORE INTO `#__rsform_config` (`SettingName`, `SettingValue`) VALUES
('paypal.email', ''),
('paypal.return', ''),
('paypal.test', '0'),
('paypal.cancel', ''),
('paypal.language', 'US'),
('paypal.tax.type', '1'),
('paypal.tax.value', '');

INSERT IGNORE INTO `#__rsform_component_types` (`ComponentTypeId`, `ComponentTypeName`) VALUES (500, 'paypal');

DELETE FROM #__rsform_component_type_fields WHERE ComponentTypeId = 500;
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES
(500, 'NAME', 'textbox', '', 0),
(500, 'LABEL', 'textbox', '', 1),
(500, 'COMPONENTTYPE', 'hidden', '500', 2),
(500, 'LAYOUTHIDDEN', 'hiddenparam', 'YES', 7);