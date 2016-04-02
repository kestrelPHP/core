<?php
/**
 *
 */
    global $user;
    if($user->language == 'en') $lang = "en_us"; else $lang = $user->language;
?>
<h5><?php print t("Website Development", array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang)); ?></h5>
<div class="left-side">
    <div class="content-lang">
        <div>
            <strong><?php print t("Content Language", array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang));?></strong>
            <span class="has-tip tip-right icon icon-info2"
                data-options="disable_for_touch:true"
                tooltip-help="left_languages"></span>
        </div>

        <div class="clearfix">
            <select class="left" id="select_language"
                change-language="sidebar.current_lang.language"
                ng-model="sidebar.current_lang.language"
                ng-options="obj.language as obj.name for obj in sidebar.hotel_lang_list">
            </select>
        </div>
    </div>
    <nav class="left-nav">
        <ul class="side-nav">
            <li ng-repeat="item in sidebar.menu_item" class="{{(item.active == true) && 'active' || ''}}">
                <a href="{{item.ng_link}}">
                    <span class="small-10 columns">{{item.title}}</span>
                    <span ng-if="item.has_data == true"
                        class="icon-checkmark color-green right"></span>
                    <span ng-if="item.require == true && item.has_data == false"
                        class="icon-close color-red right"></span>
                    <span ng-if="item.require == false && item.has_data == false"
                        class="icon-record color-orange right"></span>
                </a>
            </li>
        </ul>
    </nav>

    <div class="website-dev">
        <div>
            <strong><?php print t('Website Development', array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang)); ?></strong>
            <span class="has-tip tip-right icon icon-info2"
                data-options="disable_for_touch:true"
                tooltip-help="website_development_inf"></span>
        </div>
        <div>
            <label ng-if="!sidebar.page_status">
                <?php print t('Missing basic info', array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang)); ?>
                <span class="icon-close color-red right"></span>
            </label>
            <label ng-if="sidebar.page_status == 1">
                <?php print t('Basic info completed', array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang)); ?>
                <span class="icon-record color-orange right"></span>
            </label>
            <label ng-if="sidebar.page_status == 2">
                <?php print t('Advanced info completed', array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang)); ?>
                <span class="icon-checkmark color-green right"></span>
            </label>
        </div>
    </div>

    <div class="website-status">
        <div><strong><?php print t("Website Status", array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang));?></strong></div>
        <div>
            <div ng-if="!sidebar.website_status || sidebar.website_status == 0" class="item-inline_block">
                <?php print t("Staging", array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang)); ?>
                <!-- <a href="#" class="ng-preview-website"><?php //print t("preview", array(), array('context'=>'hls;system:1;module:7;section:3', 'langcode'=>$lang));?></a> -->
                <span class="icon icon-preview-website icon-gray ng-preview-website clickable mrgl5"></span>
                <span ng-click="openPreviewPopup()" class="icon icon-mobile icon-gray clickable va-mid mrgl4"></span>
            </div>
            <div ng-if="sidebar.website_status == 1" class="item-inline_block">
                <?php print t("Live", array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang)); ?>
                <!-- <a href="#" class="ng-preview-website"><?php //print t("preview", array(), array('context'=>'hls;system:1;module:7;section:3', 'langcode'=>$lang));?></a> -->
                <!-- <span class="icon-external-link"></span> -->
                <span class="icon icon-preview-website icon-gray ng-preview-website clickable mrgl2 mrgl5"></span>
                <span ng-click="openPreviewPopup()" class="icon icon-mobile icon-gray clickable va-mid mrgl4"></span>
            </div>

        </div>
    </div>
</div>