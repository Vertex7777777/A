<?php if(!$this->tpl_var['userhash']){ ?>
<?php $this->_compileInclude('header'); ?>
<body>
<?php $this->_compileInclude('nav'); ?>
<div class="container-fluid">
	<div class="row-fluid">
		<div class="main">
			<div class="col-xs-2" style="padding-top:10px;margin-bottom:0px;">
				<?php $this->_compileInclude('menu'); ?>
			</div>
			<div class="col-xs-10" id="datacontent">
<?php } ?>
				<div class="box itembox" style="margin-bottom:0px;border-bottom:1px solid #CCCCCC;">
					<div class="col-xs-12">
						<ol class="breadcrumb">
							<li><a href="index.php?<?php echo $this->tpl_var['_app']; ?>-master"><?php echo $this->tpl_var['apps'][$this->tpl_var['_app']]['appname']; ?></a></li>
							<li><a href="index.php?<?php echo $this->tpl_var['_app']; ?>-master-attachtype">文件类型</a></li>
							<li class="active">修改文件类型</li>
						</ol>
					</div>
				</div>
				<div class="box itembox" style="padding-top:10px;margin-bottom:0px;">
					<h4 class="title" style="padding:10px;">
						修改文件类型
						<a class="btn btn-primary pull-right" href="index.php?<?php echo $this->tpl_var['_app']; ?>-master-attachtype">文件类型</a>
					</h4>
					<form action="index.php?<?php echo $this->tpl_var['_app']; ?>-master-attachtype-modify" method="post" class="form-horizontal">
						<div class="form-group">
							<label for="basic" class="control-label col-sm-2">文件类型</label>
							<div class="col-sm-4">
								<input class="form-control" id="basic" name="args[attachtype]" type="text" value="<?php echo $this->tpl_var['attachtype']['attachtype']; ?>" needle="needle" msg="您必须文件类型" />
							</div>
						</div>
						<div class="form-group">
							<label for="basicapi" class="control-label col-sm-2">扩展名</label>
							<div class="col-sm-9">
								<input class="form-control" id="basicapi" name="args[attachexts]" type="text" value="<?php echo $this->tpl_var['attachtype']['attachexts']; ?>" needle="needle" msg="您必须填写文件类型扩展名"/>
								<span class="help-block">多个扩展名之间用英文逗号隔开</span>
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-sm-2"></label>
							<div class="col-sm-9">
								<button class="btn btn-primary" type="submit">提交</button>
								<input type="hidden" name="page" value="<?php echo $this->tpl_var['page']; ?>"/>
								<input type="hidden" name="modifyattachtype" value="1"/>
								<input type="hidden" name="atid" value="<?php echo $this->tpl_var['attachtype']['atid']; ?>"/>
								<?php $aid = 0;
 foreach($this->tpl_var['search'] as $key => $arg){ 
 $aid++; ?>
								<input type="hidden" name="search[<?php echo $key; ?>]" value="<?php echo $arg; ?>"/>
								<?php } ?>
							</div>
						</div>
					</form>
				</div>
			</div>
<?php if(!$this->tpl_var['userhash']){ ?>
		</div>
	</div>
</div>
<?php $this->_compileInclude('footer'); ?>
</body>
</html>
<?php } ?>