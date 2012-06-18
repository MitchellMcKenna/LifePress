<div id="main_content">
    <?php if (isset($editing)): ?>
        <div id="breadcrumb"><a href="<?php echo $referer?>">Back to Items</a> &rsaquo; Editing <span class="highlight"><?php echo $item->item_title?></span></div>
    <?php endif; ?>

    <?php echo validation_errors();?>

    <form action="" method="post" class="generic">
        <div class="row">
            <label class="title" for="title_input">Title</label>
            <input id="title_input" type="text" class="text_input" name="title" value="<?php if (!$_POST && isset($item)): echo $item->item_title; else: echo $this->input->post('title'); endif;?>" />
        </div>

        <div class="row">
            <label class="title" for="wmd_input">Content</label>
            <div class="wmd-panel">
                <div id="wmd-button-bar"></div>
                <textarea id="wmd-input" class="wmd-input text_input" name="content"><?php if (!$_POST && isset($item)): echo $item->item_content; else: echo $this->input->post('content'); endif;?></textarea>
            </div>
        </div>

        <div class="row">
            <label class="title" for="wmd-preview">Preview</label>
            <div id="wmd-preview" class="wmd-panel wmd-preview"></div>
        </div>

        <div class="row">
            <label class="title" for="tag_input">Tags</label>
            <input id="tag_input" type="text" class="text_input" name="tags" value="<?php if (!$_POST && isset($tag_string)): echo $tag_string; else: echo $this->input->post('tags'); endif;?>" />
        </div>

        <span class="input_explain">Separate with commas e.g. these, are, my, tags</span>

        <?php if (isset($editing) && empty($item->item_feed_id)): ?>
            <div class="row">
                <label class="title" for="feed_url_input">Timestamp / Publish Options</label>
                <span class="option_container">
                    <span class="option"><input<?php if (!$_POST || $this->input->post('timestamp') == 'no_change'): ?> checked="checked"<?php endif; ?> type="radio" name="timestamp" value="no_change" id="radio_no_change" /> <label for="radio_no_change">No Change</label></span>
                    <span class="option"><input<?php if ($this->input->post('timestamp') == 'make_current'): ?> checked="checked"<?php endif; ?> type="radio" name="timestamp" value="make_current" id="radio_make_current" /> <label for="radio_make_current">Make Current Time</label></span>
                    <?php if ($item->item_status == 'draft'): ?>
                        <span class="option"><input<?php if ($this->input->post('timestamp') == 'make_current_publish'): ?> checked="checked"<?php endif; ?> type="radio" name="timestamp" value="make_current_publish" id="radio_make_current_publish" /> <label for="radio_make_current_publish">Make Current Time and Publish Now</label></span>
                    <?php endif; ?>
                </span>
            </div>
        <?php endif; ?>

        <div class="buttons">
            <input type="hidden" value="false" name="draft" />

            <div class="clear"></div>

            <?php if (isset($editing)): ?>
                <input type="hidden" name="referer" value="<?php echo $referer?>" />
                <input type="hidden" name="save_edit" value="true" />

                <button type="submit" class="positive"><img src="<?php echo $this->config->item('base_url')?>public/images/system/icons/silk/accept.png" alt="" />Save Changes</button>
            <?php else: ?>
                <button type="submit" class="positive"><img src="<?php echo $this->config->item('base_url')?>public/images/system/icons/silk/accept.png" alt="" />Publish Post Now</button>

                <a href="#draft" class="draft_button"><img src="<?php echo $this->config->item('base_url')?>public/images/system/icons/silk/page_white.png" alt="" />Save as Draft</a>
            <?php endif; ?>
        </div>

    </form>

</div>

<div id="side_content">
    <p class="tip"><strong>Shorthand</strong><br />The blog post content area supports the <a href="http://daringfireball.net/projects/markdown/syntax" rel="external">Markdown</a> method of shorthand markup.</p>
</div>

<script type="text/javascript" src="/public/scripts/pagedown/Markdown.Converter.js"></script>
<script type="text/javascript" src="/public/scripts/pagedown/Markdown.Sanitizer.js"></script>
<script type="text/javascript" src="/public/scripts/pagedown/Markdown.Editor.js"></script>
<script type="text/javascript">
    (function () {
        var converter1 = Markdown.getSanitizingConverter();
        var editor1 = new Markdown.Editor(converter1);
        editor1.run();
    })();
</script>
