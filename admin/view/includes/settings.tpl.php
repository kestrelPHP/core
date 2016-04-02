<?php
/**
 *
 */
?>

<div class="clearfix" id="website">
    <?php include_once 'left-sidebar.php'; global $user; if($user->language == 'en') $lang = "en_us"; else $lang = $user->language; ?>
    <div class="content-side">
        <input type="hidden" id="meta-title" value="<?php print t("Website", array(), array('langcode'=>$lang))." | ". t("Website Development", array(), array('langcode'=>$lang))." - ". t("Settings", array(), array('langcode'=>$lang));?>">
        <h6 class="mrgt15"><?php print t("Settings", array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang));?></h6>
        <form action="{{settings.data.settings_form['#action']}}" method="post"
            name="settings_form" id="{{settings.data.settings_form.form_id['#id']}}"
            novalidate>
            <div class="row">
                <div class="columns large-6">
                    <label><?php print t("Accommodation Name", array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang));?></label>
                    <div class="row">
                        <div class="columns large-7">
                            <input type="text" id="uxAccomName" name="hotel_name" placeholder=""
                              ng-model="hotel_sys.hotel_name_obj.page_title"
                              ng-init="hotel_sys.hotel_name_obj.page_title = hotel_sys.hotel_name_obj.page_title
                                ? hotel_sys.hotel_name_obj.page_title : hotel_sys.hotel_name"
                              ng-class="{'alert-border': ngErrorRequired('hotel_name')}"
						      required />

    						<label for="uxAccomName" class="error"
    						  ng-show="ngErrorRequired('hotel_name')">
    						  <?php print t('Missing details', array(), array('context'=>'hls;system:1;module:1', 'langcode'=>$lang)); ?>
    						</label>
                        </div>
                    </div>

                    <label>
                        <?php print t("Template", array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang));?>
                        <span class="has-tip tip-right icon icon-info2" data-options="disable_for_touch:true"
                            tooltip-help="settings_template"></span>
                    </label>
                    <div class="row">
                        <div class="columns large-7">
                            <input type="hidden" name="hotel_sys[template_id]" value="{{hotel_sys.template_id}}" />
                            <input type="hidden" name="template_name" value="{{template_names[hotel_sys.template_id]}}" />

                            <select ng-model="hotel_sys.template_id"
                                ng-change="changeTemplate()">
                                <option ng-repeat="(id, obj) in template_list"
                                    value="{{obj.template_id}}"
                                    ng-selected="hotel_sys.template_id == obj.template_id">{{obj.display_name}}</option>
                            </select>
                        </div>

                        <div class="left">
                            <label class="inline">
                                    <span class="has-tip icon-dark-gray icon-list-alt"
                                        data-options="disable_for_touch:true"
                                        ng-if="show_template_preview"
                                        tooltip-help="{{'preview_template_' + hotel_sys.template_id}}"></span>
                            </label>
                        </div>
                    </div>

                    <label>
                        <?php print t("Logo", array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang));?>
                        <span class="has-tip tip-right icon icon-info2" data-options="disable_for_touch:true"
                            tooltip-help="settings_logo"></span>
                    </label>
                    <div class="row">
                        <div class="columns large-7">
                            <div class="row collapse">
                                <div class="columns large-8">
                                    <input type="file" id="uxLogo" fileupload="fileupload_options"
                                        data-type="logo" />

                                    <input name="hotel_inf[image_items][logo]" type="text"
                                        value="{{hotel_inf.image_items.logo}}"
                                        placeholder="<?php print t('No file selected', array(), array('context'=>'hls;system:1;module:1', 'langcode'=>$lang)); ?>" id="uxLogoText"
                                        class="error-nobdrr" value=""
                                        ng-model="hotel_inf.image_items.logo"
                                        ng-class="{'alert-border': ngErrorRequired('hotel_inf[image_items][logo]')}"
                                        required />

                                    <a href="#" id="uxLogoDelete" class="input-text-delete" title="<?php print t('Delete', array(), array('context'=>'hls;system:1;module:1', 'langcode'=>$lang)); ?>">X</a>
                                </div>

                                <div class="columns large-4">
                                    <a href="#" class="button postfix error-nobdrl" id="uxLogoButton"
                                        ng-class="{'alert-border': ngErrorRequired('hotel_inf[image_items][logo]')}">
                                        <?php print t("Choose File", array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang));?>
                                    </a>
                                </div>

        						<label for="uxLogoText" class="error"
        						  ng-show="ngErrorRequired('hotel_inf[image_items][logo]')">
        						  <?php print t('Missing details', array(), array('context'=>'hls;system:1;module:1', 'langcode'=>$lang)); ?>
        						</label>

        						<label for="uxLogoText" id="uxLogoErrorMessage" style="display: none;"
        						  class="error"></label>

                                <label id="uxLogoMessage" style="display: none;"
                                    class="upload-status"
                                    data-loading="<?php print t('Uploading...', array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang)); ?>"
                                    data-deleting="<?php print t('Deleting...', array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang)); ?>"
                                    data-error="<?php print t('Upload error', array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang)); ?>"></label>
                            </div>
                        </div>
                        <div class="left">
                            <label class="inline">
                                <small>{{template_settings.logo.width}}x{{template_settings.logo.height}} px
                                    | {{template_settings.logo.size}} kb</small>
                            </label>

                        </div>
                    </div>
                    <!-- -->

                    <label>
                        <?php print t("Logo for Mobile", array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang));?>
                        <span class="has-tip tip-right icon icon-info2" data-options="disable_for_touch:true"
                            tooltip-help="settings_logo_mobile"></span>
                    </label>
                    <div class="row">
                        <div class="columns large-7">
                            <div class="row collapse">
                                <div class="columns large-8">
                                    <input type="file" id="uxLogoMobile" fileupload="fileupload_options"
                                        data-type="mobile_logo" />

                                    <input name="hotel_inf[image_items][mobile_logo]" type="text"
                                      placeholder="<?php print t('No file selected', array(), array('context'=>'hls;system:1;module:1', 'langcode'=>$lang)); ?>" id="uxLogoMobileText"
                                      class="error-nobdrr" value=""
                                      ng-model="hotel_inf.image_items.mobile_logo"
                                      ng-class="{'alert-border': ngErrorRequired('hotel_inf[image_items][mobile_logo]')}"
        						      required />

                                    <a href="#" id="uxLogoMobileDelete" class="input-text-delete" title="<?php print t('Delete', array(), array('context'=>'hls;system:1;module:1', 'langcode'=>$lang)); ?>">X</a>
                                </div>

                                <div class="columns large-4">
                                    <a href="#" class="button postfix error-nobdrl" id="uxLogoMobileButton"
                                      ng-class="{'alert-border': ngErrorRequired('hotel_inf[image_items][mobile_logo]')}">
                                        <?php print t("Choose File", array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang));?>
                                    </a>
                                </div>

        						<label for="uxLogoMobileText" class="error"
        						  data-empty="<?php print t('Missing details', array(), array('context'=>'hls;system:1;module:1', 'langcode'=>$lang)); ?>"
        						  ng-show="ngErrorRequired('hotel_inf[image_items][mobile_logo]')">
        						  <?php print t('Missing details', array(), array('context'=>'hls;system:1;module:1', 'langcode'=>$lang)); ?>
        						</label>

        						<label for="uxLogoMobileText" id="uxLogoMobileErrorMessage" style="display: none;"
        						  class="error"></label>

                                <label id="uxLogoMobileMessage" style="display: none;"
                                    class="upload-status"
                                    data-loading="<?php print t('Uploading...', array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang)); ?>"
                                    data-deleting="<?php print t('Deleting...'); ?>"
                                    data-error="<?php print t('Upload error'); ?>"></label>
                            </div>
                        </div>
                        <div class="left">
                            <label class="inline">
                                <small>{{template_settings.mobile_logo.width}}x{{template_settings.mobile_logo.height}} px
                                    | {{template_settings.mobile_logo.size}} kb</small>
                            </label>
                        </div>
                    </div>
                    <!-- end -->

                    <!--star -->
                    <label ng-if="slideshow_type == 1">
                        <?php print t("Background", array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang));?> 1
                        <span class="has-tip tip-right icon icon-info2" data-options="disable_for_touch:true"
                            tooltip-help="settings_banner_1"></span>
                    </label>
                    <label ng-if="slideshow_type == 0">
                        <?php print t("Banner", array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang));?> 1
                        <span class="has-tip tip-right icon icon-info2" data-options="disable_for_touch:true"
                            tooltip-help="settings_banner_1"></span>
                    </label>
                    <div class="row">
                        <div class="columns large-7">
                            <div class="row collapse">
                                <div class="columns large-8">
                                    <input type="file" id="uxBanner1" fileupload="fileupload_options"
                                        data-type="banner" />

                                    <input name="hotel_inf[image_items][banner][]" type="text"
                                        value="{{hotel_inf.image_items.banner[0]}}"
                                        placeholder="<?php print t('No file selected', array(), array('context'=>'hls;system:1;module:1', 'langcode'=>$lang)); ?>" id="uxBanner1Text"
                                        class="error-nobdrr"
                                        ng-class="{'alert-border': ngBannerRequired()}" />

                                    <a href="#" id="uxBanner1Delete" class="input-text-delete" title="<?php print t('Delete', array(), array('context'=>'hls;system:1;module:1', 'langcode'=>$lang)); ?>">X</a>
                                </div>

                                <div class="columns large-4">
                                    <a href="#" class="button postfix error-nobdrl" id="uxBanner1Button"
                                        ng-class="{'alert-border': ngBannerRequired()}">
                                        <?php print t("Choose File", array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang));?>
                                    </a>
                                </div>

        						<label for="uxBanner1Text" class="error"
        						  ng-show="ngBannerRequired()">
        						  <?php print t('Missing details', array(), array('context'=>'hls;system:1;module:1', 'langcode'=>$lang)); ?>
        						</label>

        						<label for="uxBanner1Text" id="uxBanner1ErrorMessage" style="display: none;"
        						  class="error"></label>

                                <label id="uxBanner1Message" style="display: none;"
                                    class="upload-status"
                                    data-loading="<?php print t('Uploading...', array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang)); ?>"
                                    data-deleting="<?php print t('Deleting...', array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang)); ?>"
                                    data-error="<?php print t('Upload error', array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang)); ?>"></label>
                            </div>
                        </div>
                        <div class="left">
                            <label class="inline">
                                <small>{{template_settings.banner.width}}x{{template_settings.banner.height}} px
                                    | {{template_settings.banner.size}} kb</small>
                            </label>
                        </div>
                    </div>
                    <!-- end -->

                    <!--star -->
                    <label ng-if="slideshow_type == 1">
                        <?php print t("Background", array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang));?> 2
                        <small>(<?php print t("optional", array(), array('context'=>'hls;system:1;module:1', 'langcode'=>$lang));?>)</small>
                    </label>
                    <label ng-if="slideshow_type == 0">
                        <?php print t("Banner", array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang));?> 2
                        <small>(<?php print t("optional", array(), array('context'=>'hls;system:1;module:1', 'langcode'=>$lang));?>)</small>
                    </label>
                    <div class="row">
                        <div class="columns large-7">
                            <div class="row collapse">
                                <div class="columns large-8">
                                    <input type="file" id="uxBanner2" fileupload="fileupload_options"
                                        data-type="banner" />

                                    <input name="hotel_inf[image_items][banner][]" type="text"
                                        value="{{hotel_inf.image_items.banner[1]}}"
                                        class="error-nobdrr"
                                        placeholder="<?php print t('No file selected', array(), array('context'=>'hls;system:1;module:1', 'langcode'=>$lang)); ?>" id="uxBanner2Text" />

                                    <a href="#" id="uxBanner2Delete" class="input-text-delete" title="<?php print t('Delete', array(), array('context'=>'hls;system:1;module:1', 'langcode'=>$lang)); ?>">X</a>
                                </div>

                                <div class="columns large-4">
                                    <a href="#" class="button postfix error-nobdrl" id="uxBanner2Button">
                                        <?php print t("Choose File", array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang));?>
                                    </a>
                                </div>

        						<label for="uxBanner2Text" id="uxBanner2ErrorMessage" style="display: none;"
        						  class="error"></label>

                                <label for="uxBanner2Text" id="uxBanner2Message" style="display: none;"
                                    class="upload-status"
                                    data-loading="<?php print t('Uploading...', array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang)); ?>"
                                    data-deleting="<?php print t('Deleting...', array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang)); ?>"
                                    data-error="<?php print t('Upload error', array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang)); ?>"></label>
                            </div>
                        </div>
                        <div class="left">
                            <label class="inline">
                                <small>{{template_settings.banner.width}}x{{template_settings.banner.height}} px
                                    | {{template_settings.banner.size}} kb</small>
                            </label>
                        </div>
                    </div>
                    <!-- end -->

                     <!--star -->
                    <label ng-if="slideshow_type == 1">
                        <?php print t("Background", array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang));?> 3
                        <small>(<?php print t("optional", array(), array('context'=>'hls;system:1;module:1', 'langcode'=>$lang));?>)</small>
                    </label>
                    <label ng-if="slideshow_type == 0">
                        <?php print t("Banner", array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang));?> 3
                        <small>(<?php print t("optional", array(), array('context'=>'hls;system:1;module:1', 'langcode'=>$lang));?>)</small>
                    </label>
                    <div class="row">
                        <div class="columns large-7">
                            <div class="row collapse">
                                <div class="columns large-8">
                                    <input type="file" id="uxBanner3" fileupload="fileupload_options"
                                        data-type="banner" />

                                    <input name="hotel_inf[image_items][banner][]" type="text"
                                        value="{{hotel_inf.image_items.banner[2]}}"
                                        class="error-nobdrr"
                                        placeholder="<?php print t('No file selected', array(), array('context'=>'hls;system:1;module:1', 'langcode'=>$lang)); ?>" id="uxBanner3Text" />

                                    <a href="#" id="uxBanner3Delete" class="input-text-delete" title="<?php print t('Delete', array(), array('context'=>'hls;system:1;module:1', 'langcode'=>$lang)); ?>">X</a>
                                </div>

                                <div class="columns large-4">
                                    <a href="#" class="button postfix error-nobdrl" id="uxBanner3Button">
                                        <?php print t("Choose File", array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang));?>
                                    </a>
                                </div>

        						<label for="uxBanner3Text" id="uxBanner3ErrorMessage" style="display: none;"
        						  class="error"></label>

                                <label for="uxBanner3Text" id="uxBanner3Message" style="display: none;"
                                    class="upload-status"
                                    data-loading="<?php print t('Uploading...', array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang)); ?>"
                                    data-deleting="<?php print t('Deleting...', array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang)); ?>"
                                    data-error="<?php print t('Upload error', array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang)); ?>"></label>
                            </div>
                        </div>
                        <div class="left">
                            <label class="inline">
                                <small>{{template_settings.banner.width}}x{{template_settings.banner.height}} px
                                    | {{template_settings.banner.size}} kb</small>
                            </label>
                        </div>
                    </div>
                    <!-- end -->

                    <!--star -->
                    <label ng-if="slideshow_type == 1">
                        <?php print t("Background", array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang));?> 4
                        <small>(<?php print t("optional", array(), array('context'=>'hls;system:1;module:1', 'langcode'=>$lang));?>)</small>
                    </label>
                    <label ng-if="slideshow_type == 0">
                        <?php print t("Banner", array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang));?> 4
                        <small>(<?php print t("optional", array(), array('context'=>'hls;system:1;module:1', 'langcode'=>$lang));?>)</small>
                    </label>
                    <div class="row">
                        <div class="columns large-7">
                            <div class="row collapse">
                                <div class="columns large-8">
                                    <input type="file" id="uxBanner4" fileupload="fileupload_options"
                                        data-type="banner" />

                                    <input name="hotel_inf[image_items][banner][]" type="text"
                                        value="{{hotel_inf.image_items.banner[3]}}"
                                        class="error-nobdrr"
                                        placeholder="<?php print t('No file selected', array(), array('context'=>'hls;system:1;module:1', 'langcode'=>$lang)); ?>" id="uxBanner4Text" />

                                    <a href="#" id="uxBanner4Delete" class="input-text-delete" title="<?php print t('Delete', array(), array('context'=>'hls;system:1;module:1', 'langcode'=>$lang)); ?>">X</a>
                                </div>

                                <div class="columns large-4">
                                    <a href="#" class="button postfix error-nobdrl" id="uxBanner4Button">
                                        <?php print t("Choose File", array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang));?>
                                    </a>
                                </div>

        						<label for="uxBanner4Text" id="uxBanner4ErrorMessage" style="display: none;"
        						  class="error"></label>

                                <label for="uxBanner4Text" id="uxBanner4Message" style="display: none;"
                                    class="upload-status"
                                    data-loading="<?php print t('Uploading...', array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang)); ?>"
                                    data-deleting="<?php print t('Deleting...', array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang)); ?>"
                                    data-error="<?php print t('Upload error', array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang)); ?>"></label>
                            </div>
                        </div>
                        <div class="left">
                            <label class="inline">
                                <small>{{template_settings.banner.width}}x{{template_settings.banner.height}} px
                                    | {{template_settings.banner.size}} kb</small>
                            </label>
                        </div>
                    </div>
                    <!-- end -->

                    <!--star -->
                    <label>
                        <?php print t("Favicon", array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang));?>
                        <small>(<?php print t("optional", array(), array('context'=>'hls;system:1;module:1', 'langcode'=>$lang));?>)</small>
                        <span class="has-tip tip-right icon icon-info2" data-options="disable_for_touch:true"
                            tooltip-help="settings_favicon"></span>
                    </label>
                    <div class="row">
                        <div class="columns large-7">
                            <div class="row collapse">
                                <div class="columns large-8">
                                    <input type="file" id="uxFavicon" fileupload="fileupload_options"
                                        data-type="favicon" />

                                    <input name="hotel_inf[image_items][favicon]" type="text"
                                        value="{{hotel_inf.image_items.favicon}}"
                                        class="error-nobdrr"
                                        placeholder="<?php print t('No file selected', array(), array('context'=>'hls;system:1;module:1', 'langcode'=>$lang)); ?>" id="uxFaviconText" />

                                    <a href="#" id="uxFaviconDelete" class="input-text-delete" title="<?php print t('Delete', array(), array('context'=>'hls;system:1;module:1', 'langcode'=>$lang)); ?>">X</a>
                                </div>

                                <div class="columns large-4">
                                    <a href="#" class="button postfix error-nobdrl" id="uxFaviconButton">
                                        <?php print t("Choose File", array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang));?>
                                    </a>
                                </div>

        						<label for="uxFaviconText" id="uxFaviconErrorMessage"
        						  class="error"></label>

                                <label id="uxFaviconMessage" style="display: none;"
                                    class="upload-status"
                                    data-loading="<?php print t('Uploading...', array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang)); ?>"
                                    data-deleting="<?php print t('Deleting...', array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang)); ?>"
                                    data-error="<?php print t('Upload error', array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang)); ?>"></label>
                            </div>
                        </div>
                        <!--<div class="left">
                            <label class="inline">
                                <small>16x16 px</small>
                            </label>
                        </div>-->
                    </div>
                    <!-- end -->

                    <!-- <label>Website Content Language <span title="Website Content Language"
                        class="has-tip tip-right icon icon-info2" data-options="disable_for_touch:true"
                        data-tooltip=""></span></label>
                    <div class="row">
                        <div class="columns large-12" ng-repeat="item in lang_list">
                            <input ng-if="item.active == true" type="checkbox" checked="checked"
                                value="{{item.lang_code}}" name="hotel_sys[hotel_lang][]"
                                id="lang-{{item.lang_code}}" />

                            <input ng-if="!item.active" type="checkbox" value="{{item.lang_code}}"
                                name="hotel_sys[hotel_lang][]" id="lang-{{item.lang_code}}" />

                            <label for="lang-{{item.lang_code}}">{{item.lang_name_en}}</label>
                        </div>
                    </div>
                    <p>
                        <small>Need more languages?</small>
                        <span title="Need more Language" class="has-tip tip-right icon icon-info2"
                            data-options="disable_for_touch:true" data-tooltip=""></span>
                    </p> -->

                    <div class="row">
                    	<div class="large-12 columns">
                          <label><?php print t("Website Footer", array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang));?>
                              <span class="has-tip tip-right icon icon-info2" data-options="disable_for_touch:true"
                                tooltip-help="settings_footer"></span>
                          </label>

                          <div class="tinymce-border" ng-class="{'alert-border': ngErrorRequired('footer_text')}">
                              <textarea placeholder="" id="uxWebsiteFooter"
                                name="footer_text" tinymce="tiny_options"
                                ng-model="hotel_sys.hotel_page_footer_obj.page_desc"
                                required></textarea>
                          </div>

    					   <label for="uxWebsiteFooter" class="error"
    						  ng-show="ngErrorRequired('footer_text')">
    						  <?php print t('Missing details', array(), array('context'=>'hls;system:1;module:1', 'langcode'=>$lang)); ?>
    						</label>
                        </div>
                    </div>

                    <label>
                        <?php print t("Hotel Link Solutions Footer Link", array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang));?>
                        <span class="has-tip tip-right icon icon-info2" data-options="disable_for_touch:true" tooltip-help="settings_hls_footer_link"></span>
                    </label>

                    <div class="clearfix mrgt-5">
                        <input type="radio" name="hotel_inf[show_footer_link]" value="1"
                            id="uxShowHotelLink" ng-model="hotel_inf.show_footer_link" class="rd-ck-inline left"/>
                        <label for="uxShowHotelLink" class="font11 inline left mrgr10"><?php print t("Yes", array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang));?></label>

                        <input type="radio" name="hotel_inf[show_footer_link]" value="0"
                            id="uxHideHotelLink" ng-model="hotel_inf.show_footer_link" class="rd-ck-inline left"/>
                        <label for="uxHideHotelLink" class="font11 inline left mrgr10"><?php print t("No", array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang));?></label>
                    </div>
                </div>

                <div class="columns large-6">

                <!-- choose template color -->
                    <!-- <label ng-hide="hotel_inf.manual_custom_css">
                        <?php //print t('Preset Colour Palette'); ?>
                    </label>

                    <div class="row" ng-hide="hotel_inf.manual_custom_css">
                        <div class="columns large-7">
                            <input type="hidden" name="hotel_inf[template_color]"
                                value="{{hotel_inf.template_color}}" />

                            <select ng-model="hotel_inf.template_color"
                                ng-options="id as name for (id, name) in template_settings.template_color"
                                ng-change="changeTemplate()">
                            </select>
                        </div>

                        <div class="left">
                            <label class="inline">
                                <span title="Preset Colour Palette" class="has-tip tip-right icon icon-info2"
                                    data-options="disable_for_touch:true" data-tooltip=""></span>
                            </label>
                        </div>
                    </div> -->
                <!-- end choose template color -->
                    <label ng-hide="hotel_inf.manual_custom_css">
                        <?php print t('Colour Scheme', array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang)); ?>
                        <span title="Preset Colour Palette" class="has-tip tip-right icon icon-info2"
                            data-options="disable_for_touch:true"
                            tooltip-help="template_color_info"></span>
                    </label>
                    <div class="row" ng-hide="hotel_inf.manual_custom_css">
                        <div class="columns large-7">
                            <input type="hidden" name="hotel_inf[template_color]"
                                value="{{hotel_inf.template_color}}" />

                            <select ng-model="hotel_inf.template_color"
                                ng-options="id as name for (id, name) in template_settings.template_color"
                                ng-change="changeTemplateColor()">
                            </select>
                        </div>
                    </div>

                <!-- start loop -->
                    <label ng-hide="hotel_inf.manual_custom_css" ng-if="item.define_widget!='yes' && item.tooltip_help != ''"
                        ng-repeat-start="item in template_settings.options">
                        {{item.title}}
                        <span class="has-tip tip-right icon icon-info2" data-options="disable_for_touch:true"
                            tooltip-help="{{item.tooltip_help}}"></span>
                    </label>

                    <!-- Type is color -->
                    <div ng-hide="hotel_inf.manual_custom_css" class="row" ng-if="item.type == 'color'">
                        <div class="columns large-5" ng-show="item.define_widget!='yes'">
                            <input type="text" ng-name="'options[' + item.name + '][custom_value]'"
                                id="{{item.name}}" minicolors=""
                                ng-model="item.custom_value"
                                ng-init="item.custom_value = item.custom_value ? item.custom_value : item.default_value"
                                ng-class="{'alert-border': ngErrorRequired('options[' + item.name + '][custom_value]')}"
    						    required />

                            <input type="hidden" name="{{'options[' + item.name + '][type]'}}"
                                value="color" />
                        </div>

                        <div class="columns wauto" ng-show="item.define_widget!='yes'">
                            <a class="button info tiny marl1" id="{{item.name + '_default'}}"
                                data-orign="{{item.name}}" href="#"
                                defaultcolor="{{item.default_value}}"><?php print t("Default", array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang));?></a>
                        </div>

                        <div class="left" ng-show="item.define_widget!='yes'">
                            <label class="inline">
                                <!-- <span class="has-tip tip-right icon icon-info2" data-options="disable_for_touch:true"
                                    tooltip-help="{{item.tooltip_help}}"></span> -->
                            </label>
                        </div>
                    </div>

				    <label for="{{item.name}}" class="error" ng-if="item.type == 'color'"
				       ng-show="ngErrorRequired('options[' + item.name + '][custom_value]')">
					  <?php print t('Missing details', array(), array('context'=>'hls;system:1;module:1', 'langcode'=>$lang)); ?>
					</label>
                    <!-- end type color -->

                    <!-- Type is font -->
                    <div ng-hide="hotel_inf.manual_custom_css" class="row" ng-if="item.type == 'font'">
                        <div class="columns large-7">
                            <input type="hidden" name="{{'options[' + item.name + '][custom_value]'}}"
                                value="{{item.custom_value}}"
                                ng-model="item.custom_value"
                                ng-init="item.custom_value = item.custom_value ? item.custom_value : item.default_value" />

                            <div class="font-select" ng-font-select>
                                <select id="{{item.name}}" ng-model="item.custom_value">
                                    <option ng-repeat="(font_id, font_name) in list_font"
                                        value="{{font_id}}" style="{{'font-size: 16px; font-family:' + font_id}}"
                                        ng-selected="font_id == item.custom_value">{{font_name}}</option>
                                </select>
                                <dt><a href="#"><span>Please select font</span></a></dt>
                                <dd>
                                    <ul style="display: none;">
                                        <li ng-repeat="(font_id, font_name) in list_font"
                                            value="{{font_id}}"
                                            ng-class="{'selected': font_id == item.custom_value}"
                                            ng-click="item.custom_value = font_id">
                                            <a href="#" style="{{'font-family:' + font_id}}">
                                                {{font_name}}
                                            </a>
                                        </li>
                                    </ul>
                                </dd>
                            </div>

                            <input type="hidden" name="{{'options[' + item.name + '][type]'}}"
                                value="font" />
                        </div>

                        <div class="left">
                            <label class="inline">
                                <!-- <span class="has-tip tip-right icon icon-info2" data-options="disable_for_touch:true"
                                    tooltip-help="{{item.tooltip_help}}"></span> -->
                            </label>
                        </div>
                    </div>

                    <!-- font options -->
                    <label ng-hide="hotel_inf.manual_custom_css"
                        ng-if="item.type == 'font'"
                        ng-repeat-start="opt in item.options">{{opt.title}}</label>

                    <div ng-hide="hotel_inf.manual_custom_css" class="row">
                        <div class="columns large-7">
                            <input type="text"
                                ng-name="'options[' + item.name + '][options][' + opt.name + '][custom_value]'"
                                ng-model="opt.custom_value"
                                ng-init="opt.custom_value = opt.custom_value ? opt.custom_value : opt.default_value"
                                required="required" />
                        </div>
                    </div>

				    <label for="{{opt.name}}" class="error"
				       ng-show="ngErrorRequired('options[' + item.name + '][options][' + opt.name + '][custom_value]')"
				       ng-repeat-end>
					  <?php print t('Missing details', array(), array('context'=>'hls;system:1;module:1', 'langcode'=>$lang)); ?>
					</label>
                    <!-- end font options -->
                    <!-- end type font -->

                <!-- Type is fullbox -->
                <div ng-hide="hotel_inf.manual_custom_css"
                     ng-if="item.type == 'full_box'" class="clearfix mrgt-5 mrgb5">
                    <input type="radio" name="{{'options[' + item.name + '][background_type]'}}" value="1115px"
                           ng-model="item.background_type" id="{{item.name + 'Box'}}" class="mrgb6 rd-ck-inline left"/>
                    <label for="{{item.name + 'Box'}}" class="inline left mrgr10"><?php print t("Boxed", array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang)); ?></label>
                    <input type="radio" name="{{'options[' + item.name + '][background_type]'}}" value="100%"
                           ng-model="item.background_type" id="{{item.name + 'Full'}}" class="mrgb6 rd-ck-inline left"/>
                    <label for="{{item.name + 'Full'}}" class="inline left mrgr10"><?php print t("Full Width", array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang)); ?></label>
                    <input type="hidden" name="{{'options[' + item.name + '][type]'}}" value="full_box" class="mrgb6"/>
                </div>

                <div ng-hide="hotel_inf.manual_custom_css"
                     class="{{(item.type == 'full_box' && item.background_type == '100%') ? 'row' : 'row hide'}}"
                     ng-if="item.type == 'full_box' && item.background_type == '100%'">
                    <input type="hidden" ng-name="'options[' + item.name + '][background_color]'"
                           id="{{item.name}}"
                           value="{{item.background_color = item.background_color ? item.background_color : item.default_value}}" />
                    <input type="hidden" name="options[full_width][fullWidth_type]" value = "100%"
                           ng-model="item.fullWidth_type" id="{{item.name + 'fullWidth'}}" class="mrgb6 rd-ck-inline left"/>
                    <input type="hidden" name="options[full_width][type]" value="full_width" class="mrgb6"/>
                </div>
                <!-- Full Box -->
                <div ng-hide="hotel_inf.manual_custom_css"
                     class="{{(item.type == 'full_box' && item.background_type == '1115px') ? 'row' : 'row hide'}}"
                     ng-if="item.type == 'full_box' && item.background_type != '100%'">
                    <div class="columns">
                        <label class="ng-scope ng-binding" >
                            <?php print t("General Background", array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang)); ?>
                            <span class="has-tip tip-right icon icon-info2" tooltip-help="general_background"
                                  data-options="disable_for_touch:true"></span>
                        </label>
                    </div>
                    <div class="columns large-5">
                        <input type="text" ng-name="'options[' + item.name + '][background_color]'"
                               id="{{item.name}}" minicolors=""
                               ng-model="item.background_color"
                               ng-init="item.background_color = item.background_color ? item.background_color : item.default_value"
                               ng-class="{'alert-border': ngErrorRequired('options[' + item.name + '][background_color]')}"
                               ng-required="item.background_type == 2"/>
                        <input type="hidden" name="options[full_width][fullWidth_type]" value = "1115px"
                               ng-model="item.fullWidth_type" id="{{item.name + 'Box'}}" class="mrgb6 rd-ck-inline left"/>
                        <input type="hidden" name="options[full_width][type]" value="full_width" class="mrgb6"/>
                    </div>

                    <div class="columns wauto">
                        <a class="button info tiny marl1" data-orign="{{item.name}}" id="{{item.name + '_default'}}"
                           defaultcolor="{{item.default_value}}" href="#"><?php print t("Default", array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang));?></a>
                    </div>

                    <div class="left">
                        <label class="inline">
                            <!-- <span class="has-tip tip-right icon icon-info2" data-options="disable_for_touch:true"
                                tooltip-help="{{item.tooltip_help}}"></span> -->
                        </label>
                    </div>
                </div>

                <label for="{{opt.name}}" class="error"
                       ng-show="ngErrorRequired('options[' + item.name + '][background_color]')">
                    <?php print t('Missing details', array(), array('context'=>'hls;system:1;module:1', 'langcode'=>$lang)); ?>
                </label>

                    <!-- Type is background -->
                    <div ng-hide="hotel_inf.manual_custom_css"
                        ng-if="item.type == 'background'" class="clearfix mrgt-5">
                        <input type="radio" name="{{'options[' + item.name + '][background_type]'}}" value="2"
                            ng-model="item.background_type" id="{{item.name + 'Color'}}" class="mrgb6 rd-ck-inline left"/>
                        <label for="{{item.name + 'Color'}}" class="inline left mrgr10"><?php print t("Solid Colour", array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang)); ?></label>

                        <input type="radio" name="{{'options[' + item.name + '][background_type]'}}" value="1"
                            ng-model="item.background_type" id="{{item.name + 'Image'}}" class="mrgb6 rd-ck-inline left"/>
                        <label for="{{item.name + 'Image'}}" class="inline left mrgr10"><?php print t("Image", array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang)); ?></label>
                        <input type="hidden" name="{{'options[' + item.name + '][type]'}}"
                            value="background" class="mrgb6"/>
                    </div>

                    <!-- Solid color background -->
                    <div ng-hide="hotel_inf.manual_custom_css"
                        class="{{(item.type == 'background' && item.background_type == 2) ? 'row' : 'row hide'}}"
                        ng-if="item.type == 'background'">
                        <div class="columns large-5">
                            <input type="text" ng-name="'options[' + item.name + '][background_color]'"
                                id="{{item.name}}" minicolors=""
                                ng-model="item.background_color"
                                ng-init="item.background_color = item.background_color ? item.background_color : item.default_value"
                                ng-class="{'alert-border': ngErrorRequired('options[' + item.name + '][background_color]')}"
                                ng-required="item.background_type == 2"/>
                        </div>

                        <div class="columns wauto">
                            <a class="button info tiny marl1" data-orign="{{item.name}}" id="{{item.name + '_default'}}"
                                defaultcolor="{{item.default_value}}" href="#"><?php print t("Default", array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang));?></a>
                        </div>

                        <div class="left">
                            <label class="inline">
                                <!-- <span class="has-tip tip-right icon icon-info2" data-options="disable_for_touch:true"
                                    tooltip-help="{{item.tooltip_help}}"></span> -->
                            </label>
                        </div>
                    </div>

				    <label for="{{opt.name}}" class="error"
				       ng-show="ngErrorRequired('options[' + item.name + '][background_color]')">
					  <?php print t('Missing details', array(), array('context'=>'hls;system:1;module:1', 'langcode'=>$lang)); ?>
					</label>
                        <!-- Image background -->
                    <div ng-hide="hotel_inf.manual_custom_css"
                        class="{{(item.type == 'background' && item.background_type == 1) ? 'row' : 'row hide'}}"
                        ng-if="item.type == 'background'" ng-repeat-end>
                        <div class="columns large-7">
                            <div class="row collapse">
                                <div class="columns large-8">
                                    <input type="file" id="{{item.name}}" data-type="background"
                                        fileupload="fileupload_options" />

                                    <input type="text" name="{{'options[' + item.name + '][background_image]'}}"
                                        class="error-nobdrr"
                                        placeholder="<?php print t('No file selected', array(), array('context'=>'hls;system:1;module:1', 'langcode'=>$lang)); ?>" id="{{item.name + 'Text'}}"
                                        value="{{item.background_image}}"
                                        ng-init="list_bg[item.name] = item.background_image" />

                                    <a href="#" id="{{item.name + 'Delete'}}" class="input-text-delete" title="<?php print t('Delete', array(), array('context'=>'hls;system:1;module:1', 'langcode'=>$lang)); ?>">X</a>
                                </div>

                                <div class="columns large-4 error-nobdrl">
                                    <a href="#" class="button postfix" id="{{item.name + 'Button'}}">
                                        <?php print t("Choose File", array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang));?>
                                    </a>
                                </div>

        						<label id="{{item.name + 'ErrorMessage'}}"
        						  class="error"></label>

                                <label id="{{item.name + 'Message'}}" style="display: none;"
                                    class="upload-status"
                                    data-loading="<?php print t('Uploading...', array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang)); ?>"
                                    data-deleting="<?php print t('Deleting...', array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang)); ?>"
                                    data-error="<?php print t('Upload error', array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang)); ?>"></label>
                            </div>
                        </div>
                        <div class="left">
                            <label class="inline">
                                <small>{{template_settings.background_image.width}}x{{template_settings.background_image.height}} px
                                    | {{template_settings.background_image.size}} kb</small>
                                <!-- <span class="has-tip tip-right icon icon-info2" data-options="disable_for_touch:true"
                                    tooltip-help="{{item.tooltip_help}}"></span> -->
                            </label>
                        </div>
                    </div>
                    <!-- end type background -->
                <!-- end loop -->

                    <label>
                        <?php print t("Custom CSS", array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang));?>
                        <span class="has-tip tip-right icon icon-info2" data-options="disable_for_touch:true"
                            tooltip-help="settings_custom_css"></span>
                    </label>
                    <div class="row">
                        <div class="columns large-2">
                            <a class="button info tiny item-block" href="#"
                                ng-click="openCustomCssPopup()"><?php print t("Edit", array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang));?></a>
                        </div>
                        <div class="columns large-10">
                            <a class="button info tiny" href="#"
                                ng-click="deactiveCustomCss(settings, settings.data.settings_form)"
                                ng-show="hotel_inf.manual_custom_css"><?php print t("Deactivate Custom CSS", array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang));?></a>
                        </div>
                    </div>

                </div>
            </div>

            <input type="hidden" name="hotel_inf[manual_custom_css]" value="{{hotel_inf.manual_custom_css}}" />

            <input type="hidden" name="form_build_id" value="{{settings.data.settings_form.form_build_id['#value']}}" />
            <input type="hidden" name="form_token" value="{{settings.data.settings_form.form_token['#value']}}" />
            <input type="hidden" name="form_id" value="{{settings.data.settings_form.form_id['#value']}}" />
        </form>

        <a class="button small" href="#"
            ng-loading-button="saving_settings"
            completed-message="<?php print t('Saved!', array(), array('context'=>'hls;system:1;module:1', 'langcode'=>$lang)); ?>"
            ng-click="save(settings, settings_form)">
            <?php print t("Save", array(), array('context'=>'hls;system:1;module:1', 'langcode'=>$lang));?>
        </a>
        <!-- <span><img src="/sites/all/themes/manage/img/ajax-loader.gif" /></span> -->
    </div>
