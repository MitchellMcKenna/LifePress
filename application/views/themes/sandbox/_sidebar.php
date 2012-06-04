<?php $this->lang->load('lifepress'); ?>

<div id="sidebar_container">
    <div id="explanation">
        <p><?php echo $this->lang->line('explanation'); ?></p>
    </div>

    <h3><?php echo $this->lang->line('popular_tags'); ?></h3>

    <ul class="tag_list">
        <?php foreach($popular_tags as $tag): ?>
            <li><a href="<?php echo $this->config->item('base_url')?>items/tag/<?php echo $tag->slug?>"><?php echo $tag->name?></a></li>
        <?php endforeach; ?>
    </ul>

    <h3><?php echo $this->lang->line('search'); ?></h3>

    <form id="search_form" method="post" action="<?php echo $this->config->item('base_url')?>items/do_search">
        <p><input type="text" name="query" class="text_input" value="<?php if (isset($query)): echo $query; endif;?>" /></p>
        <p><input type="submit" value="<?php echo $this->lang->line('search_submit'); ?>"/></p>
    </form>

    </div>
