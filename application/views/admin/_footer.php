        </div>

        <?php if (isset($this->config)) : ?>
            <p><input type="hidden" value="<?php echo $this->config->item('base_url');?>" name="base_url" /></p>
        <?php endif; ?>

        <div class="clear"></div>

        <div id="footer">
            Powered by <a href="http://github.com/mitchellmckenna/lifepress/" rel="external">LifePress</a> <?php echo $this->config->item('lifepress_version');?>
        </div>
    </body>
</html>
