
<!-- BEGIN DOCK 1 ============================================================ -->
<div id="dock">
        <div class="dock-container">
            <?php if(isset($this->usuario->id_usuario)){ ?>
                <a class="dock-item" href="<?php echo $this->url(array('controller' => 'admin', 'action' => 'index'), '', true); ?>"><span>Início</span><img src="<?php echo $this->baseUrl(); ?>/public/img/dock/home.png" alt="Inicio" /></a>
            <?php }else{ ?>
                <a class="dock-item" href="<?php echo $this->url(array('controller' => 'admin', 'action' => 'index'), '', true); ?>"><span>Realizar login</span><img src="<?php echo $this->baseUrl(); ?>/public/img/dock/home.png" alt="Realizar loginn" /></a>
            <?php } ?>
                <a class="dock-item" href="example2.html"><span>Example&nbsp;2</span><img src="<?php echo $this->baseUrl(); ?>/public/img/dock/email.png" alt="contact" /></a>
                <a class="dock-item" href="example3.html"><span>Example&nbsp;3</span><img src="<?php echo $this->baseUrl(); ?>/public/img/dock/portfolio.png" alt="portfolio" /></a>
                <a class="dock-item" href="all-examples.html"><span>All&nbsp;Examples</span><img src="<?php echo $this->baseUrl(); ?>/public/img/dock/music.png" alt="music" /></a>
                <a class="dock-item" href="#"><span>Video</span><img src="<?php echo $this->baseUrl(); ?>/public/img/dock/video.png" alt="video" /></a>
                <a class="dock-item" href="#"><span>History</span><img src="<?php echo $this->baseUrl(); ?>/public/img/dock/history.png" alt="history" /></a>
                <a class="dock-item" href="#"><span>Calendar</span><img src="<?php echo $this->baseUrl(); ?>/public/img/dock/calendar.png" alt="calendar" /></a>
                <a class="dock-item" href="#"><span>Links</span><img src="<?php echo $this->baseUrl(); ?>/public/img/dock/link.png" alt="links" /></a>
                <a class="dock-item" href="#"><span>RSS</span><img src="<?php echo $this->baseUrl(); ?>/public/img/dock/rss.png" alt="rss" /></a>
                <a class="dock-item" href="#"><span>RSS2</span><img src="<?php echo $this->baseUrl(); ?>/public/img/dock/rss2.png" alt="rss" /></a>
        </div><!-- end div .dock-container -->
</div><!-- end div .dock #dock -->
<br />
<!-- END DOCK 1 ============================================================ -->