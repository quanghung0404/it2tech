<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="row form-horizontal">
	<div class="col-lg-6">
		<div class="panel">
			<div class="panel-head">
				<b><?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_MAP_FEATURES');?></b>

				<div class="panel-info">
					<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_MAP_FEATURES_INFO');?>
				</div>
			</div>

			<div class="panel-body">
				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_LANGUAGE_CODE'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_LANGUAGE_CODE'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_LANGUAGE_CODE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<div class="form-inline">
							<input type="text" name="main_locations_blog_language" class="form-control text-center" value="<?php echo $this->config->get('main_locations_blog_language' );?>" size="3" style="width: auto" />
							<a class="btn btn-default" href="https://developers.google.com/maps/faq#languagesupport" target="_blank"><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_WORKFLOW_LANGUAGE_CODE_REFERENCE');?></a>
						</div>
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_USE_STATIC_MAPS'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_USE_STATIC_MAPS'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_USE_STATIC_MAPS_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<?php echo $this->html('grid.boolean', 'main_locations_static_maps', $this->config->get('main_locations_static_maps'));?>
					</div>
				</div>
				
				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_BLOG_MAP_SIZE_HEIGHT'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_BLOG_MAP_SIZE_HEIGHT'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_BLOG_MAP_SIZE_HEIGHT_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<div class="form-inline">
							<div class="form-group">
								<div class="input-group">
									<input type="text" name="main_locations_blog_map_height" class="form-control text-center" value="<?php echo $this->config->get('main_locations_blog_map_height');?>" />
									<span class="input-group-addon"><?php echo JText::_( 'COM_EASYBLOG_PIXELS' );?></span>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_BLOG_MAP_TYPE'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_BLOG_MAP_TYPE'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_BLOG_MAP_TYPE_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<select name="main_locations_map_type" class="form-control">
							<option value="ROADMAP"<?php echo $this->config->get( 'main_locations_map_type' ) == 'ROADMAP' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_LOCATIONS_ROADMAP' ); ?></option>
							<option value="SATELLITE"<?php echo $this->config->get( 'main_locations_map_type' ) == 'SATELLITE' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_LOCATIONS_SATELLITE' ); ?></option>
							<option value="HYBRID"<?php echo $this->config->get( 'main_locations_map_type' ) == 'HYBRID' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_LOCATIONS_HYBRID' ); ?></option>
							<option value="TERRAIN"<?php echo $this->config->get( 'main_locations_map_type' ) == 'TERRAIN' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_LOCATIONS_TERRAIN' ); ?></option>
						</select>
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_DEFAULT_ZOOM_LEVEL'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_DEFAULT_ZOOM_LEVEL'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_DEFAULT_ZOOM_LEVEL_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<div class="row">
							<div class="col-sm-3">
								<input type="text" name="main_locations_default_zoom_level" class="form-control text-center" value="<?php echo $this->config->get('main_locations_default_zoom_level');?>" />
							</div>
						</div>
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_MAX_ZOOM_LEVEL'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_MAX_ZOOM_LEVEL'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_MAX_ZOOM_LEVEL_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<div class="row">
							<div class="col-sm-3">
								<input type="text" name="main_locations_max_zoom_level" class="form-control text-center" value="<?php echo $this->config->get('main_locations_max_zoom_level');?>" />
							</div>
						</div>
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_MIN_ZOOM_LEVEL'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_MIN_ZOOM_LEVEL'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_MIN_ZOOM_LEVEL_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<div class="row">
							<div class="col-sm-3">
								<input type="text" name="main_locations_min_zoom_level" class="form-control text-center" value="<?php echo $this->config->get('main_locations_min_zoom_level');?>" />
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-lg-6">
		<div class="panel">
			<div class="panel-head">
				<b><?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_TITLE');?></b>
				<div class="panel-info">
					<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_INFO');?>
				</div>
			</div>

			<div class="panel-body">
				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_ENABLE_LOCATION'); ?>

						<i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_ENABLE_LOCATION'); ?>"
							data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_WORKFLOW_LOCATIONS_ENABLE_LOCATION_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
					</label>

					<div class="col-md-7">
						<?php echo $this->html('grid.boolean', 'main_locations', $this->config->get('main_locations'));?>
					</div>
				</div>

				<div class="form-group">
		            <label for="page_title" class="col-md-5">
		                <?php echo JText::_('COM_EASYBLOG_SETTINGS_LOCATIONS_SERVICE_PROVIDER'); ?>

		                <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LOCATIONS_SERVICE_PROVIDER'); ?>"
		                    data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LOCATIONS_SERVICE_PROVIDER_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
		            </label>

		            <div class="col-md-7">
						<select name="location_service_provider" class="form-control" data-location-integration>
							<option value="maps"<?php echo $this->config->get('location_service_provider' ) == 'maps' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LOCATIONS_SERVICE_PROVIDER_GOOGLEMAPS' ); ?></option>
							<option value="places"<?php echo $this->config->get('location_service_provider' ) == 'places' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LOCATIONS_SERVICE_PROVIDER_GOOGLEPLACES' ); ?></option>]
							<option value="foursquare"<?php echo $this->config->get('location_service_provider' ) == 'foursquare' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_SETTINGS_LOCATIONS_SERVICE_PROVIDER_FOURSQUARE' ); ?></option>
						</select>
		            </div>
		        </div>
			</div>
		</div>

		<div class="panel<?php echo $this->config->get('location_service_provider') != 'foursquare' ? ' hide' : '';?>" data-panel-foursquare data-panel-integration>
			<div class="panel-head">
				<b><?php echo JText::_('COM_EASYBLOG_SETTINGS_LOCATIONS_SERVICE_PROVIDER_FOURSQUARE');?></b>
				<div class="panel-info"><?php echo JText::_('COM_EASYBLOG_SETTINGS_LOCATIONS_SERVICE_PROVIDER_FOURSQUARE_INFO');?></div>
				<br />
				<a href="https://developer.foursquare.com/" target="_blank" class="btn btn-primary btn-sm"><?php echo JText::_('COM_EASYBLOG_FOURSQUARE_CREATE_APP');?></a>
			</div>

			<div class="panel-body">
		        <div class="form-group">
		            <label for="page_title" class="col-md-5">
		                <?php echo JText::_('COM_EASYBLOG_SETTINGS_LOCATIONS_FOURSQUARE_CLIENT_ID'); ?>

		                <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LOCATIONS_FOURSQUARE_CLIENT_ID'); ?>"
		                    data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LOCATIONS_FOURSQUARE_CLIENT_ID_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
		            </label>

		            <div class="col-md-7">
						<input type="text" name="foursquare_client_id" class="form-control" value="<?php echo $this->config->get('foursquare_client_id');?>" size="60" />
		            </div>
		        </div>

		        <div class="form-group">
		            <label for="page_title" class="col-md-5">
		                <?php echo JText::_('COM_EASYBLOG_SETTINGS_LOCATIONS_FOURSQUARE_CLIENT_SECRET'); ?>

		                <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LOCATIONS_FOURSQUARE_CLIENT_SECRET'); ?>"
		                    data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LOCATIONS_FOURSQUARE_CLIENT_SECRET_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
		            </label>

		            <div class="col-md-7">
						<input type="text" name="foursquare_client_secret" class="form-control" value="<?php echo $this->config->get('foursquare_client_secret');?>" size="60" />
		            </div>
		        </div>
		    </div>
	    </div>

		<div class="panel<?php echo $this->config->get('location_service_provider') != 'maps' ? ' hide' : '';?>" data-panel-maps data-panel-integration>
			<div class="panel-head">
				<b><?php echo JText::_('COM_EASYBLOG_SETTINGS_LOCATIONS_SERVICE_PROVIDER_GOOGLEMAPS');?></b>
			</div>

			<div class="panel-body">
		        <div class="form-group">
		            <label for="page_title" class="col-md-5">
		                <?php echo JText::_('COM_EASYBLOG_SETTINGS_LOCATIONS_SERVICE_PROVIDER_API_KEY'); ?>

		                <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LOCATIONS_SERVICE_PROVIDER_API_KEY'); ?>"
		                    data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LOCATIONS_SERVICE_PROVIDER_API_KEY_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
		            </label>

		            <div class="col-md-7">
						<input type="text" name="googlemaps_api_key" class="form-control" value="<?php echo $this->config->get('googlemaps_api_key');?>" size="60" />
		            </div>
		        </div>
		    </div>
	    </div>

		<div class="panel<?php echo $this->config->get('location_service_provider') != 'places' ? ' hide' : '';?>" data-panel-places data-panel-integration>
			<div class="panel-head">
				<b><?php echo JText::_('COM_EASYBLOG_SETTINGS_LOCATIONS_SERVICE_PROVIDER_GOOGLEPLACES');?></b>
			</div>

			<div class="panel-body">
		        <div class="form-group">
		            <label for="page_title" class="col-md-5">
		                <?php echo JText::_('COM_EASYBLOG_SETTINGS_LOCATIONS_SERVICE_PROVIDER_API_KEY'); ?>

		                <i data-html="true" data-placement="top" data-title="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LOCATIONS_SERVICE_PROVIDER_API_KEY'); ?>"
		                    data-content="<?php echo JText::_('COM_EASYBLOG_SETTINGS_LOCATIONS_SERVICE_PROVIDER_API_KEY_DESC');?>" data-eb-provide="popover" class="fa fa-question-circle pull-right"></i>
		            </label>

		            <div class="col-md-7">
						<input type="text" name="googleplaces_api_key" class="form-control" value="<?php echo $this->config->get('googleplaces_api_key');?>" size="60" />
		            </div>
		        </div>
		    </div>
	    </div>
	</div>
</div>
