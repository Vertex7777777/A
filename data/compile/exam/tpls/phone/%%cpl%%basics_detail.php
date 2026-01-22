<?php if(!$this->tpl_var['userhash']){ ?>
<?php $this->_compileInclude('header'); ?>
<body>
<div id="content">
	<div class="pages" id="basic">
<?php } ?>
		<header class="container-fluid" style="background-color:#337AB7;">
			<h5 class="text-center">
				<em style="font-size:2rem;" class="pull-left glyphicon glyphicon-chevron-left" onclick="javascript:$.goPrePage();"></em>
				<?php echo $this->tpl_var['basic']['basic']; ?>
				<em style="font-size:2rem;" class="pull-right glyphicon glyphicon-home" onclick="javascript:$.goPage($('#page1'));"></em>
			</h5>
		</header>
		<div class="container-fluid">
			<div style="clear:both;overflow:hidden;background:#FFFFFF;margin-top:0.5rem">
				<div class="col-xs-12">
					<h4 class="text-center" style="padding:1rem;"><?php echo $this->tpl_var['basic']['basic']; ?></h4>
					<div class="col-xs-5">
						<div class="thumbnail"><img alt="300x200" src="<?php if($this->tpl_var['basic']['basicthumb']){ ?><?php echo $this->tpl_var['basic']['basicthumb']; ?><?php } else { ?>app/exam/styles/image/paper.png<?php } ?>" /></div>
					</div>
					<div class="caption" class="col-xs-7">

						<p>科目：<?php echo $this->tpl_var['subjects'][$this->tpl_var['basic']['basicsubjectid']]['subject']; ?></p>
						<p>地区：<?php echo $this->tpl_var['areas'][$this->tpl_var['basic']['basicareaid']]['area']; ?></p>
						<p>您现有积分：<?php echo $this->tpl_var['_user']['usercoin']; ?> <!--（<a href="index.php?user-center-payfor">支付宝充值</a> / <a href="#myModal" role="button" data-toggle="modal">代金券充值</a>） --></p>
						<?php if($this->tpl_var['isopen']){ ?><p>到期时间：<?php echo date('Y-m-d',$this->tpl_var['isopen']['obendtime']); ?></p><?php } ?>
					</div>
					<hr style="clear:both"/>
					<?php if(!$this->tpl_var['isopen']){ ?>
					<form action="index.php?exam-phone-basics-openit" method="post">
						<?php if(!$this->tpl_var['basic']['basicdemo']){ ?>
							<?php if($this->tpl_var['price']){ ?>
							<p>
								<select name="opentype" class="input-large">
									<?php $pid = 0;
 foreach($this->tpl_var['price'] as $key => $p){ 
 $pid++; ?>
									<option value="<?php echo $key; ?>"><?php echo $p['price']; ?>积分兑换<?php echo $p['time']; ?>天</option>
									<?php } ?>
								</select>
							</p>
							<p>
								<input value="<?php echo $this->tpl_var['basic']['basicid']; ?>" name="basicid" type="hidden"/>
								<input class="btn btn-primary btn-block" value="开通" type="submit"/>
							</p>
							<?php } else { ?>
							<p>
								<input class="btn" value="请管理员先在后台设置价格" type="button"/>
							</p>
							<?php } ?>
						<?php } else { ?>
						<p>
							<input value="<?php echo $this->tpl_var['basic']['basicid']; ?>" name="basicid" type="hidden"/>
							<input class="btn btn-primary btn-block" value="免费开通" type="submit"/>
						</p>
						<?php } ?>
					</form>
					<?php } else { ?>
					<p>
						<a class="btn btn-primary ajax btn-block" href="index.php?exam-phone-index-setCurrentBasic&basicid=<?php echo $this->tpl_var['basic']['basicid']; ?>">进入考场</a>
					</p>
					<?php } ?>
				</div>
			</div>
		</div>
		<?php $this->_compileInclude('footer'); ?>
		<?php if(!$this->tpl_var['userhash']){ ?>
    </div>
</div>
</body>
</html>
<?php } ?>