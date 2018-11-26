
<nav data-toggle="wy-nav-shift" class="wy-nav-side stickynav">
		<div class="wy-side-nav-search">
			<a href="." class="icon icon-home"> Extract Transform Load</a>
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
						<li class="toctree-l2 toc-item <?=$toccurrent==='mongo/products' ? 'toc-current' : ''?>">
							<a href="<?php echo base_url();?>mongo/products" title="Extract Mongo CRUD">E: Mongo</a>
						</li>
                        <li class="toctree-l2 toc-item <?=$toccurrent==='mongo/aggregated' ? 'toc-current' : ''?>">
                            <a href="<?php echo base_url();?>mongo/aggregated" title="Transform Mongo CRUD">T: Mongo</a>
                        </li>
                        <li class="toctree-l2 toc-item <?=$toccurrent==='sql/temp_products' ? 'toc-current' : ''?>">
                            <a href="<?php echo base_url();?>sql/temp_products" title="Transform SQL CRUD">T: SQL</a>
                        </li>
                        <li class="toctree-l2 toc-item <?=$toccurrent==='sql/products' ? 'toc-current' : ''?>">
                            <a href="<?php echo base_url();?>sql/products" title="Load SQL CRUD">L: SQL</a>
                        </li>
					</ul>
                </li></ul>

		</div>
	</nav>