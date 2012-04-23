</div>
<?php if (isset($this->config)) : ?>
<p><input type="hidden" value="<?php echo $this->config->item('base_url');?>" name="base_url" /></p>
<?php endif; ?>
<div class="clear"></div>
<div id="footer">Powered by <a href="http://code.google.com/p/lifepress/" rel="external">LifePress</a> <?php echo $this->config->item('sweetcron_version');?> <?php echo $this->config->item('lifepress_release');?></div>
</body>
</html>
