<?php if(!$this->tpl_var['userhash']){ ?>
<?php $this->_compileInclude('header'); ?>
<body>
<div id="content">
	<div class="pages" id="basic">
<?php } ?>
		<header class="container-fluid" style="background-color:#337AB7;">
			<h5 class="text-center">
				<em style="font-size:2rem;" class="pull-left glyphicon glyphicon-chevron-left" onclick="javascript:$.goPrePage();"></em>
				考场列表
				<em style="font-size:2rem;" class="pull-right glyphicon glyphicon-home" onclick="javascript:$.goPage($('#page1'));"></em>
			</h5>
		</header>
		<div class="container-fluid">
			<?php $bid = 0;
 foreach($this->tpl_var['basics']['data'] as $key => $basic){ 
 $bid++; ?>
			<div style="clear:both;overflow:hidden;background:#FFFFFF;margin-top:0.5rem">
				<div class="col-xs-12" style="padding-left:0px;">

					<div style="clear:both;overflow:hidden;background:#FFFFFF;margin-top:0.5rem">
						<div class="col-xs-4">
							<a href="index.php?exam-phone-basics-detail&basicid=<?php echo $basic['basicid']; ?>" class="ajax" data-page="basic" data-target="basic"><img src="<?php if($basic['basicthumb']){ ?><?php echo $basic['basicthumb']; ?><?php } else { ?>app/exam/styles/image/paper.png<?php } ?>" style="width:10rem;margin-top:0.5rem"/></a>
						</div>
						<div class="col-xs-8" style="padding:0.2rem;">
							<div class="text-left" style="padding:0.2rem;">
								<a href="index.php?exam-phone-basics-detail&basicid=<?php echo $basic['basicid']; ?>" class="ajax" data-page="basic" data-target="basic">
									<h5>
										<?php echo $basic['basic']; ?>
										<?php if($this->tpl_var['data']['openbasics'][$basic['basicid']]){ ?>
										<b class="text-info" style="font-size:1rem;">已开通</b>
										<?php } elseif($basic['basicdemo']){ ?>
										<b class="text-success" style="font-size:1rem;">免费</b>
										<?php } ?>
									</h5>
									<p style="font-size:1rem;"><?php echo $basic['basicdescribe']; ?></p>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php } ?>
			<?php if($this->tpl_var['basics']['pages']){ ?>
			<div style="clear:both;overflow:hidden;background:#FFFFFF;margin-top:0.5rem">
				<div class="col-xs-12" style="padding-left:0px;">
					<ul class="pagination pull-right">
						<?php echo $this->tpl_var['basics']['pages']; ?>
					</ul>
				</div>
			</div>
			<?php } ?>
		</div>
		<?php $this->_compileInclude('footer'); ?>
		<?php if(!$this->tpl_var['userhash']){ ?>
    </div>
</div>
</body>
</html>
<?php } ?>