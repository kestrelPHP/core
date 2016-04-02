<?php
/**
 * @file
 * Partial listing of UsersList Page
 */
global $user; if($user->language == 'en') $lang = "en_us"; else $lang = $user->language;
?>
<div id="misc" class="clearfix">
    <h5><?php print t('User Management', array(), array('context'=>'hls;system:1;module:10;section:28', 'langcode'=>$lang)); ?></h5>
    <input type="hidden" id="meta-title" value="<?php print t("Misc", array(), array('langcode'=>$lang))." | ". t("User Management", array(), array('langcode'=>$lang));?>">
    <div class="block pnl-filter">
        <form>
            <div class="row">
                <div class="large-3 columns">
                    <label><?php print t('Destination', array(), array('context'=>'hls;system:1;module:10;section:28', 'langcode'=>$lang)); ?></label>
                    <div class="large-10">
                        <select id="destinationFilter" name="destinationFilter"
                                ng-change="changeDestination(filter.destinationFilter)"
                                ng-model="filter.destinationFilter">
                            <?php echo $listDestination;?>
                        </select>
                    </div>
                </div>
                <div class="large-3 columns">
                    <label><?php print t('Accommodation', array(), array('context'=>'hls;system:1;module:10;section:28', 'langcode'=>$lang)); ?></label>
                    <div class="large-10">
                        <select id="hotelFilter" name="hotelFilter">
                            <?php echo $listAccommodation;?>
                        </select>
                    </div>
                </div>
                <div class="large-3 columns">
                    <label>
                        <?php print t('User Details', array(), array('context'=>'hls;system:1;module:10;section:28', 'langcode'=>$lang)); ?>
                        <span class="has-tip tip-right icon icon-info2" data-options="disable_for_touch:true"
                              tooltip-help="user_detail"></span>
                    </label>
                    <div class="large-10">
                        <input type="text" id="userDetailFilter" name="userDetailFilter"
                               ng-keyup="$event.keyCode == 13 ? find() : null"
                               ng-model="filter.userDetailFilter">
                    </div>
                </div>
                <div class="large-3 columns">
                    <a href="#" ng-click="find()" class="button small right">
                        <?php print t('Filter', array(), array('context'=>'hls;system:1;module:1', 'langcode'=>$lang)); ?>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <label class="text-right">
        <a ng-click="edit(0)" href="">
            <small><span class="icon-plus icon-gray marr3"></span>
                <span class="txt-dark-gray"><?php print t('add new user', array(), array('context'=>'hls;system:1;module:10;section:28', 'langcode'=>$lang)); ?></span></small>
        </a>
    </label>

    <table width="100%" class="tbl-border mrgt10">
        <thead>
        <tr>
            <th width="17%" ng-click="sortOnValue('user_name')">
                <?php print t('Username', array(), array('context'=>'hls;system:1;module:10;section:28', 'langcode'=>$lang)); ?> <span class="icon-menu" ng-class="sortList['user_name']"></span>
            </th>
            <th width="19%" ng-click="sortOnValue('name')">
                <?php print t('Name', array(), array('context'=>'hls;system:1;module:10;section:28', 'langcode'=>$lang)); ?>  <span class="icon-menu" ng-class="sortList['name']"></span>
            </th>
            <th width="19%" ng-click="sortOnValue('email')">
                <?php print t('Contact Email', array(), array('context'=>'hls;system:1;module:10;section:28', 'langcode'=>$lang)); ?>  <span class="icon-menu" ng-class="sortList['email']"></span>
            </th>
            <th width="10%" ng-click="sortOnValue('user_type')">
                <?php print t('User Type', array(), array('context'=>'hls;system:1;module:10;section:28', 'langcode'=>$lang)); ?>  <span class="icon-menu" ng-class="sortList['user_type']"></span>
            </th>
            <th width="17%" ng-click="sortOnValue('login')">
                <?php print t('Last Login (GMT)', array(), array('context'=>'hls;system:1;module:10;section:28', 'langcode'=>$lang)); ?>  <span class="icon-menu" ng-class="sortList['login']"></span>
            </th>
            <th width="8%"><?php print t('Active', array(), array('context'=>'hls;system:1;module:10;section:28', 'langcode'=>$lang)); ?>
                <span class="has-tip tip-right icon icon-info2" data-options="disable_for_touch:true" tooltip-help="auser_list_active"></span></th>
            <th width="10%"><?php print t('Admin', array(), array('context'=>'hls;system:1;module:10;section:28', 'langcode'=>$lang)); ?></th>
        </tr>
        </thead>
        <tbody>
        <tr ng-repeat="(id, item) in list">
            <td ng-repeat="td in displayOrder">
                {{item[td]}}
            </td>
            <td align="center">
                <input type="checkbox" ng-click="active($event,item.id)"
                       ng-checked="item.status == 1" >
            </td>
            <td align="center" class="action-func">
            		<span style="cursor: pointer;"  class="icon-lock"
                          ng-click="unlockUser(item.id)" ng-show="item.has_blocked == 1 && item.status == 0"></span>
                    <span style="cursor: pointer;" class="icon-wrench2"
                          ng-click="edit(item.id)"></span>                    
                    <span style="cursor: pointer;"  class="icon-dark-gray icon-trash"
                          ng-click="delItem(item.id,item.user_name)"></span>
            </td>
        </tr>
        <tr ng-show="fetched && totalItem == 0">
            <td colspan="6"><?php print t('No Hotelier Found', array(), array('context'=>'hls;system:1;module:10;section:31', 'langcode'=>$lang)); ?></td>
        </tr>
        </tbody>
    </table>

    <div class="bottom-list bg-blue" ng-show="totalItem > 0">
        <div class="right">
            <pagination prev="Prev" next="Next"
                        of="of" itemst="items" getfirstpage="getFirstPage"
                        paging="paging" totalpage="totalPage" totalitem="totalItem" pagingnow ="pagingNow"
                        totalresult="totalResult" getpage="getPage" itemfrom="itemFrom"></pagination>
        </div>
        <div class="clearfix"></div>
    </div>
