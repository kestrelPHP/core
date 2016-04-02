<header>
    <div class="container">
        <div class="top_nav">
            <div class="container">
                <!--<div class="left col-xs-12 col-sm-6 main_h1"><h1></h1></div>-->
                <div class="col-xs-12">
                    <ul class="pull-right">
                        <li class="inline">
                            <a href="mailto:dinhtrongnam@gmail.com"><i class="fa fa-envelope"></i> dinhtrongnam@gmail.com</a>
                        </li>
                        <li class="inline">
                            <i class="fa fa-phone"></i> (+84) 977 645 045
                        </li>
                        <li class="inline">
                            <a href="<?php echo $link_home_vn; ?>"><div class="img-thumbnail flag flag-icon-background flag-icon-vn" style="padding: 10px; border: 0px solid #ddd;" title="<?php echo $lang_home_vn; ?>"></div></a>
                            <a href="<?php echo $link_home_en; ?>"><div class="img-thumbnail flag flag-icon-background flag-icon-gb" style="padding: 10px; border: 0px solid #ddd;" title="<?php echo $lang_home_en; ?>"></div></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="navbar navbar-default ">
            <div class="navbar-header">
                <button data-target=".navbar-collapse" data-toggle="collapse" class="navbar-toggle" type="button">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a href="<?php echo $link_home; ?>" class="navbar-brand site-logo">
                    <img src="app/static/theme/image/logo.png" class="img-responsive" width="70" height="70" />
                    <span class="logo_title"> Laravel - Sximo</span>
                    <span class="logo_subtitle">Internal PHP Application</span>
                </a>
            </div>

            <div class="navbar-collapse collapse">
                <ul class="nav navbar-nav navbar-collapse collapse navbar-right"  id="topmenu">
                    <li class="<?php if($link_current == $link_service) echo 'active'; ?>">
                        <a  href="<?php echo $link_service; ?>" >
                            <i class=""></i> <span> <?php echo $lang_service; ?> </span>
                        </a>
                    </li>
                    <li class="<?php if($link_current == $link_about) echo 'active'; ?>" >
                        <a href=" <?php echo $link_about; ?>" >
                            <i class=""></i> <span> <?php echo $lang_about; ?> </span>
                        </a>
                    </li>
                    <li class="<?php if($link_current == $link_faq) echo 'active'; ?>" >
                        <a href=" <?php echo $link_faq; ?>" >
                            <i class=""></i> <span> <?php echo $lang_faq; ?> </span>
                        </a>
                    </li>
                    <li class="<?php if($link_current == $link_portfolio) echo 'active'; ?>" >
                        <a href=" <?php echo $link_portfolio; ?>" >
                            <i class=""></i> <span> <?php echo $lang_portfolio; ?> </span>
                        </a>
                    </li>
                    <li class="<?php if($link_current == $link_blog) echo 'active'; ?>" >
                        <a href="<?php echo $link_blog; ?>">
                            <i class=""></i> <span> <?php echo $lang_blog; ?> </span>
                        </a>
                    </li>
                    <li class="<?php if($link_current == $link_contact) echo 'active'; ?>" >
                        <a href=" <?php echo $link_contact; ?>" >
                            <i class=""></i> <span> <?php echo $lang_contact; ?> </span>
                        </a>
                    </li>

                    <li><a href="javascript://ajax" class="dropdown-toggle" data-toggle="dropdown"><span class="label label-success"> My Account</span></a>
                        <ul class="dropdown-menu dropdown-menu-right">

                            <li><a href="user/login"><i class="fa fa-sign-in"></i> Log In</a></li>
                            <li><a href="user/register"><i class="fa fa-user"></i> Registration</a></li>

                        </ul>

                    </li>

                </ul>
            </div><!--/nav-collapse -->
        </div><!-- /container -->
    </div>
</header>