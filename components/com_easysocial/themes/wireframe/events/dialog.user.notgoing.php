<dialog>
    <width>400</width>
    <height>150</height>
    <selectors type="json">
    {
        "{closeButton}"     : "[data-close-button]",
        "{submitButton}"    : "[data-submit-button]"
    }
    </selectors>
    <title><?php echo JText::_('COM_EASYSOCIAL_EVENTS_DIALOG_NOT_GOING_TO_EVENT_TITLE'); ?></title>
    <content>
        <p><?php echo JText::_('COM_EASYSOCIAL_EVENTS_DIALOG_NOT_GOING_TO_EVENT_CONTENT');?></p>
    </content>
    <buttons>
        <button data-close-button type="button" class="btn btn-es btn-sm"><?php echo JText::_('COM_EASYSOCIAL_CLOSE_BUTTON'); ?></button>
        <button data-submit-button type="button" class="btn btn-es-primary btn-sm"><?php echo JText::_('COM_EASYSOCIAL_YES_BUTTON'); ?></button>
    </buttons>
</dialog>
