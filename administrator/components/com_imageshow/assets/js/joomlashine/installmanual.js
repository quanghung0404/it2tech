/**
* @author    JoomlaShine.com http://www.joomlashine.com
* @copyright Copyright (C) 2008 - 2011 JoomlaShine.com. All rights reserved.
* @license   GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
*/
var JSNInstallManual = new Class({
	options : {
		formPluginInstall: false,
		formImageshowCore: false,
		formUpdatePage: false
	},
	initialize: function(options)
	{
		this.options = Object.merge(this.options, options);
	},
	
	startManualInstall: function()
	{
		this._loadForm();
	},
	
	_loadForm : function()
	{
		var form = new Element('form', {
			'id' : 'jsn-install-manual-form',
			'class' : 'jsn-install-manual',
			'name' : 'installManualForm',
			'method' : 'post',
			'enctype' : 'multipart/form-data',
			'action' : this.options.actionForm
		});
		
		if (this.options.formPluginInstall == true) {
			html = this._formInstallJSNPlugin();
		} else if (this.options.formUpdatePage == true) {
			html = this._formUpdatePage();
		} else if (this.options.formImageshowCore == true){
			html = this._formInstallImageshowCore();
		}	
		
		form.innerHTML = form.innerHTML + html;
		
		$(this.options.parentId).appendChild(form);
	},
	
	_formInstallJSNPlugin: function()
	{
		html = '<div id="jsn-install-manual">';
		html += '<div id="jsn-install-manual-stage-1">';
		html += '<span style="color:red;">' + ((this.options.default_install) ? this.options.requireDefaultInstallText : this.options.requireInstallText )+ '</span> ';
		html += '<div id="jsn-install-manual-stage-2' + ((this.options.default_install) ? '-default' : '') + '">';
		
		if (!this.options.default_install) {
			html += '<ul><li> '+ this.options.installPluginText +'.</li></ul>';
		}
		
		html += '<div class="jsn-manual-installation"><ul><li>1. '+this.options.dowloadInstallationPackageText+': <a href="'+ this.options.downloadLink+ '" class="btn">'+ this.options.manualDownloadText + '</a></li>';
		html += '<li>2. '+this.options.selectDownloadPackageText+': <input type="file" size="45" name="file"/></li></ul>';
		html += '<input type="hidden" name="redirect_link" value="' + this.options.redirectLink + '" />';
		html += '<input type="hidden" name="identified_name" value="' + this.options.identify_name + '" />';
		html += '<div class="form-actions"><button class="btn" type="submit"/>'+ this.options.manualInstallButton +'</button></div>';
		html += '</div></div>';
		
		return html;
	},
	
	_formInstallImageshowCore: function()
	{
		html = '<div id="jsn-install-manual">';
		html += '<div class="jsn-manual-installation"><ul><li>1. '+this.options.dowloadInstallationPackageText+': <a href="'+ this.options.downloadLink+ '" class="btn">'+ this.options.manualDownloadText + '</a></li>';
		html += '<li>2. '+this.options.selectDownloadPackageText+': <input type="file" size="50" name="file"/></li></ul>';
		html += '<div><input type="hidden" name="redirect_link" value="' + this.options.redirectLink + '" /></div>';
		html += '<div class="form-actions"><button class="btn" type="submit"/>'+ this.options.manualInstallButton +'</button></div>';
		html += '</div>';
		
		return html;
	},
	
	_formUpdatePage: function()
	{
		html = '<div id="jsn-install-manual">';
		html += '<div id="jsn-install-manual-stage-1" class="jsn-manual-installation">';
		html += '<ul><li>1. '+this.options.dowloadInstallationPackageText+': <a href="'+ this.options.downloadLink+ '" class="btn">'+ this.options.manualDownloadText + '</a></li>';
		html += '<li>2. '+this.options.selectDownloadPackageText+': <input type="file" size="55" name="file"/></li></ul>';
		html += '<div><input type="hidden" name="redirect_link" value="' + this.options.redirectLink + '" /></div>';
		html += '<div class="form-actions"><button class="btn" type="submit"/>'+ this.options.manualInstallButton +'</button></div>';
		html += '</div></div>';
		
		return html;
	}
});