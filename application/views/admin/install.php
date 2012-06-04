<div id="main_content">
    <?php echo validation_errors();?>

    <?php if (isset($success)): ?>
        <p>Installation Successful! Your login details are below:</p>

        <p>Username: <?php echo $username; ?><br />Password: <?php echo $password; ?></p>

        <p>You can now <a href="<?php echo $this->config->item('base_url'); ?>admin/login">Log In &raquo;</a></p>
    <?php else: ?>
        <form action="" method="post" class="generic">
            <p>
                <label class="title">Lifestream Title</label>
                <input class="text_input" type="text" name="lifestream_title" value="<?php echo set_value('lifestream_title'); ?>" />
            </p>

            <p>
                <label class="title">Login Username</label>
                <input class="text_input" type="text" name="username" value="<?php echo set_value('username'); ?>" />
            </p>

            <p>
                <label class="title">Email Address</label>
                <input class="text_input" type="text" name="email" value="<?php echo set_value('email'); ?>" />
            </p>

            <div class="buttons">
                <button type="submit"><img src="<?php echo $this->config->item('base_url'); ?>public/images/system/icons/silk/cog_go.png" alt=""/>Install</button>
            </div>
        </form>
    <?php endif; ?>
</div>
