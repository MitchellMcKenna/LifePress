<div id="main_content">

    <?php echo validation_errors();?>

    <form action="" method="post" class="generic">
        <div class="row">
            <label class="title" for="title_input">Title</label>
            <input id="title_input" type="text" class="text_input" name="title" value="<?php echo set_value('title'); ?>" />
        </div>

        <div class="row">
            <label class="title" for="wmd_input">Content</label>
            <div class="wmd-panel">
                <div id="wmd-button-bar"></div>
                <textarea id="wmd-input" class="wmd-input text_input" name="content"><?php echo set_value('content'); ?></textarea>
            </div>
        </div>

        <div class="row">
            <label class="title" for="wmd-preview">Preview</label>
            <div id="wmd-preview" class="wmd-panel wmd-preview"></div>
        </div>

        <div class="row">
            <label class="title" for="tag_input">Tags</label>
            <input id="tag_input" type="text" class="text_input" name="tags" value="<?php echo set_value('tags'); ?>" />
        </div>

        <span class="input_explain">Separate with commas e.g. these, are, my, tags</span>

        <div class="buttons">
            <input type="hidden" value="false" name="draft" />

            <div class="clear"></div>

            <button type="submit" class="positive"><img src="<?php echo base_url("public/images/system/icons/silk/accept.png"); ?>" alt="" />Publish Post Now</button>

            <a href="#draft" class="draft_button"><img src="<?php echo base_url("public/images/system/icons/silk/page_white.png"); ?>" alt="" />Save as Draft</a>
        </div>

    </form>

</div>

<div id="side_content">
    <p class="tip"><strong>Shorthand</strong><br />The blog post content area supports the <a href="http://daringfireball.net/projects/markdown/syntax" rel="external">Markdown</a> method of shorthand markup.</p>
</div>

<script type="text/javascript" src="<?php echo base_url("public/scripts/pagedown/Markdown.Converter.js"); ?>"></script>
<script type="text/javascript" src="<?php echo base_url("public/scripts/pagedown/Markdown.Sanitizer.js"); ?>"></script>
<script type="text/javascript" src="<?php echo base_url("public/scripts/pagedown/Markdown.Editor.js"); ?>"></script>
<script type="text/javascript">
    (function () {
        var converter1 = Markdown.getSanitizingConverter(),
            editor1 = new Markdown.Editor(converter1);
        editor1.run();
    })();
</script>
