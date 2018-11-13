
<nav data-toggle="wy-nav-shift" class="wy-nav-side stickynav">
		<div class="wy-side-nav-search">
			<a href="." class="icon icon-home"> Admin Panel Centrum Informatyki</a>
		</div>
		<div class="wy-menu wy-menu-vertical" data-spy="affix" role="navigation" aria-label="main navigation">
			<ul class="<?=$current==='extract' ? 'current' : ''?>">
				<li class="toctree-l1 <?=$current==='extract' ? 'current' : ''?>">
					<a class="<?=$current==='extract' ? 'current' : ''?>" href="<?=base_url()?>extract">Extract</a>
				</li>
			</ul>
			<ul class="<?=$current==='transform' ? 'current' : ''?>">
				<li class="toctree-l1 <?=$current==='transform' ? 'current blue' : ''?>">
					<a class="<?=$current==='transform' ? 'current blue' : ''?>" href="<?=base_url()?>transform">Transform</a>
				</li></ul>
            <ul class="<?=$current==='load' ? 'current' : ''?>">
                <li class="toctree-l1 <?=$current==='load' ? 'current green' : ''?>">
                    <a class="<?=$current==='load' ? 'current green' : ''?>" href="<?=base_url()?>load">Load</a>
                </li></ul>
            <ul class="<?=$current==='crudhome' ? 'current' : ''?>">
                <li class="toctree-l1 <?=$current==='crudhome' ? 'current' : ''?>">
                    <a class="<?=$current==='crudhome' ? 'current' : ''?>" href="<?=base_url()?>crudhome">Monitor DB</a>
                    <ul class="current subnav">
						<li class="toctree-l2 toc-item <?=$toccurrent==='emongocrud' ? 'toc-current' : ''?>">
							<a href="<?php echo base_url();?>emongocrud" title="Extract Mongo CRUD">E: Mongo</a>
						</li>
                        <li class="toctree-l2 toc-item <?=$toccurrent==='tmongocrud' ? 'toc-current' : ''?>">
                            <a href="<?php echo base_url();?>tmongocrud" title="Transform Mongo CRUD">T: Mongo</a>
                        </li>
                        <li class="toctree-l2 toc-item <?=$toccurrent==='tsqlcrud' ? 'toc-current' : ''?>">
                            <a href="<?php echo base_url();?>tsqlcrud" title="Transform SQL CRUD">T: SQL</a>
                        </li>
                        <li class="toctree-l2 toc-item <?=$toccurrent==='lsqlcrud' ? 'toc-current' : ''?>">
                            <a href="<?php echo base_url();?>lsqlcrud" title="Load SQL CRUD">L: SQL</a>
                        </li>
					</ul>
                </li></ul>
            <ul class="<?=$current==='extractPage' ? 'current' : ''?>">
                <li class="toctree-l1 <?=$current==='extractPage' ? 'current' : ''?>">
                    <a class="<?=$current==='extractPage' ? 'current' : ''?>" href="<?=base_url()?>extractPage">Extract 1 item</a></li></ul>

		</div>
	</nav>