<?php $this->_compileInclude('header'); ?>

<body>
<?php $this->_compileInclude('nav'); ?>

<style>
    .ai-box {
        border: 1px solid #ddd;
        background: #fdfdfd;
        border-radius: 4px;
        padding: 20px;
        margin-top: 20px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        clear: both; /* 确保换行显示 */
    }
    .ai-title {
        font-size: 16px;
        font-weight: bold;
        color: #337ab7; /* 契合 LNMP 系统蓝 */
        margin-bottom: 15px;
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
    }
    .ai-content {
        line-height: 1.8;
        color: #444;
        font-size: 14px;
    }
    .ai-loading {
        color: #888;
        text-align: center;
        padding: 10px;
    }
    /* 简单的闪烁动画 */
    .typing-dot {
        animation: blink 1s infinite;
    }
    @keyframes blink { 50% { opacity: 0; } }
</style>
<div class="container-fluid">
	<div class="row-fluid">
		<div class="main">
			<div class="box itembox" style="margin-bottom:0px;">
				<div class="col-xs-12">
					<ol class="breadcrumb">
					  <li><a href="index.php">首页</a></li>
					  <li><a href="index.php?exam-app">考试</a></li>
					  <li><a href="index.php?exam-app-basics"><?php echo $this->tpl_var['data']['currentbasic']['basic']; ?></a></li>
					  <li><a href="index.php?exam-app-exam">正式考试</a></li>
					  <li class="active">成绩单</li>
					</ol>
				</div>
			</div>
			<div class="box itembox">
				<legend class="text-center"><h3><?php echo $this->tpl_var['sessionvars']['examsession']; ?></h3></legend>
				<?php if($this->tpl_var['data']['currentbasic']['basicexam']['notviewscore']){ ?>
				<div class="col-xs-12 alert alert-info">
					<p>您已经成功交卷，请等待教师统计并公布分数。</p>
				</div>
				<?php } else { ?>
				<div class="col-xs-4">
            		<div class="boardscore">
            			<h1 class="text-center text-danger"><?php echo $this->tpl_var['sessionvars']['examsessionscore']; ?> 分</h1>
            			<p>分数评价</p>
            		</div>
            	</div>
            	<div class="col-xs-8">
            		<div><b class="text-info">考试详情：</b></div>
            			<p>总分：<b class="text-warning"><?php echo $this->tpl_var['sessionvars']['examsessionsetting']['examsetting']['score']; ?></b>分 合格分数线：<b class="text-warning"><?php echo $this->tpl_var['sessionvars']['examsessionsetting']['examsetting']['passscore']; ?></b>分 答卷耗时：<b class="text-warning"><?php if($this->tpl_var['sessionvars']['examsessiontime'] >= 60){ ?><?php if($this->tpl_var['sessionvars']['examsessiontime']%60){ ?><?php echo intval($this->tpl_var['sessionvars']['examsessiontime']/60)+1; ?><?php } else { ?><?php echo intval($this->tpl_var['sessionvars']['examsessiontime']/60); ?><?php } ?>分钟<?php } else { ?><?php echo $this->tpl_var['sessionvars']['examsessiontime']; ?>秒<?php } ?></b></p>
              		<table class="table table-hover table-bordered">
                      <tr class="success">
                        <th>题型</th>
                        <th>总题数</th>
                        <th>答对题数</th>
                        <th>总分</th>
                        <th>得分</th>
                      </tr>
                      <?php $nid = 0;
 foreach($this->tpl_var['number'] as $key => $num){ 
 $nid++; ?>
                      <?php if($num){ ?>
                      <tr>
                        <td><?php echo $this->tpl_var['questype'][$key]['questype']; ?></td>
                        <td><?php echo $num; ?></td>
                        <td><?php echo $this->tpl_var['right'][$key]; ?></td>
                        <td><?php echo number_format($num*$this->tpl_var['sessionvars']['examsessionsetting']['examsetting']['questype'][$key]['score'],1); ?></td>
                        <td><?php echo number_format($this->tpl_var['score'][$key],1); ?></td>
                      </tr>
                      <?php } ?>
                      <?php } ?>
                      <tr>
                        <td colspan="5" align="left">本次考试共<b class="text-warning"><?php echo $this->tpl_var['allnumber']; ?></b>道题，总分<b class="text-warning"><?php echo $this->tpl_var['sessionvars']['examsessionsetting']['examsetting']['score']; ?></b>分，您做对<b class="text-warning"><?php echo $this->tpl_var['allright']; ?></b>道题，得到<b class="text-warning"><?php echo $this->tpl_var['sessionvars']['examsessionscore']; ?></b>分</td>
                      </tr>
                   </table>
                   <?php if($this->tpl_var['data']['currentbasic']['basicexam']['model'] != 2){ ?>
                   <div class="text-center"><a href="index.php?exam-app-history-view&ehid=<?php echo $this->tpl_var['ehid']; ?>" class="btn btn-info">查看答案和解析</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="index.php?exam-app-history&ehtype=2" class="btn btn-info">进入我的考试记录</a></div>
            	   <?php } ?>
            	</div>

                <div class="col-xs-12">
                    <div class="ai-box">
                        <div class="ai-title">
                            <span class="glyphicon glyphicon-tasks"></span> 
                            企业安全考评 · AI 智能诊断
                        </div>
                        <div id="ai-result-area" class="ai-content">
                            <div class="ai-loading">
                                <span class="glyphicon glyphicon-refresh" style="animation: spin 2s linear infinite;"></span>
                                正在分析您的安全考核试卷，生成整改建议中<span class="typing-dot">...</span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
		</div>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function(){
    // 获取当前页面URL中的 ehid 参数
    // PHPEMS 的模板变量可以直接输出，这里为了保险使用 JS 提取
    var ehid = "<?php echo $this->tpl_var['ehid']; ?>"; 
    
    // 如果模板没解析出来，尝试从 URL 获取
    if(!ehid) {
        var urlParams = new URLSearchParams(window.location.search);
        ehid = urlParams.get('ehid');
    }

    if(ehid){
        $.ajax({
            url: "index.php?exam-app-exam-getanalysis&ehid=" + ehid,
            type: "GET",
            dataType: "json",
            success: function(res) {
                if(res.status == 'success'){
                    // 打字机效果（可选，提升高级感）
                    $("#ai-result-area").html(res.data).hide().fadeIn(500);
                } else {
                    $("#ai-result-area").html("<span style='color:red;'>分析获取失败：" + res.msg + "</span>");
                }
            },
            error: function() {
                $("#ai-result-area").html("<span style='color:red;'>网络连接超时，请检查服务器网络。</span>");
            }
        });
    }
});
</script>
<?php $this->_compileInclude('footer'); ?>
</body>
</html>