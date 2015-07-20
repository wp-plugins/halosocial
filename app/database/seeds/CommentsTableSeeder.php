<?php
/**
 * @author Hung Tran <hungtran@halo.social>
 * @category database seeder
 * @package HaloSocial
 * @copyright 2015 HaloSocial
 */

class CommentsTableSeeder extends Seeder {

    public function run()
    {


        $root = HALOCommentModel::create(['message' => 'Root node','actor_id'=>1,'commentable_id'=>0,'commentable_type'=>'system','published' => 1]);

    }

}