</div>

<!-- Add new user -->
<div id="add-new" class="reveal-modal small" data-reveal>
<div class="title row">
    <div class="columns large-12">
        <label ng-hide="users.data.users['#value'].uid">
            <?php print t('Add User', array(), array('context'=>'hls;system:1;module:10;section:28', 'langcode'=>$lang)); ?>
        </label>
        <label ng-show="users.data.users['#value'].uid">
            <?php print t('Edit User', array(), array('context'=>'hls;system:1;module:10;section:28', 'langcode'=>$lang)); ?>
        </label>
    </div>
</div>

<div class="content">
<div class="generating" ng-show="loading_user">
    <img src="/sites/all/themes/manage/img/ajax-loader.gif">
</div>

<form action="{{users.data['#action']}}" method="post" name="users_form"
      id="{{users.data.form_id['#id']}}" novalidate
      ng-submit="save(users, users_form, multiSelected)"
      ng-hide="loading_user">

<!--  -->
<div ng-if="start_edit_user">

    <div class="row">
        <div class="columns large-6">
            <label class="mrgt10">
                <?php print t('First name', array(), array('context'=>'hls;system:1;module:10;section:28', 'langcode'=>$lang)); ?>
            </label>
            <div class="large-11">
                <input type="text" placeholder="" required
                       name="first_name"
                       id="first_name"
                       ng-model="users.data.users['#value'].first_name"
                       ng-class="{'alert-border': ngErrorRequired('users_form','first_name')}"
                       tabindex="1"
                    />
                <label for="first_name" class="error"
                       ng-show="ngErrorRequired('users_form','first_name')">
                    <?php print t('First name is required', array(), array('context'=>'hls;system:1;module:10;section:28', 'langcode'=>$lang)); ?>
                </label>
            </div>
            <label><?php print t('User Name', array(), array('context'=>'hls;system:1;module:10;section:28', 'langcode'=>$lang)); ?></label>
            <div class="large-11">
                <input type="text" required name="name" id="name" autocomplete="off"
                       ng-model="users.data.users['#value'].name"
                       ng-class="{'alert-border': ngErrorRequired('users_form','name')}"
                       tabindex="3"
                    />
                <label for="name" class="error"
                       ng-show="ngErrorRequired('users_form','name')">
                    <?php print t('User name is required', array(), array('context'=>'hls;system:1;module:10;section:28', 'langcode'=>$lang)); ?>
                </label>
                <label for="name" class="error" id="alert-username">
                    <?php print t('Username already existed', array(), array('context'=>'hls;system:1;module:10;section:28', 'langcode'=>$lang)); ?>
                </label>
            </div>
            <div class="large-10">
                <label>
                    <?php print t('Password', array(), array('context'=>'hls;system:1;module:10;section:28', 'langcode'=>$lang)); ?>
                    <span class="has-tip tip-right icon icon-info2" data-options="disable_for_touch:true"
                          tooltip-help="user_pass"></span>
                    <span class="right pass-txt"> {{passShow}} </span>
                </label>
            </div>
            <div class="large-10 left">
                <input type="password" placeholder="" autocomplete="off" id="pass1"
                       ng-model="pass1" name="pass1" id="pass1" tabindex="5"/>
                <label for="pass1" class="error" id="alert-user-pass1">
                    <?php print t('Password required.', array(), array('context'=>'hls;system:1;module:10;section:28', 'langcode'=>$lang)); ?>
                </label>
            </div>
                    <span style="cursor: pointer;" class="left icon-rotate-right icon-gray-dark mrgl5 mrgt7"
                          ng-click="generatePass()"></span>
            <div class="clearfix"></div>
            <div id="hidden-user-type">
                <label><?php print t('User Type', array(), array('context'=>'hls;system:1;module:10;section:28', 'langcode'=>$lang)); ?></label>
                <div class="large-11">
                    <select ng-change="changeType()" id="user_type"
                            ng-model="users.data.users['#value'].user_type"
                            ng-options="item.id as item.name for item in users.data.list_type['#value']" tabindex="7"/>
                    </select>
                </div>
            </div>

        </div>
        <div class="columns large-6">
            <label class="mrgt10">
                <?php print t('Last Name', array(), array('context'=>'hls;system:1;module:10;section:28', 'langcode'=>$lang)); ?>
            </label>
            <div class="large-11">
                <input type="text" required
                       name="last_name" id="last_name"
                       ng-model="users.data.users['#value'].last_name"
                       ng-class="{'alert-border': ngErrorRequired('users_form','last_name')}"
                       tabindex="2"
                    />
                <label for="last_name" class="error"
                       ng-show="ngErrorRequired('users_form','last_name')">
                    <?php print t('Last name is required', array(), array('context'=>'hls;system:1;module:10;section:28', 'langcode'=>$lang)); ?>
                </label>
            </div>
            <label><?php print t('Email', array(), array('context'=>'hls;system:1;module:10;section:28', 'langcode'=>$lang)); ?></label>
            <div class="large-11">
                <input type="email" required name="mail" id="mail"
                       ng-model="users.data.users['#value'].mail"
                       ng-class="{'alert-border': ngErrorRequired('users_form','mail') || ngInvalid('users_form','mail') }"
                       tabindex="4"
                    />
                <label for="mail" class="error"
                       ng-show="ngErrorRequired('users_form','mail')">
                    <?php print t('Email is required', array(), array('context'=>'hls;system:1;module:10;section:28', 'langcode'=>$lang)); ?>
                </label>
                <label for="mail" class="error" id="alert-user-email">
                    <?php print t('Email already existed', array(), array('context'=>'hls;system:1;module:10;section:28', 'langcode'=>$lang)); ?>
                </label>
                <label for="mail" class="error"
                       ng-show="!ngErrorRequired('users_form','mail') && ngInvalid('users_form','mail')">
                    <?php print t('Invalid email', array(), array('context'=>'hls;system:1;module:1', 'langcode'=>$lang)); ?>
                </label>
            </div>
            <label>
                <?php print t('Re-type Password', array(), array('context'=>'hls;system:1;module:10;section:28', 'langcode'=>$lang)); ?>
            </label>
            <div class="large-11">
                <input type="password" placeholder=""
                       name="pass2" ng-model="pass2" id="pass2" tabindex="6"/>
                <label for="pass2" class="error" id="alert-user-pass">
                    <?php print t('Passwords do not match', array(), array('context'=>'hls;system:1;module:10;section:28', 'langcode'=>$lang)); ?>
                </label>
            </div>
            <div id="hidden-partner" ng-show="users.data.users['#value'].user_type == 2 || users.data.users['#value'].user_type == 3">
                <label><?php print t('Partner', array(), array('context'=>'hls;system:1;module:10;section:28', 'langcode'=>$lang)); ?></label>
                <div class="large-11" >
                    <select id="partner" name="partner" 
                            ng-change="changePartner(users.data.users['#value'].partner_id, users.data.users['#value'].user_type)"
                            ng-model="users.data.users['#value'].partner_id"
                            ng-class="{'alert-border': ngInvalid('partner', '0')}"
                            required tabindex="8">
                            <option value="0">{{users.data.arr_default['#value'][0]}}</option>
                            <option
                                ng-repeat="item in users.data.list_partner['#value']"
                                value="{{item.id}}"
                                ng-selected="users.data.users['#value'].partner_id == item.id"
                                ng-if="(item.id !== 1 && users.data.users['#value'].user_type == 2) || (users.data.users['#value'].user_type == 3)"
                            >{{item.name}}
                            </option>
                    </select>
                    <label for="partner" class="error"
                           ng-show="ngInvalid('partner','0')">
                        <?php print t('Please select a value', array(), array('context'=>'hls;system:1;module:10;section:28', 'langcode'=>$lang)); ?>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="large-12 columns"
         ng-show="users.data.users['#value'].user_type == 2 || users.data.users['#value'].user_type == 3">
        <div ng-show="users.data.users['#value'].user_type == 3">
            <label class="mrgt5">
                <?php print t('Select Accommodation', array(), array('context'=>'hls;system:1;module:10;section:28', 'langcode'=>$lang)); ?>
                <span class="has-tip tip-right icon icon-info2" data-options="disable_for_touch:true"
                  tooltip-help="user_accm"></span>
            </label>
            <select id="list_hotel" class="mrgt5" multiple ng-multiple="true"
                ng-change="list_hotel_change(multiSelected)"
                ng-model="multiSelected"
                ng-options="item.id as item.name for item in hotelList">
            </select>
        </div>
        <input type="hidden" id="per_none_parent" name="per_none_parent" value="off">
        <table width="100%" class="add-bg mrgt5" id="alert-permission">
            <thead>
            <tr>
                <th width="80%" class="text-left"><?php print t('Solution/Page', array(), array('context'=>'hls;system:1;module:10;section:28', 'langcode'=>$lang)); ?></th>
                <th width="20%"><?php print t('Access', array(), array('context'=>'hls;system:1;module:10;section:28', 'langcode'=>$lang)); ?></th>
            </tr>
            </thead>
            <tbody>
            <tr ng-repeat="item in users.data.list_permission['#value']">
                <td colspan="2" class="table-sub">
                    <table width="100%">
                        <tr>
                            <td width="73%" style="cursor: pointer;" ng-click="show = !show">
                                <span ng-class="!show ? 'icon-caret-right' : 'icon-caret-down'"></span>
                                {{item.title}}
                            </td>
                            <td width="27%" align="center">
                                <input ng-disabled="item.disabled"
                                   name="per_{{item.id}}" ng-change="changePermissionParent(item)"
                                   ng-model="item.checked" type="checkbox">
                            </td>
                        </tr>
                        <tr ng-show="show">
                            <td colspan="2" class="pdd0">
                                <table class="tbl-border-none" width="100%">
                                    <tr ng-repeat="c in item.child | orderBy:'weight'"
                                        ng-if="!item.restrict || item.restrict != users.data.users['#value'].user_type || c.allow_all"
                                        >
                                        <td colspan="2" class="table-sub" ng-if="c.id!='misc_ttr_management' && c.id!='misc_accommodation_list'">
                                            <table width="100%">
                                                <tr>
                                                    <td width="73%" ng-click="showLang = !showLang">
                                                        <span ng-class="!showLang ? 'icon-caret-right' : 'icon-caret-down'"
                                                          ng-if="c.count > 0"></span>&nbsp;
                                                        {{c.title}}</span></td>
                                                    <td align="center" width="27%">
                                                        <input ng-disabled="c.disabled" name="per_{{c.id}}"
                                                           ng-change="changePermissionChild(item, c.checked)"
                                                           ng-show="!c.prefix"
                                                           ng-model="c.checked"  type="checkbox" />
                                                        <!-- ng-show="c.prefix!='news' && c.prefix!='lang' " -->
                                                    </td>
                                                </tr>
                                                <tr ng-show="showLang" class="tran-mana-cont">
                                                    <td colspan="2" class="pdd0">
                                                        <table class="tbl-border-none tran-mana" width="100%">
                                                            <tr ng-repeat="cLang in c.childLang" ng-if="cLang.show==1">
                                                                <td width="73%" style="cursor: pointer;">
                                                                    {{cLang.title}}
                                                                </td>
                                                                <td width="27%" align="center">
                                                                    <input ng-disabled="cLang.disabled"
                                                                           ng-change="changePermissionChildLang(item,c,c.checked,cLang.checked)"
                                                                           name="{{cLang.prefix}}_{{cLang.id}}"
                                                                           ng-model="cLang.checked" type="checkbox" />
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr ng-show="users.data.users['#value'].user_type == 3 && item.title == 'Misc'">
                                        <td colspan="2" class="table-sub">
                                            <table width="100%">
                                                <tr>
                                                    <td width="73%" style="cursor: pointer;" ng-click="showWell = !showWell">
                                                        <span ng-class="!show ? 'icon-caret-right' : 'icon-caret-down'"></span>
                                                        <?php print t('Welcome Pages Management', array(), array('context'=>'hls;system:1;module:10;section:28', 'langcode'=>$lang)); ?>
                                                    </td>
                                                    <td width="27%" align="center"></td>
                                                </tr>
                                                <tr ng-show="showWell">
                                                    <td colspan="2" class="pdd0">
                                                        <table class="tbl-border-none" width="100%">
                                                            <tr>
                                                                <td colspan="2" class="table-sub">
                                                                    <table width="100%">
                                                                        <tr class="tran-mana-cont" ng-repeat="cWellcomePage in users.data.list_wellcome_pages['#value']">
                                                                            <td colspan="2" class="pdd0">
                                                                                <table class="tbl-border-none tran-mana" width="100%">
                                                                                    <tr>
                                                                                        <td width="73%" style="cursor: pointer;">
                                                                                            {{cWellcomePage.title}}
                                                                                        </td>
                                                                                        <td width="27%" align="center">
                                                                                            <input name="{{cWellcomePage.name}}" type="checkbox" value ="{{cWellcomePage.id}}"
                                                                                                   ng-checked="isWellcomepage(cWellcomePage.id,item.title, cWellcomePage.name)" />
                                                                                        </td>
                                                                                    </tr>
                                                                                </table>
                                                                            </td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>

                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                    </table>
                </td>
            </tr>

            </tbody>
        </table>
        <label for="list-permission" class="error" id="list-permission">
            <?php print t('Please select a solution/page', array(), array('context'=>'hls;system:1;module:10;section:28', 'langcode'=>$lang)); ?>
        </label>
    </div>

    <div class="large-12 columns">
        <div class="clearfix">
            <input type="checkbox" class="rd-ck-inline left" name="email_detail"><label class="inline left">
                <?php print t('Email details automatically to user after saving', array(), array('context'=>'hls;system:1;module:10;section:28', 'langcode'=>$lang)); ?>
            </label>
        </div>
    </div>

    <div class="clearfix"></div>

    <input type="hidden" name="user_id" value="{{ users.data.users['#value'].uid }}">
    <input type="hidden" name="form_build_id" value="{{users.data.form_build_id['#value']}}" />
    <input type="hidden" name="form_token" value="{{users.data.form_token['#value']}}" />
    <input type="hidden" name="form_id" value="{{users.data.form_id['#value']}}" />