</div>
<!-- Custom css popup -->
<div id="custom-css" class="reveal-modal" data-reveal>
    <form action="" method="post" id="custom_css_form" name="custom_css_form" novalidate>
        <div class="title row">
            <div class="columns large-12">
                <label>
                    <?php print t("Custom CSS", array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang));?>
                    <span class="has-tip tip-right icon icon-info2" data-options="disable_for_touch:true"
                        tooltip-help="settings_custom_css_popup"></span>
                </label>
            </div>
        </div>
        <div class="content row">
            <div class="columns large-12">
                <div class="large-12">
                    <textarea id="content-css" name="content"
                        style="resize: vertical; min-height: 350px;">{{content_css}}</textarea>
                    <div id="content-css-editor" style="min-height: 350px;" class="bd1"></div>
                </div>
            </div>
        </div>

        <a class="small button left button-grey" href="#" id="reset_custom_css"
            ng-click="resetCustomCss()"><?php print t("Reset", array(), array('context'=>'hls;system:1;module:4;section:3', 'langcode'=>$lang));?></a>
        <a class="small button right mrgl10" href="#"
            ng-loading-button="saving_css"
            completed-message="<?php print t('Saved!'); ?>"
            ng-click="saveCustomCss(settings, settings.data.settings_form)"><?php print t("Save", array(), array('context'=>'hls;system:1;module:1', 'langcode'=>$lang));?></a>

        <a class="close-reveal-modal">&#215;</a>

        <input type="hidden" name="form_build_id" value="{{settings.data.settings_form.form_build_id['#value']}}" />
        <input type="hidden" name="form_token" value="{{settings.data.settings_form.form_token['#value']}}" />
        <input type="hidden" name="form_id" value="{{settings.data.settings_form.form_id['#value']}}" />
    </form>
</div>
<?php include_once 'left-demo.php'; global $user; if($user->language == 'en') $lang = "en_us"; else $lang = $user->language; ?>