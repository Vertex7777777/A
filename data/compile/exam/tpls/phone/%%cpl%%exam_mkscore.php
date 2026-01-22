<?php if(!$this->tpl_var['userhash']){ ?>
<?php $this->_compileInclude('header'); ?>
<body>
<div id="content">
	<div class="pages" id="paper">
<?php } ?>
		<header class="container-fluid" style="background-color:#337AB7;">
			<h5 class="text-center">
				<em style="font-size:2rem;" class="pull-left glyphicon glyphicon-chevron-left" onclick="javascript:$.goPrePage();"></em>
				正式考试主观题评分
				<em style="font-size:2rem;" class="pull-right glyphicon glyphicon-home" onclick="javascript:$.goPage($('#page1'));"></em>
			</h5>
		</header>
		<div class="container-fluid" id="datacontent">
			<div class="row-fluid">
				<form id="form1" name="form1" class="col-xs-12" action="index.php?exam-phone-exam-makescore">
					<?php $oid = 0; ?>
					<?php $qid = 0;
 foreach($this->tpl_var['questype'] as $key => $quest){ 
 $qid++; ?>
					<?php if($quest['questsort']){ ?>
					<?php if($this->tpl_var['sessionvars']['examsessionquestion']['questions'][$quest['questid']] || $this->tpl_var['sessionvars']['examsessionquestion']['questionrows'][$quest['questid']]){ ?>
					<?php $oid++; ?>
					<h4 class="title">
						<?php echo $this->tpl_var['ols'][$oid]; ?>、<?php echo $quest['questype']; ?><?php echo $this->tpl_var['sessionvars']['examsessionsetting']['examsetting']['questype'][$quest]['describe']; ?>
					</h4>
					<?php $tid = 0; ?>
	                <?php $qnid = 0;
 foreach($this->tpl_var['sessionvars']['examsessionquestion']['questions'][$quest['questid']] as $key => $question){ 
 $qnid++; ?>
	                <?php $tid++; ?>
		            <div style="clear:both;overflow:hidden;background:#FFFFFF;margin-top:0.5rem;padding:1rem;">
						<h4 class="title">
							第<?php echo $tid; ?>题
							<span class="pull-right">
								<a class="btn text-primary qicon"><i class="glyphicon glyphicon-heart-empty"></i></a>
								<a name="question_<?php echo $question['questionid']; ?>">
								<input id="time_<?php echo $question['questionid']; ?>" type="hidden" name="time[<?php echo $question['questionid']; ?>]"/>
							</span>
						</h4>
						<div class="choice">
							</a><?php echo html_entity_decode($this->ev->stripSlashes($question['question'])); ?>
						</div>
						<?php if(!$this->tpl_var['questype'][$quest]['questsort']){ ?>
						<?php if($question['questionselect'] && $this->tpl_var['questype'][$quest]['questchoice'] != 5){ ?>
						<div class="choice">
		                	<?php echo html_entity_decode($this->ev->stripSlashes($question['questionselect'])); ?>
		                </div>
		                <?php } ?>
		                <?php } ?>
						<div class="selector decidediv">
		                	<table class="table table-hover table-bordered">
			            		<tr class="info">
			                		<td width="30%">正确答案：</td>
			                		<td><?php echo html_entity_decode($this->ev->stripSlashes($question['questionanswer'])); ?></td>
			                	</tr>
			                	<tr>
			                		<td>您的答案：</td>
			                		<td><?php if(is_array($this->tpl_var['sessionvars']['examsessionuseranswer'][$question['questionid']])){ ?><?php echo implode('',$this->tpl_var['sessionvars']['examsessionuseranswer'][$question['questionid']]); ?><?php } else { ?><?php echo html_entity_decode($this->ev->stripSlashes($this->tpl_var['sessionvars']['examsessionuseranswer'][$question['questionid']])); ?><?php } ?></td>
			                	</tr>
			            	</table>
		                </div>
		                <div class="choice" style="line-height:2.5rem;">
	                  		<input type="text" class="col-xs-12" name="score[<?php echo $question['questionid']; ?>]" value="" maxvalue="<?php echo $this->tpl_var['sessionvars']['examsessionsetting']['examsetting']['questype'][$quest['questid']]['score']; ?>">
	                  		<span class="text-error">【请根据参考答案给出分值】提示：本题共<?php echo $this->tpl_var['sessionvars']['examsessionsetting']['examsetting']['questype'][$quest['questid']]['score']; ?>分，可输入0.5的倍数。</span>
	                  	</div>
					</div>
					<?php } ?>
					<?php $qrid = 0;
 foreach($this->tpl_var['sessionvars']['examsessionquestion']['questionrows'][$quest['questid']] as $key => $questionrow){ 
 $qrid++; ?>
	                <?php $tid++; ?>
	                <div style="clear:both;overflow:hidden;background:#FFFFFF;margin-top:0.5rem;padding:1rem;">
						<h4 class="title">第<?php echo $tid; ?>题<?php echo $did; ?>小题
							<span class="pull-right">
								<a class="btn text-primary qicon"><i class="glyphicon glyphicon-heart-empty"></i></a>
								<a name="question_<?php echo $data['questionid']; ?>"></a>
								<input id="time_<?php echo $data['questionid']; ?>" type="hidden" name="time[<?php echo $data['questionid']; ?>]"/>
							</span>
						</h4>
						<div class="choice">
							<?php echo html_entity_decode($this->ev->stripSlashes($questionrow['qrquestion'])); ?>
						</div>
						<?php $did = 0;
 foreach($questionrow['data'] as $key => $data){ 
 $did++; ?>
		                <?php $qcid++; ?>
		                <hr />
	                	<div style="padding:0rem 1.5rem;margin-top:1rem;">
							<div class="choice">
								<?php echo html_entity_decode($this->ev->stripSlashes($data['question'])); ?>
							</div>
							<div class="selector decidediv">
			                	<table class="table table-hover table-bordered">
				            		<tr class="info">
				                		<td width="30%">正确答案：</td>
				                		<td><?php echo html_entity_decode($this->ev->stripSlashes($data['questionanswer'])); ?></td>
				                	</tr>
				                	<tr>
				                		<td>您的答案：</td>
				                		<td><?php if(is_array($this->tpl_var['sessionvars']['examsessionuseranswer'][$data['questionid']])){ ?><?php echo implode('',$this->tpl_var['sessionvars']['examsessionuseranswer'][$data['questionid']]); ?><?php } else { ?><?php echo html_entity_decode($this->ev->stripSlashes($this->tpl_var['sessionvars']['examsessionuseranswer'][$data['questionid']])); ?><?php } ?></td>
				                	</tr>
				            	</table>
			                </div>
			                <div class="choice" style="line-height:2.5rem;">
		                  		<input type="text" class="col-xs-12" name="score[<?php echo $data['questionid']; ?>]" value="" maxvalue="<?php echo $this->tpl_var['sessionvars']['examsessionsetting']['examsetting']['questype'][$quest['questid']]['score']; ?>">
		                  		<span class="text-error">【请根据参考答案给出分值】提示：本题共<?php echo $this->tpl_var['sessionvars']['examsessionsetting']['examsetting']['questype'][$quest['questid']]['score']; ?>分，可输入0.5的倍数。</span>
		                  	</div>
						</div>
						<?php } ?>
					</div>
					<?php } ?>
					<?php } ?>
					<?php } ?>
					<?php } ?>
					<h3 class="text-center">
						 <button type="submit" class="btn btn-primary">自行判分完毕，提交分数</button>
						 <input type="hidden" name="makescore" value="1"/>
					</h3>
				</form>
			</div>
		</div>
		<?php $this->_compileInclude('footer'); ?>
	<?php if(!$this->tpl_var['userhash']){ ?>
    </div>
</div>
</body>
</html>
<?php } ?>