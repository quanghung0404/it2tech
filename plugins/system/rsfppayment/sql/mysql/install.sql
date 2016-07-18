INSERT IGNORE INTO `#__rsform_config` (`SettingName`, `SettingValue`) VALUES ('payment.currency', 'USD');
INSERT IGNORE INTO `#__rsform_config` (`SettingName`, `SettingValue`) VALUES ('payment.thousands', ',');
INSERT IGNORE INTO `#__rsform_config` (`SettingName`, `SettingValue`) VALUES ('payment.decimal', '.');
INSERT IGNORE INTO `#__rsform_config` (`SettingName`, `SettingValue`) VALUES ('payment.nodecimals', '2');
INSERT IGNORE INTO `#__rsform_config` (`SettingName`, `SettingValue`) VALUES ('payment.mask', '{product} - {price} {currency}');
INSERT IGNORE INTO `#__rsform_config` (`SettingName`, `SettingValue`) VALUES ('payment.totalmask', '{price} {currency}');

INSERT IGNORE INTO `#__rsform_component_types` (`ComponentTypeId`, `ComponentTypeName`) VALUES (21, 'singleProduct');
INSERT IGNORE INTO `#__rsform_component_types` (`ComponentTypeId`, `ComponentTypeName`) VALUES (22, 'multipleProducts');
INSERT IGNORE INTO `#__rsform_component_types` (`ComponentTypeId`, `ComponentTypeName`) VALUES (28, 'donationProduct');
INSERT IGNORE INTO `#__rsform_component_types` (`ComponentTypeId`, `ComponentTypeName`) VALUES (23, 'total');
INSERT IGNORE INTO `#__rsform_component_types` (`ComponentTypeId`, `ComponentTypeName`) VALUES (27, 'choosePayment');

UPDATE #__rsform_component_types SET `ComponentTypeName`='singleProduct' WHERE `ComponentTypeName` = 'paypalSingleProduct';
UPDATE #__rsform_component_types SET `ComponentTypeName`='multipleProducts' WHERE `ComponentTypeName` = 'paypalMultipleProducts';
UPDATE #__rsform_component_types SET `ComponentTypeName`='total' WHERE `ComponentTypeName` = 'paypalTotal';

DELETE FROM #__rsform_component_type_fields WHERE ComponentTypeId IN (21, 22, 23, 27, 28);

INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES
(21, 'PRICE', 'textbox', '', 4),
(21, 'CAPTION', 'textbox', '', 1),
(21, 'NAME', 'hiddenparam', 'rsfp_Product', 0),
(21, 'COMPONENTTYPE', 'hidden', '21', 0),
(21, 'DESCRIPTION', 'textarea', '', 2),
(21, 'SHOW', 'select', 'YES\r\nNO', 3),
(22, 'REQUIRED', 'select', 'NO\r\nYES', 6),
(22, 'ITEMS', 'textarea', '', 5),
(22, 'MULTIPLE', 'select', 'NO\r\nYES', 3),
(22, 'SIZE', 'textbox', '', 2),
(22, 'COMPONENTTYPE', 'hidden', '22', 9),
(22, 'CAPTION', 'textbox', '', 1),
(22, 'NAME', 'textbox', '', 0),
(22, 'ADDITIONALATTRIBUTES', 'textarea', '', 7),
(22, 'DESCRIPTION', 'textarea', '', 8),
(22, 'VALIDATIONMESSAGE', 'textarea', 'INVALIDINPUT', 9),
(22, 'VIEW_TYPE', 'select', 'DROPDOWN\r\nCHECKBOX\r\nRADIOGROUP', 4),
(22, 'FLOW', 'select', 'HORIZONTAL\r\nVERTICAL', 3),
(23, 'COMPONENTTYPE', 'hidden', '23', 2),
(23, 'CAPTION', 'textbox', '', 1),
(23, 'NAME', 'textbox', '', 0),
(27, 'NAME', 'textbox', '', 0),
(27, 'CAPTION', 'textbox', '', 1),
(27, 'FLOW', 'select', 'HORIZONTAL\r\nVERTICAL', 2),
(27, 'VIEW_TYPE', 'select', 'DROPDOWN\r\nRADIOGROUP', 3),
(27, 'ADDITIONALATTRIBUTES', 'textarea', '', 4),
(27, 'DESCRIPTION', 'textarea', '', 5),
(27, 'SHOW', 'select', 'YES\r\nNO', 6),
(27, 'COMPONENTTYPE', 'hidden', '27', 6),
(28, 'NAME', 'textbox', '', 1),
(28, 'CAPTION', 'textbox', '', 2),
(28, 'REQUIRED', 'select', 'NO\r\nYES', 3),
(28, 'SIZE', 'textbox', '20', 4),
(28, 'MAXSIZE', 'textbox', '', 5),
(28, 'VALIDATIONRULE', 'select', '//<code>\r\nreturn RSFormProHelper::getValidationRules();\r\n//</code>', 6),
(28, 'VALIDATIONMESSAGE', 'textarea', 'INVALIDINPUT', 7),
(28, 'ADDITIONALATTRIBUTES', 'textarea', '', 8),
(28, 'DEFAULTVALUE', 'textarea', '', 9),
(28, 'DESCRIPTION', 'textarea', '', 10),
(28, 'COMPONENTTYPE', 'hidden', '28', 11),
(28, 'VALIDATIONEXTRA', 'textbox', '', 12);

UPDATE #__rsform_component_type_fields SET `FieldValues`='//<code>\r\nreturn RSFormProHelper::getValidationRules();\r\n//</code>' WHERE `ComponentTypeId` = 28 AND `FieldName` = 'VALIDATIONRULE' AND `FieldValues` LIKE '%RSgetValidationRules%';

CREATE TABLE IF NOT EXISTS `#__rsform_payment` (
  `form_id` int(11) NOT NULL,
  `params` text NOT NULL,
  PRIMARY KEY (`form_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;