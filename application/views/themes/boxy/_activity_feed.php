<div id="main_container">
    <?php if (isset($tag)): ?>
        <p id="breadcrumb"><a href="<?php echo $this->config->item('base_url')?>">Home</a> &rsaquo; Items tagged with <?php echo $tag?></p>
    <?php endif; ?>

    <?php if (isset($query)): ?>
        <p id="breadcrumb"><a href="<?php echo $this->config->item('base_url')?>">Home</a> &rsaquo; Search for <?php echo $query?></p>
    <?php endif; ?>

    <?php if (isset($site)): ?>
        <p id="breadcrumb"><a href="<?php echo $this->config->item('base_url')?>">Home</a> &rsaquo; Items from <?php echo $site?></p>
    <?php endif; ?>

    <ul id="activity_list">
        <?php if (!empty($items)): ?>
            <?php foreach ($items as $itr => $item): ?>
                <!-- begin conditional content -->

                <li class="item <?php echo $item->feed->get_class()?> <?php if (($itr + 1) % 3 == 0): ?> last<?php endif; ?>">
                    <p class="site_info" style="background: white url(<?php echo $item->feed->icon; ?>) 3px center no-repeat">I posted to <a href="<?php echo $this->config->item('base_url')?>items/site/<?php echo $item->feed->domain?>"><?php echo $item->feed->domain?></a></p>
                    <div class="item_inner">

                        <!-- domain-specific boxes -->

                        <?php if ($item->feed->domain == 'twitter.com'): ?>
                            <p class="twitter_user"><a href="<?php echo $this->config->item('base_url')?>items/site/<?php echo $item->feed->domain; ?>"><img src="<?php echo $this->config->item('theme_folder')?>images/me_twitter.jpg" alt="" /></a></p>
                            <p class="twitter_tweet"><?php echo $item->title?></p>
                        <?php elseif ($item->feed->domain == 'vimeo.com'): ?>
                            <?php echo $item->get_video()?>
                            <p class="vimeo_title"><a href="<?php echo $item->get_local_permalink()?>/<?php echo $item->name?>"><?php echo $item->title?></a></p>
                        <?php elseif ($item->feed->domain == 'youtube.com'): ?>
                            <?php echo $item->get_video()?>
                            <p class="youtube_title"><a href="<?php echo $item->get_local_permalink()?>/<?php echo $item->name?>"><?php echo $item->title?></a></p>
                            <p><?php echo word_limiter(strip_tags($item->content), 8)?></p>
                        <?php elseif ($item->feed->domain == 'digg.com'): ?>
                            <div class="inner_container">
                                <p class="digg_title"><a href="<?php echo $item->get_local_permalink()?>/<?php echo $item->name?>"><?php echo $item->title?></a></p>
                                <p><?php echo word_limiter(strip_tags($item->content), 38)?></p>
                            </div>
                        <?php elseif ($item->feed->domain == 'flickr.com'): ?>
                            <p class="activity_image_text"><a href="<?php echo $item->get_local_permalink()?>/<?php echo $item->name?>"><?php echo $item->title?></a><span class="activity_image_content"></span></p>
                            <a class="activity_image" href="<?php echo $item->get_local_permalink()?>/<?php echo $item->name?>" style="background: url(<?php echo $item->data[$item->feed->get_class()]['image']['m']?>) center center no-repeat"></a>
                        <?php elseif (!$item->feed_id): //this means it came from LifePress itself ?>
                            <div class="inner_container">
                                <p class="blog_title"><a href="<?php echo $item->get_local_permalink()?>/<?php echo $item->name?>"><?php echo $item->title?></a></p>
                                <p class="blog_cite">A blog post</p>
                            </div>
                        <?php else: //generic container with instructions ?>
                            <div class="inner_container instructions">
                                <p><strong>The Boxy theme does not have a custom style for this type of item.</strong></p>
                                <p>You can create one by editing the <code>_activity_feed.php</code> and <code>main.css</code> files.</p>
                                <p>Please read the <a href="http://code.google.com/p/sweetcron/wiki/Themes">Theme Docs</a> and <a href="http://code.google.com/p/sweetcron/wiki/API">API</a> for more information.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <p class="date"><?php echo $item->get_human_date()?> | <a href="<?php echo $item->get_local_permalink()?>/<?php echo $item->name?>">Comments &raquo;</a></p>
                </li>

            <?php endforeach; ?>
        <?php endif; ?>
    </ul>

    <div class="clear"></div>

    <p id="pagination">Page: <?php echo $pages?></p>
</div>
