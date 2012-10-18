<?php $this->lang->load('lifepress'); ?>

<div id="main_container">

    <?php if (isset($tag)): ?>
        <p id="breadcrumb"><a href="<?php echo $this->config->item('base_url')?>"><?php echo $this->lang->line('home'); ?></a> &rsaquo; <?php echo $this->lang->line('tagged_with'); ?> <?php echo $tag?></p>
    <?php endif; ?>

    <?php if (isset($query)): ?>
        <p id="breadcrumb"><a href="<?php echo $this->config->item('base_url')?>"><?php echo $this->lang->line('home'); ?></a> &rsaquo; <?php echo $this->lang->line('search_for'); ?> <?php echo $query?></p>
    <?php endif; ?>

    <?php if (isset($site)): ?>
        <p id="breadcrumb"><a href="<?php echo $this->config->item('base_url')?>"><?php echo $this->lang->line('home'); ?></a> &rsaquo; <?php echo $this->lang->line('items_from'); ?> <?php echo $site?></p>
    <?php endif; ?>

    <ul id="activity_list">
        <?php foreach ($items as $item): ?>

            <!-- begin conditional content -->
            <li class="item <?php echo $item->feed->get_class()?>">
                <?php if ($item->is_blog_post()): ?>
                    <p class="site_info" style="background: transparent url(<?php echo $item->feed->icon?>) 0 center no-repeat"><?php echo $this->lang->line('posted_a'); ?> <a href="<?php echo $this->config->item('base_url')?>items/site/lifepress">blog entry</a></p>
                <?php else: ?>
                    <p class="site_info" style="background: transparent url(<?php echo $item->feed->icon?>) 0 center no-repeat"><?php echo $this->lang->line('posted_to'); ?> <a href="<?php echo $this->config->item('base_url')?>items/site/<?php echo $item->feed->domain?>"><?php echo $item->feed->domain?></a></p>
                <?php endif; ?>

                <h2><?php echo $item->title ?></h2>

                <p class="original_link"><a href="<?php echo $item->permalink?>"><?php echo $item->permalink?></a></p>

                <?php if ($item->has_content()): ?>
                    <div class="content"><?php echo $item->content ?></div>
                <?php endif; ?>

                <?php if ($item->has_image() && !$item->has_video()): ?>
                    <p><a href="<?php echo $item->permalink ?>"><img src="<?php echo $item->get_image()?>" alt="" /></a></p>
                <?php endif; ?>

                <?php if ($item->has_video()): ?>
                    <div><?php echo $item->get_video()?></div>
                <?php endif; ?>

                <?php if ($item->has_tags()): ?>
                    <ul class="item_tag_list">
                        <li>Tags: </li>

                        <?php foreach ($item->get_tags() as $tag): ?>
                            <li><a href="<?php echo $this->config->item('base_url')?>items/tag/<?php echo $tag->slug?>"><?php echo $tag->name?></a></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <p class="date"><?php echo $item->get_human_date()?> | <a href="<?php echo $item->get_local_permalink()?>"><?php echo $this->lang->line('comments'); ?> &raquo;</a></p>
            </li>

        <?php endforeach; ?>
    </ul>

    <p id="pagination"><?php echo $pages?></p>
</div>
