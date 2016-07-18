INSERT IGNORE INTO `#__rsform_component_types` (`ComponentTypeId`, `ComponentTypeName`) VALUES (499, 'offlinePayment');

DELETE FROM #__rsform_component_type_fields WHERE ComponentTypeId = 499;
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES
(499, 'NAME', 'textbox', '', 0),
(499, 'LABEL', 'textbox', '', 1),
(499, 'WIRE', 'textarea', '', 2),
(499, 'COMPONENTTYPE', 'hidden', '499', 6),
(499, 'LAYOUTHIDDEN', 'hiddenparam', 'YES', 7);