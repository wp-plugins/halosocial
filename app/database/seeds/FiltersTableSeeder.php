<?php
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category database seeder
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */

class FiltersTableSeeder extends Seeder {

    public function run()
    {
		$filters = $this->getFilterSettings();
		HALOFilter::insertNewFilters($filters);
    }

	/*
		return event filter settings array
	*/
	public function getFilterSettings() {
        $filters = array(
			//admin.filters.index
        	array(
            	'name'      	=> 'admin.filters.index',
            	'type' 	        => 'core', 
            	'description' 	=> 'Filter for Name column', 
            	'on_display_handler' 	=> 'HALOFilter::getColumnValues', 
            	'on_apply_handler' 		=> 'HALOFilter::filterColumnValues', 
            	'params' 		=> '{"table":"halo_filters","column":"name","title":"Name"}', 

                'published' => 1,				
				
                'created_at' => new DateTime,
                'updated_at' => new DateTime,
        	),
        	array(
            	'name'      	=> 'admin.filters.index',
            	'type' 	        => 'core', 
            	'description' 	=> 'Filter for Type column', 
            	'on_display_handler' 	=> 'HALOFilter::getColumnValues', 
            	'on_apply_handler' 		=> 'HALOFilter::filterColumnValues', 
            	'params' 		=> '{"table":"halo_filters","column":"type","title":"Type"}', 

                'published' => 1,

                'created_at' => new DateTime,
                'updated_at' => new DateTime,
        	),
			//admin.profiles.index
        	array(
            	'name'      	=> 'admin.profiles.index',
            	'type' 	        => 'core', 
            	'description' 	=> 'Filter for Type column', 
            	'on_display_handler' 	=> 'HALOFilter::getColumnValues', 
            	'on_apply_handler' 		=> 'HALOFilter::filterColumnValues', 
            	'params' 		=> '{"table":"halo_profiles","column":"type","title":"Type"}', 

                'published' => 1,

                'created_at' => new DateTime,
                'updated_at' => new DateTime,
        	),
        	array(
            	'name'      	=> 'admin.profiles.index',
            	'type' 	        => 'core', 
            	'description' 	=> 'Filter for Published column', 
            	'on_display_handler' 	=> 'HALOFilter::getYesNo', 
            	'on_apply_handler' 		=> 'HALOFilter::filterColumnValues', 
            	'params' 		=> '{"table":"halo_profiles","column":"published","title":"Published"}', 

                'published' => 1,

                'created_at' => new DateTime,
                'updated_at' => new DateTime,
        	),
			//admin.fields.index
        	array(
            	'name'      	=> 'admin.fields.index',
            	'type' 	        => 'core', 
            	'description' 	=> 'Filter for Type column', 
            	'on_display_handler' 	=> 'HALOFilter::getColumnValues', 
            	'on_apply_handler' 		=> 'HALOFilter::filterColumnValues', 
            	'params' 		=> '{"table":"halo_fields","column":"type","title":"Type"}', 

                'published' => 1,

                'created_at' => new DateTime,
                'updated_at' => new DateTime,
        	),

			//admin.categories.index
        	array(
            	'name'      	=> 'admin.categories.index',
            	'type' 	        => 'core', 
            	'description' 	=> 'Filter for Published column', 
            	'on_display_handler' 	=> 'HALOFilter::getYesNo', 
            	'on_apply_handler' 		=> 'HALOFilter::filterColumnValues', 
            	'params' 		=> '{"table":"halo_common_categories","column":"published","title":"Published"}', 

                'published' => 1,

                'created_at' => new DateTime,
                'updated_at' => new DateTime,
        	),
        	array(
            	'name'      	=> 'admin.categories.index',
            	'type' 	        => 'core', 
            	'description' 	=> 'Filter for Parent column', 
            	'on_display_handler' 	=> 'HALOFilter::getNonLeafCategories', 
            	'on_apply_handler' 		=> 'HALOFilter::filterColumnValues', 
            	'params' 		=> '{"table":"halo_common_categories","column":"parent_id","title":"Parent"}', 

                'published' => 1,

                'created_at' => new DateTime,
                'updated_at' => new DateTime,
        	),
			//admin.categories.index
        	array(
            	'name'      	=> 'admin.categories.index',
            	'type' 	        => 'core', 
            	'description' 	=> 'Filter to search category by name',
            	'on_display_handler' 	=> 'HALOFilter::displayColumnSearch', 
            	'on_apply_handler' 		=> 'HALOFilter::applyColumnSearch', 
            	'params' 		=> '{"table":"halo_common_categories","column":"name","title":"Search"}',

                'published' => 1,

                'created_at' => new DateTime,
                'updated_at' => new DateTime,
        	),

			//activity.profile.user
        	array(
            	'name'      	=> 'activity.profile.user',
            	'type' 	        => 'core', 
            	'description' 	=> 'Filter for user activities on profile stream', 
            	'on_display_handler' 	=> '', 
            	'on_apply_handler' 		=> 'HALOProfileModel::userStream', 
            	'params' 		=> '', 

                'published' => 1,

                'created_at' => new DateTime,
                'updated_at' => new DateTime,
        	),
			
			//activity.profile.byuser
        	array(
            	'name'      	=> 'activity.profile.byuser',
            	'type' 	        => 'core', 
            	'description' 	=> "Filter for user's activities on profile stream", 
            	'on_display_handler' 	=> 'HALOPrivacy::getUserActivities', 
            	'on_apply_handler' 		=> 'HALOPrivacy::getUserActivities', 
            	'params' 		=> '{"title":"By User","uiType":"form.filter_tree_radio"}',

                'published' => 1,

                'created_at' => new DateTime,
                'updated_at' => new DateTime,
        	),
						
			//activity.home.byuser
        	array(
            	'name'      	=> 'activity.home.byuser',
            	'type' 	        => 'core', 
            	'description' 	=> "Filter for user's activities on homepage stream", 
            	'on_display_handler' 	=> 'HALOPrivacy::getUserActivities', 
            	'on_apply_handler' 		=> 'HALOPrivacy::getUserActivities', 
            	'params' 		=> '{"title":"By User","uiType":"form.filter_tree_radio"}',

                'published' => 1,

                'created_at' => new DateTime,
                'updated_at' => new DateTime,
        	),
			
			//activity.home.string
        	array(
            	'name'      	=> 'activity.home.string',
            	'type' 	        => 'core', 
            	'description' 	=> "Filter to search activity with string text", 
            	'on_display_handler' 	=> '', 
            	'on_apply_handler' 		=> 'HALOActivityModel::applyTextSearch', 
            	'params' 		=> '{"title":"Search","uiType":"filter.text"}',

                'published' => 1,

                'created_at' => new DateTime,
                'updated_at' => new DateTime,
        	),
			
			//activity.home.hashtag
        	array(
            	'name'      	=> 'activity.home.hashtag',
            	'type' 	        => 'core', 
            	'description' 	=> "Filter to search activity by hashtag", 
            	'on_display_handler' 	=> '', 
            	'on_apply_handler' 		=> 'HALOActivityModel::getByHashTag', 
            	'params' 		=> '{"title":"Tags","uiType":"filter.text"}',

                'published' => 1,

                'created_at' => new DateTime,
                'updated_at' => new DateTime,
        	),
			
			//activity.privacy.index
        	array(
            	'name'      	=> 'activity.privacy.index',
            	'type' 	        => 'core', 
            	'description' 	=> 'Filter to enforcement privacy setting of activities',
            	'on_display_handler' 	=> '', 
            	'on_apply_handler' 		=> 'HALOPrivacy::privacyStream', 
            	'params' 		=> '', 

                'published' => 1,

                'created_at' => new DateTime,
                'updated_at' => new DateTime,
        	),
			
			//activity.single.index
        	array(
            	'name'      	=> 'activity.single.index',
            	'type' 	        => 'core', 
            	'description' 	=> 'Filter to get update of single activity view only',
            	'on_display_handler' 	=> '', 
            	'on_apply_handler' 		=> 'HALOActivityModel::singleActivity', 
            	'params' 		=> '', 

                'published' => 1,

                'created_at' => new DateTime,
                'updated_at' => new DateTime,
        	),
			
			//user.listing.search
        	array(
            	'name'      	=> 'user.listing.search',
            	'type' 	        => 'core', 
            	'description' 	=> 'Filter for searching user',
            	'on_display_handler' 	=> 'HALOUserModel::displayNameSearch', 
            	'on_apply_handler' 		=> 'HALOUserModel::applyNameSearch', 
            	'params' 		=> '{"table":"halo_users","column":"name","title":"Search","metaCb":"HALOFilter::getTextSearch"}',

                'published' => 1,

                'created_at' => new DateTime,
                'updated_at' => new DateTime,
        	),
			
			//user.listing.sort
        	array(
            	'name'      	=> 'user.listing.sort',
            	'type' 	        => 'core', 
            	'description' 	=> 'Filter for User list ordering',
            	'on_display_handler' 	=> 'HALOUserModel::displaySortBy', 
            	'on_apply_handler' 		=> 'HALOUserModel::applySortBy', 
            	'params' 		=> '{"title":"Sort"}',

                'published' => 1,

                'created_at' => new DateTime,
                'updated_at' => new DateTime,
        	),
			
			//user.listing.type
        	array(
            	'name'      	=> 'user.listing.type',
            	'type' 	        => 'core', 
            	'description' 	=> 'Filter user by type',
            	'on_display_handler' 	=> 'HALOUserModel::getUserType', 
            	'on_apply_handler' 		=> 'HALOUserModel::getUserType', 
            	'params' 		=> '{"title":"By Type"}',

                'published' => 1,

                'created_at' => new DateTime,
                'updated_at' => new DateTime,
        	),
			
		);
		return $filters;
	}
}