</div>
<!--  -->
</form>
</div>

<button class="small button mrgl10" ng-click="save(users, users_form, multiSelected)"
        ng-loading-button="saving" completed-message="Saved!">
    <?php print t('Save', array(), array('context'=>'hls;system:1;module:1', 'langcode'=>$lang)); ?>
</button>
<a class="close-reveal-modal">&#215;</a>
</div>
<!-- End add new user -->

<div id="delete-item" class="reveal-modal tiny" data-reveal>
    <div class="title row">
        <div class="columns large-12"><?php print t('Delete Item', array(), array('context'=>'hls;system:1;module:1', 'langcode'=>$lang)); ?></div>
    </div>
    <div class="content row">
        <div class="columns large-12">
            <div class="clearfix">
                <label class="left">
                    <div id="trash-user-name-text"><?php print t('Are you sure you want to delete <span>{-hotel_name-}</span>?', array(), array('context'=>'hls;system:1;module:1', 'langcode'=>$lang)); ?></div>
                </label>
            </div>
        </div>
    </div>
    <a class="small button left button-grey" href="#"
       ng-click="cancelDeleteItem()" ng-loading-button="deleting_item"><?php print t("No", array(), array('context'=>'hls;system:1;module:1', 'langcode'=>$lang)); ?></a>
    <a class="small button" href="#" ng-click="deleteItem()"><?php print t('Yes', array(), array('context'=>'hls;system:1;module:1', 'langcode'=>$lang)); ?></a>
    <a class="close-reveal-modal" ng-click="">&#215;</a>
</div>
